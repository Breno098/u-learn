<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        liveEvents: Object,
        errors: Object,
        query: Object,
        types: Object,
        responsible: Array,

        canLiveEventStore: Boolean,
        canLiveEventEdit: Boolean,
        canLiveEventDestroy: Boolean,
    });

    const columns = [{
        name: 'name',
        align: 'left',
        label: 'Nome',
        field: 'name',
        style: 'width: 30%',
    }, {
        name: 'type',
        align: 'left',
        label: 'Tipo',
        field: liveEvent => props.types[liveEvent.type],
        style: 'width: 10%',
    }, {
        name: 'responsible_name',
        align: 'left',
        label: 'Professor',
        field: liveEvent => liveEvent.responsible.name,
        style: 'width: 20%',
    }, {
        name: 'start_at',
        align: 'left',
        label: 'Início',
        field: 'start_at',
        style: 'width: 20%',
    }, {
        name: 'end_at',
        align: 'left',
        label: 'Término',
        field: 'end_at',
        style: 'width: 20%',
    }, {
        name: 'actions',
        label: 'Ação',
    }];

    const requestData = useForm({
        orderBy: props.query.orderBy,
        sort: props.query.sort,
        page: props.query.page,
        rowsPerPage: props.query.rowsPerPage,
        filters: {
            name: props.query.filters.name,
            type: props.query.filters.type,
            start_at: props.query.filters.start_at,
            end_at: props.query.filters.end_at,
            responsible_id: props.query.filters.responsible_id ? parseInt(props.query.filters.responsible_id) : null,
        },
    });

    const sortBy = (field) => {
        if (requestData.orderBy === field) {
            requestData.sort = requestData.sort == 'desc' ? 'asc' : 'desc';
        } else {
            requestData.sort == 'asc';
        }

        requestData.orderBy = field;
        requestData.page = 1;

        submit();
    }

    const removeFilter = (filterName) => {
        requestData.filters[filterName] = null;
        submit();
    }

    const submit = () => {
        requestData.get(route('admin.live-event.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const create = () => useForm().get(route('admin.live-event.create'));

    const edit = (id) => useForm().get(route('admin.live-event.edit', id));

    const show = (id) => useForm().get(route('admin.live-event.show', id));

    function destroy(id) {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir evento',
                message: 'Tem certeza que deseja excluir esse evento?',
            },
        }).onOk(() => {
            useForm().delete(route('admin.live-event.destroy', id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Evento excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    function destroySelected() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir selecionados',
                message: 'Tem certeza que deseja excluir eventos selecionadas?',
            },
        }).onOk(() => {
            useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.live-event.destroy-multiples'), {
                onSuccess: () => {
                    selected.value = [];

                    $q.notify({
                        type: 'positive',
                        message: 'Eventos excluídas com sucesso',
                        position: 'top',
                    })
                }
            })
      });
    }

    const selected = ref([])

    const optionsResponsible = ref(props.responsible)

    const filterResponsible = (val, update, abort) => {
        update(() => optionsResponsible.value = props.responsible.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const countAppliedFilters = computed(() => Object.values(props.query.filters).filter(fil => fil).length);

    const showFilters = ref(false)

    const optionsTypes = computed(() => {
        let options = new Array();

        for (const key in props.types) {
            options.push({ label: props.types[key], value: key })
        }

        return options;
    })

    const clearFilters = () => useForm().get(route('admin.live-event.index'))

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Eventos ao vivo" />

        <div class="row q-pb-xl">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Eventos ao vivo </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Eventos ao vivo" class="text-primary" />
                </q-breadcrumbs>
            </div>

             <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    v-if="selected.length > 0 && canLiveEventDestroy"
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroySelected"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir selecionados
                    </div>
                </q-btn>

                <q-btn
                    v-if="canLiveEventStore"
                    rounded
                    no-caps
                    color="primary"
                    @click="create"
                >
                    <q-icon name="add" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Novo evento ao vivo
                    </div>
                </q-btn>
            </div>
        </div>

        <q-card flat class="adm-br-16">
            <q-card-section class="row items-center q-py-sm q-px-lg">
                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.title"
                    :label="`Nome = ${query.filters.title}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('title')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.type"
                    :label="`Tipo = ${types[query.filters.type]}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('type')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.responsible_id"
                    :label="`Professor = ${responsible.filter(t => t.id == query.filters.responsible_id).shift().name}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('responsible_id')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.start_at"
                    :label="`Inicio = ${query.filters.start_at}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('start_at')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.end_at"
                    :label="`Termina = ${query.filters.end_at}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('end_at')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-space/>

                <q-btn round dense flat color="primary">
                    <q-badge
                        color="primary"
                        floating
                        rounded
                        :label="countAppliedFilters"
                        v-if="countAppliedFilters"
                    />

                    <q-icon name="tune" class="rotate-90"/>

                    <q-menu
                        style="min-width: 500px"
                        max-width='500px'
                        class="bg-white q-pa-md adm-br-16"
                        v-model="showFilters"
                    >
                        <div class="row q-col-gutter-sm">
                            <div class="col-12">
                                <q-input
                                    outlined
                                    v-model="requestData.filters.name"
                                    label="Nome do evento ao vivo"
                                />
                            </div>

                            <div class="col-12">
                                <q-select
                                    :options="optionsTypes"
                                    emit-value
                                    map-options
                                    outlined
                                    v-model="requestData.filters.type"
                                    label="Tipo"
                                />
                            </div>

                            <div class="col-12">
                                <q-select
                                    :options="optionsResponsible"
                                    option-value="id"
                                    option-label="name"
                                    emit-value
                                    map-options
                                    outlined
                                    v-model="requestData.filters.responsible_id"
                                    label="Professor"
                                    clearable
                                    use-input
                                    @filter="filterResponsible"
                                >
                                    <template v-slot:selected-item="scope">
                                        <q-chip
                                            class="adm-chip-primary q-my-none"
                                            :label="scope.opt.name"
                                            dense
                                        >
                                            <q-icon
                                                name="cancel"
                                                size="xs"
                                                @click="scope.removeAtIndex(scope.index)"
                                                class="q-ml-xs cursor-pointer"
                                            />
                                        </q-chip>
                                    </template>
                                </q-select>
                            </div>

                            <div class="col-12">
                                <q-input
                                    outlined
                                    v-model="requestData.filters.start_at"
                                    mask="##/##/#### ##:##"
                                    label="Data e hora de inicio"
                                    placeholder="dia/mês/ano hora:min"
                                    :bottom-slots="Boolean(errors['filters.start_at'])"
                                    clearable
                                >
                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.start_at'] }} </div>
                                    </template>

                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today" />
                                    </template>

                                    <q-popup-proxy class="row">
                                        <q-date
                                            v-model="requestData.filters.start_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            flat
                                            square
                                        />

                                        <q-time
                                            v-model="requestData.filters.start_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            format24h
                                            flat
                                            square
                                        >
                                            <div class="row items-center justify-end">
                                                <q-btn
                                                    label="Ok"
                                                    color="primary"
                                                    flat
                                                    v-close-popup
                                                />
                                            </div>
                                        </q-time>
                                    </q-popup-proxy>
                                </q-input>
                            </div>

                            <div class="col-12">
                                <q-input
                                    outlined
                                    v-model="requestData.filters.end_at"
                                    mask="##/##/#### ##:##"
                                    label="Data e hora de término"
                                    :bottom-slots="Boolean(errors['filters.end_at'])"
                                    placeholder="dia/mês/ano hora:min"
                                    clearable
                                >
                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.end_at'] }} </div>
                                    </template>

                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today" />
                                    </template>

                                    <q-popup-proxy class="row">
                                        <q-date
                                            v-model="requestData.filters.end_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            flat
                                            square
                                        />

                                        <q-time
                                            v-model="requestData.filters.end_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            format24h
                                            flat
                                            square
                                        >
                                            <div class="row items-center justify-end">
                                                <q-btn
                                                    label="Ok"
                                                    color="primary"
                                                    flat
                                                    v-close-popup
                                                />
                                            </div>
                                        </q-time>
                                    </q-popup-proxy>
                                </q-input>
                            </div>
                        </div>

                        <div class="flex flex-center q-pt-lg q-pa-md q-gutter-sm">
                            <q-btn
                                color="primary"
                                rounded
                                no-caps
                                @click="submit"
                            >
                                <q-icon name="check" size="xs"/>

                                <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                    Filtrar
                                </div>
                            </q-btn>

                            <q-btn
                                    color="primary"
                                    rounded
                                    outline
                                    no-caps
                                    @click="clearFilters"
                            >
                                <q-icon name="close" size="xs"/>

                                <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20 m-4">
                                    Limpar
                                </div>
                            </q-btn>
                        </div>
                    </q-menu>
                </q-btn>
            </q-card-section>

            <q-card-section class="q-py-none">
                <q-separator color="grey-3"/>
            </q-card-section>

            <q-card-section class="q-py-none">
                <q-table
                    :rows="liveEvents.data"
                    :columns="columns"
                    flat
                    class="text-grey-8"
                    v-model:selected="selected"
                    selection="multiple"
                    hide-pagination
                    :pagination.sync="{rowsPerPage: requestData.rowsPerPage}"
                >
                    <template v-slot:header-selection="scope">
                        <q-checkbox v-model="scope.selected" />
                    </template>

                    <template v-slot:body-selection="scope">
                        <q-checkbox
                            :model-value="scope.selected"
                            @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }"
                        />
                    </template>

                    <template v-slot:header-cell="props">
                        <q-th :props="props">
                            <div class="adm-fw-700 adm-fs-16 cursor-pointer" @click="sortBy(props.col.name)">
                                {{ props.col.label }}

                                <q-icon
                                    :name="query.sort == 'desc' ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                                    v-if="props.col.name === query.orderBy"
                                />
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:header-cell-actions="props">
                        <q-th :props="props" auto-width>
                            <div class="row items-center justify-center adm-fw-700 adm-fs-16">
                                {{  props.col.label  }}
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:body-cell-responsible_name="props">
                        <q-td :props="props">
                            <q-chip class="adm-chip-primary">
                                {{ props.row.responsible.name }}
                            </q-chip>
                        </q-td>
                    </template>

                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props">
                            <div class="row items-center justify-center adm-fw-700 adm-fs-16">
                                <q-btn icon="more_vert" flat v-if="canLiveEventEdit || canLiveEventDestroy">
                                    <q-menu :offset="[65, 0]">
                                        <q-list>
                                            <q-item
                                                v-if="canLiveEventEdit"
                                                clickable
                                                @click="edit(props.row.id)"
                                                class="text-grey-7 flex items-center"
                                            >
                                                <q-icon name="edit" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Editar </div>
                                                </q-item-section>
                                            </q-item>

                                            <q-separator/>

                                            <q-item
                                                clickable
                                                @click="show(props.row.id)"
                                                class="text-grey-7 flex items-center"
                                            >
                                                <q-icon name="visibility" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Vizualizar </div>
                                                </q-item-section>
                                            </q-item>

                                            <q-separator/>

                                            <q-item
                                                v-if="canLiveEventDestroy"
                                                clickable
                                                @click="destroy(props.row.id)"
                                                class="text-grey-7 flex flex-center"
                                            >
                                                <q-icon name="close" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Excluir </div>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </q-menu>
                                </q-btn>

                                <q-btn
                                    v-else
                                    @click="show(props.row.id)"
                                    class="text-grey-7 flex flex-center"
                                    flat
                                    no-caps
                                >
                                    <q-icon name="visibility" size="xs"/>
                                    <div class="q-ml-sm"> Visualizar </div>
                                </q-btn>
                            </div>
                        </q-td>
                    </template>
                </q-table>

                <div class="row items-center text-grey">
                    <q-space/>

                    <div class="row items-center text-grey">
                        Resultado por página

                        <q-select
                            :options="[5, 10, 15]"
                            v-model="requestData.rowsPerPage"
                            borderless
                            class="q-ml-md"
                            @update:model-value="submit"
                        />
                    </div>

                    <q-pagination
                        v-model="requestData.page"
                        :max="liveEvents.meta.last_page"
                        @update:model-value="submit"
                        direction-links
                        boundary-links
                        color="grey"
                        input
                        icon-first="keyboard_double_arrow_left"
                        icon-last="keyboard_double_arrow_right"
                    />
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
