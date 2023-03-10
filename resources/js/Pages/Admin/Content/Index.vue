<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';
    import SummaryContents from '@/Components/Content/SummaryContents.vue';

    const $q = useQuasar()

    const props = defineProps({
        contents: Object,
        errors: Object,
        query: Object,

        contentReleasedThisMonth: Object,
        contentsMostViewed: Object,
        contentsExpiresIn60Days: Object,

        canContentStore: Boolean,
        canContentEdit: Boolean,
        canContentDestroy: Boolean,
    });

    const columns = [{
        name: 'name',
        align: 'left',
        label: 'Nome',
        field: 'name',
        style: 'width: 25%',
    }, {
        name: 'category_name',
        align: 'left',
        label: 'Categoria',
        field: content => content.category.name,
        style: 'width: 10%',
    }, {
        name: 'sections_name',
        align: 'left',
        label: 'Seções',
        field: content => content.sections.map(s => s.name).join(', '),
        style: 'width: 35%',
    }, {
        name: 'launch_start_at',
        align: 'left',
        label: 'Lançamento',
        field: 'launch_start_at',
        style: 'width: 15%',
    }, {
        name: 'end_at',
        align: 'left',
        label: 'Encerramento',
        field: 'end_at',
        style: 'width: 15%',
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
            launch_start_at: props.query.filters.launch_start_at,
            launch_end_at: props.query.filters.launch_end_at,
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
        requestData.get(route('admin.content.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const create = () => useForm().get(route('admin.content.create'));

    const edit = (id) => useForm().get(route('admin.content.edit', id));

    const show = (id) => useForm().get(route('admin.content.show', id));

    const exportExcel = () => {
        axios.get(route('admin.content.export', requestData), {
            responseType: "blob"
        })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'conteudos.xlsx');
            link.click();
        })
        .catch(response => {
            $q.notify({
                type: 'negative',
                message: 'Erro ao fazer a exportação',
                position: 'center',
            })
        });
    }

    const destroy = (id) => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir conteúdo',
                message: 'Tem certeza que deseja excluir essa conteúdo?',
            },
        }).onOk(() => {
            useForm().delete(route('admin.content.destroy', id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Conteúdo excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    const destroySelected = () => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir selecionados',
                message: 'Tem certeza que deseja excluir conteúdos selecionadas?',
            },
      }).onOk(() => {
        useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.content.destroy-multiples'), {
            onSuccess: () => {
                selected.value = [];

                $q.notify({
                    type: 'positive',
                    message: 'Conteúdo excluídas com sucesso',
                    position: 'top',
                })
            }
        })
      });
    }

    const selected = ref([])

    const countAppliedFilters = computed(() => Object.values(props.query.filters).filter(fil => fil).length);

    const showFilters = ref(false)

    const clearFilters = () => useForm().get(route('admin.content.index'))

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Conteúdos" />

        <div class="row q-pb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Conteúdos </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Conteúdos" class="text-primary" />
                </q-breadcrumbs>
            </div>

             <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    v-if="selected.length > 0"
                    @click="destroySelected"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir selecionados
                    </div>
                </q-btn>

                 <q-btn
                    color="primary"
                    rounded
                    no-caps
                    @click="create"
                >
                    <q-icon name="add" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Novo conteúdo
                    </div>
                </q-btn>
            </div>
        </div>

        <SummaryContents
            :contentReleasedThisMonth="contentReleasedThisMonth"
            :contentsMostViewed="contentsMostViewed"
            :contentsExpiresIn60Days="contentsExpiresIn60Days"
        />

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
                    v-if="query.filters.launch_start_at"
                    :label="`Inicio = ${query.filters.launch_start_at}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('launch_start_at')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.launch_end_at"
                    :label="`Termina = ${query.filters.launch_end_at}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('launch_end_at')"
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
                                    label="Nome do conteúdo"
                                />
                            </div>

                            <div class="col-12">
                                <q-input
                                    outlined
                                    v-model="requestData.filters.launch_start_at"
                                    mask="##/##/#### ##:##"
                                    label="Data e hora de inicio"
                                    placeholder="dia/mês/ano hora:min"
                                    :bottom-slots="Boolean(errors['filters.launch_start_at'])"
                                    clearable
                                >
                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.launch_start_at'] }} </div>
                                    </template>

                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today" />
                                    </template>

                                    <q-popup-proxy class="row">
                                        <q-date
                                            v-model="requestData.filters.launch_start_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            flat
                                            square
                                        />

                                        <q-time
                                            v-model="requestData.filters.launch_start_at"
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
                                    v-model="requestData.filters.launch_end_at"
                                    mask="##/##/#### ##:##"
                                    label="Data e hora de término"
                                    :bottom-slots="Boolean(errors['filters.launch_end_at'])"
                                    placeholder="dia/mês/ano hora:min"
                                    clearable
                                >
                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.launch_end_at'] }} </div>
                                    </template>

                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today" />
                                    </template>

                                    <q-popup-proxy class="row">
                                        <q-date
                                            v-model="requestData.filters.launch_end_at"
                                            mask="DD/MM/YYYY HH:mm"
                                            flat
                                            square
                                        />

                                        <q-time
                                            v-model="requestData.filters.launch_end_at"
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
                    :rows="contents.data"
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

                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props">
                            <div class="row items-center justify-center adm-fw-700 adm-fs-16">
                                <q-btn icon="more_vert" flat v-if="canContentEdit || canContentDestroy">
                                    <q-menu :offset="[65, 0]">
                                        <q-list>
                                            <q-item
                                                v-if="canContentEdit"
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
                                                v-if="canContentDestroy"
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
                                    class="text-grey-7 flex flex-center text-no-wrap"
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
                    <q-btn
                        color="primary"
                        no-caps
                        outline
                        rounded
                        flat
                        @click="exportExcel()"
                    >
                        <q-icon name="save_alt" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Exportar excel
                        </div>
                    </q-btn>

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
                        :max="contents.meta.last_page"
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
