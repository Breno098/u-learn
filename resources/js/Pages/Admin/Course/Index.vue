<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        courses: Object,
        errors: Object,
        query: Object,

        canCourseStore: Boolean,
        canCourseEdit: Boolean,
        canCourseDestroy: Boolean,
    });

    const columns = [{
        name: 'name',
        align: 'left',
        label: 'Nome',
        field: 'name',
        style: 'width: 50%',
    }, {
        name: 'category_name',
        align: 'left',
        label: 'Categoria',
        field: course => course.category.name,
        style: 'width: 50%',
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
        requestData.get(route('admin.course.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const create = () => useForm().get(route('admin.course.create'));

    const edit = (id) => useForm().get(route('admin.course.edit', id));

    const destroy = (id) => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir curso',
                message: 'Tem certeza que deseja excluir essa curso?',
                confirm: true,
                icon: { name: 'close', color: 'red' },
            },
        }).onOk(() => {
            useForm().delete(route('admin.course.destroy', id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: 'Curso excluído com sucesso',
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })
                }
            })
        });
    }

    const destroySelected = () => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir selecionados',
                message: 'Tem certeza que deseja excluir cursos selecionadas?',
                confirm: true,
                icon: { name: 'playlist_remove', color: 'red' }
            },
      }).onOk(() => {
        useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.course.destroy-multiples'), {
            onSuccess: () => {
                selected.value = [];

                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Cursos excluídos com sucesso',
                        icon: { name: 'playlist_add_check', color: 'green' },
                        timeout: 3000
                    }
                })
            }
        })
      });
    }

    const selected = ref([])

    const showFilters = ref(false)

    const clearFilters = () => useForm().get(route('admin.course.index'))
</script>

<template>
    <Head title="Cursos" />

    <AuthenticatedLayout>
        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_school" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md"> Cursos </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
                    <q-btn
                        color="red"
                        class="q-mr-md"
                        no-caps
                        outline
                        v-if="selected.length > 0"
                        @click="destroySelected"
                        icon="playlist_remove"
                        label="Excluir selecionados"
                    />

                    <q-btn
                        color="indigo"
                        no-caps
                        @click="create"
                        icon="add"
                        label="Novo curso"
                    />
                </div>
            </q-card-section>
        </q-card>

        <q-dialog v-model="showFilters" persistent>
            <q-card flat style="width: 900px; max-width: 90vw;">
                <q-card-section class="row items-center q-px-lg">
                    <q-icon name="filter_list" color="indigo" size="sm"/>

                    <div class="text-h6 q-ml-md text-blue-grey-10">
                        Filtros
                    </div>

                    <q-space/>

                    <q-btn
                        icon="close"
                        flat
                        dense
                        v-close-popup
                        color="indigo"
                    />
                </q-card-section>

                <q-card-section class="row items-center q-px-lg q-col-gutter-sm">
                    <div class="col-12">
                        <q-input
                            outlined
                            v-model="requestData.filters.name"
                            label="Nome do curso"
                            color="indigo"
                            @keydown.enter.prevent="submit"
                        />
                    </div>

                    <div class="col-12 flex items-center justify-end q-pt-lg q-gutter-sm">
                        <q-btn
                            color="indigo"
                            no-caps
                            @click="submit"
                            icon="search"
                            label="Filtrar"
                        />

                        <q-btn
                            color="indigo"
                            no-caps
                            outline
                            @click="clearFilters"
                            icon="clear_all"
                            label="Limpar"
                        />
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-card flat>
            <q-card-section class="q-pb-none q-pt-lg">
                <q-btn
                    dense
                    color="indigo"
                    class="absolute inset-shadow-down"
                    @click="showFilters = !showFilters"
                    icon="filter_list"
                    style="top: 0; left: 12px; transform: translateY(-50%);"
                />

                <q-chip
                    color="indigo"
                    text-color="white"
                    v-if="query.filters.name"
                    :label="`Nome = ${query.filters.name}`"
                    square
                >
                    <q-icon
                        name="disabled_by_default"
                        size="xs"
                        @click="removeFilter('name')"
                        class="q-ml-sm cursor-pointer"
                    />
                </q-chip>

                <q-table
                    :rows="courses.data"
                    :columns="columns"
                    flat
                    class="text-blue-grey-10"
                    v-model:selected="selected"
                    selection="multiple"
                    hide-pagination
                    :pagination.sync="{rowsPerPage: requestData.rowsPerPage}"
                >
                    <template v-slot:header-selection="scope">
                        <q-checkbox
                            v-model="scope.selected"
                            color="indigo"
                            checked-icon="task_alt"
                            unchecked-icon="radio_button_unchecked"
                            indeterminate-icon="remove_circle_outline"
                        />
                    </template>

                    <template v-slot:body-selection="scope">
                        <q-checkbox
                            :model-value="scope.selected"
                            @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }"
                            color="indigo"
                            checked-icon="task_alt"
                            unchecked-icon="radio_button_unchecked"
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
                                <q-btn icon="more_vert" flat v-if="canCourseEdit || canCourseDestroy">
                                    <q-menu :offset="[45, 0]">
                                        <q-list>
                                            <q-item
                                                v-if="canCourseEdit"
                                                clickable
                                                @click="edit(props.row.id)"
                                                class="text-blue-grey-10 flex items-center"
                                            >
                                                <q-icon name="edit" size="xs" color="indigo" />

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Editar </div>
                                                </q-item-section>
                                            </q-item>

                                            <q-separator/>

                                            <q-item
                                                v-if="canCourseDestroy"
                                                clickable
                                                @click="destroy(props.row.id)"
                                                class="text-blue-grey-10 flex flex-center"
                                            >
                                                <q-icon name="close" size="xs" color="red"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Excluir </div>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </q-menu>
                                </q-btn>
                            </div>
                        </q-td>
                    </template>
                </q-table>

                <div class="row items-center text-blue-grey-10">
                    <q-space/>

                    <div class="row items-center text-blue-grey-10">
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
                        :max="courses.meta.last_page"
                        @update:model-value="submit"
                        direction-links
                        boundary-links
                        color="indigo"
                        input
                        icon-first="keyboard_double_arrow_left"
                        icon-last="keyboard_double_arrow_right"
                    />
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
