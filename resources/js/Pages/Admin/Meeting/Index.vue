<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        meetings: Object,
        errors: Object,
        query: Object,
        types: Object,
        teachers: Array,

        canMeetingStore: Boolean,
        canMeetingEdit: Boolean,
        canMeetingDestroy: Boolean,
    });

    const columns = [{
        name: 'name',
        align: 'left',
        label: 'Nome do encontro',
        field: 'name',
        style: 'width: 30%',
    }, {
        name: 'type',
        align: 'left',
        label: 'Tipo',
        field: 'type',
        style: 'width: 10%',
    }, {
        name: 'teacher_name',
        align: 'left',
        label: 'Professor',
        field: meeting => meeting.teacher.name,
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
        label: 'Ação'
    }];

    const requestData = useForm({
        orderBy: props.query.orderBy,
        sort: props.query.sort,
        page: props.query.page,
        rowsPerPage: props.query.rowsPerPage,
        filters: {
            name: props.query.filters.name,
            type: props.query.filters.type,
            teacher_id: props.query.filters.teacher_id ? parseInt(props.query.filters.teacher_id) : null,
            start_at: props.query.filters.start_at,
            end_at: props.query.filters.end_at
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
        requestData.get(route('admin.meeting.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const create = () => useForm().get(route('admin.meeting.create'));

    const edit = (id) => useForm().get(route('admin.meeting.edit', id));

    const show = (id) => useForm().get(route('admin.meeting.show', id));

    function destroy(id) {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir encontro',
                message: 'Tem certeza que deseja excluir esse encontro?',
            },
        }).onOk(() => {
            useForm().delete(route('admin.meeting.destroy', id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Encontro excluído com sucesso',
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
                message: 'Tem certeza que deseja excluir encontros selecionados?',
            },
      }).onOk(() => {
        useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.meeting.destroy-multiples'), {
            onSuccess: () => {
                selected.value = [];

                $q.notify({
                    type: 'positive',
                    message: 'Encontros excluídos com sucesso',
                    position: 'top',
                })
            }
        })
      });
    }

    const selected = ref([])

    const countAppliedFilters = computed(() => Object.values(props.query.filters).filter(fil => fil).length);

    const showFilters = ref(false)

    const optionsTeachers = ref(props.teachers)

    const filterTeachers = (val, update, abort) => {
        update(() => optionsTeachers.value = props.teachers.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsTypes = computed(() => {
        let options = new Array();

        for (const key in props.types) {
            options.push({ label: props.types[key], value: key })
        }

        return options;
    })

    const getTypeLabel = (index) => props.types[index];

    const getTypeIcon = (index) => {
        switch (index) {
            case 'agendamento': return 'o_calendar_today';
            case 'grupo': return 'sym_o_group';
            case 'extra': return 'info';
            case 'imersao': return 'o_headset';
            case 'individual': return 'person_outline';
            default: return ''
        }
    }

    const clearFilters = () => useForm().get(route('admin.meeting.index'))

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Encontros" />

        <div class="row q-pb-xl">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Encontros </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Encontros" class="text-primary" />
                </q-breadcrumbs>
            </div>

             <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    v-if="selected.length > 0 && canMeetingDestroy"
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
                    v-if="canMeetingStore"
                    color="primary"
                    rounded
                    no-caps
                    @click="create"
                >
                    <q-icon name="add" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Novo encontro
                    </div>
                </q-btn>
            </div>
        </div>

        <q-card flat class="adm-br-16">
            <q-card-section class="row items-center q-py-sm q-px-lg">
                 <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.name"
                    :label="`Nome = ${query.filters.name}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('name')"
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
                    v-if="query.filters.teacher_id"
                    :label="`Professor = ${teachers.filter(t => t.id == query.filters.teacher_id).shift().name}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('teacher_id')"
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
                                    label="Nome do encontro"
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
                                    :options="optionsTeachers"
                                    option-value="id"
                                    option-label="name"
                                    emit-value
                                    map-options
                                    outlined
                                    v-model="requestData.filters.teacher_id"
                                    label="Professor"
                                    clearable
                                    use-input
                                    @filter="filterTeachers"
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
                                    :bottom-slots="Boolean(errors['filters.start_at'])"
                                    clearable
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today"/>
                                    </template>

                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.start_at'] }} </div>
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
                                    clearable
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today"/>
                                    </template>

                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.end_at'] }} </div>
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
                    :rows="meetings.data"
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
                            <div class="flex flex-center adm-fw-700 adm-fs-16">
                                {{  props.col.label  }}
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:body-cell-type="props">
                        <q-td :props="props">
                            <q-icon
                                :name="getTypeIcon(props.row.type)"
                                size="sm"
                                class="q-mr-sm"
                            />

                            {{ getTypeLabel(props.row.type) }}
                        </q-td>
                    </template>

                    <template v-slot:body-cell-teacher_name="props">
                        <q-td :props="props">
                            <q-chip class="adm-chip-primary">
                                {{ props.row.teacher.name }}
                            </q-chip>
                        </q-td>
                    </template>

                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props">
                            <div class="row items-center justify-center adm-fw-700 adm-fs-16">
                                <q-btn icon="more_vert" flat v-if="canMeetingEdit || canMeetingDestroy">
                                    <q-menu :offset="[65, 0]">
                                        <q-list>
                                            <q-item
                                                v-if="canMeetingEdit"
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
                                                v-if="canMeetingDestroy"
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
                        :max="meetings.meta.last_page"
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
