<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        products: Object,
        errors: Object,
        query: Object,
    });

    const columns = [{
        name: 'image',
        align: 'left',
        label: 'Imagem',
        field: 'image',
    }, {
        name: 'name',
        align: 'left',
        label: 'Nome do produto',
        field: 'name',
        sorteable: true
    }, {
        name: 'description',
        align: 'left',
        label: 'Descrição',
        field: 'description',
        sorteable: true
    }, {
        name: 'payment_methods',
        align: 'left',
        label: 'Formas de pagamento',
        field: (product) => {
            let returnData = [];

            if (product.credit_card_accept)
                returnData.push('Cartão de crédito');

            if (product.boleto_accept)
                returnData.push('Boleto');

            if (product.pix_accept)
                returnData.push('PIX');

            return returnData.join(', ');
        },
        sorteable: true
    },{
        name: 'actions',
        align: 'center',
        label: 'Ação',
    }];

    const requestData = useForm({
        orderBy: props.query.orderBy,
        sort: props.query.sort,
        page: props.query.page,
        rowsPerPage: props.query.rowsPerPage,
        filters: {
            name: props.query.filters.name,
            start_at: props.query.filters.start_at,
            end_at: props.query.filters.end_at,
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
        requestData.get(route('admin.product.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const create = () => useForm().get(route('admin.product.create'));

    const edit = (id) => useForm().get(route('admin.product.edit', id));

    const show = (id) => useForm().get(route('admin.product.show', id));

    function destroy(id) {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir Produto',
                message: 'Tem certeza que deseja excluir essa produto?',
            },
        }).onOk(() => {
            useForm().delete(route('admin.product.destroy', id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Produto excluído com sucesso',
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
                message: 'Tem certeza que deseja excluir produtos selecionadas?',
            },
      }).onOk(() => {
        useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.product.destroy-multiples'), {
            onSuccess: () => {
                selected.value = [];

                $q.notify({
                    type: 'positive',
                    message: 'Produtos excluídos com sucesso',
                    position: 'top',
                })
            }
        })
      });
    }

    const selected = ref([])

    const countAppliedFilters = computed(() => Object.values(props.query.filters).filter(fil => fil).length);

    const showFilters = ref(false)

    const clearFilters = () => useForm().get(route('admin.product.index'))

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Produtos" />

        <div class="row q-pb-xl">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Produtos </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Produtos" class="text-primary" />
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
                        Novo produto
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
                                    label="Nome do produto"
                                />
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
                    :rows="products.data"
                    :columns="columns"
                    flat
                    class="text-grey-8"
                    v-model:selected="selected"
                    selection="multiple"
                    hide-pagination
                    :pagination.sync="{
                        rowsPerPage: requestData.rowsPerPage
                    }"
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
                        <q-th :props="props" >
                            <div class="row items-center adm-fw-700 adm-fs-16">
                                <div
                                    @click="sortBy(props.col.name)"
                                    class="cursor-pointer"
                                    v-if="props.col.sorteable"
                                >
                                    {{ props.col.label }}

                                    <q-icon
                                        name="keyboard_arrow_up"
                                        v-if="props.col.name === query.orderBy && query.sort == 'desc'"
                                    />

                                    <q-icon
                                        name="keyboard_arrow_down"
                                        v-else-if="props.col.name === query.orderBy && query.sort == 'asc'"
                                    />
                                </div>

                                <div v-else>
                                    {{  props.col.label  }}
                                </div>
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:body-cell-image="props">
                        <q-td :props="props">
                            <q-img
                                v-if="props.row.image"
                                :src="props.row.image"
                                height="56px"
                            />
                        </q-td>
                    </template>

                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props" auto-width>
                            <q-btn icon="more_vert" flat>
                                <q-menu>
                                    <q-list>
                                        <q-item
                                            clickable
                                            @click="edit(props.row.id)"
                                            class="text-grey-7 flex flex-center"
                                        >
                                            <q-icon name="edit" size="xs"/>
                                            <div class="q-ml-sm"> Editar </div>
                                        </q-item>

                                        <q-separator/>

                                        <q-item
                                            clickable
                                            @click="show(props.row.id)"
                                            class="text-grey-7 flex flex-center"
                                        >
                                            <q-icon name="visibility" size="xs"/>
                                            <div class="q-ml-sm"> Visualizar </div>
                                        </q-item>

                                        <q-separator/>

                                        <q-item
                                            clickable
                                            @click="destroy(props.row.id)"
                                            class="text-grey-7 flex flex-center"
                                        >
                                            <q-icon name="close" size="xs"/>
                                            <div class="q-ml-sm"> Excluir </div>
                                        </q-item>
                                    </q-list>
                                </q-menu>
                            </q-btn>
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
                        :max="products.meta.last_page"
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

