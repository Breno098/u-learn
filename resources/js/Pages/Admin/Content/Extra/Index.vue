<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue'
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        content: Object,
        extras: Object,
        errors: Object,
        types: Object,
        players: Object,
    });

    const tab = ref('extras')

    const goMainTab = () => useForm().get(route('admin.content.edit', props.content.id))

    const goTitlesTab = () => useForm().get(route('admin.content.titles', props.content.id))

    const destroy = (extra) => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir extra ?',
                message: `Tem certeza que deseja ${extra.name}?`,
            },
        }).onOk(() => {
            useForm().delete(route('admin.content.extra.destroy', {
                content: props.content.id,
                extra: extra.id
            }), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: `Extra ${extra.name} excluido com sucesso`,
                        position: 'top',
                    })
                }
            })
        })
    }

    /** CREATE EXTRA */
    const createModalShow = ref(false)

    const hideCreateModal = () => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Fechar janela?',
                message: 'Ao fechar esta janela, os dados serão perdidos. Tem certeza que deseja fechar?',
            },
        }).onOk(() => {
            createModalShow.value = false;
            createForm.reset();
        })
    }

    const createForm = useForm({
        name: null,
        type: null,
        player:null,
        embed: null,
    });

    const storeSubmit = () => {
        createForm.post(route("admin.content.extra.store", {
            content: props.content.id,
        }),{
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: `Extra ${createForm.name} cadastrado com sucesso`,
                    position: 'top',
                })

                createModalShow.value = false;
            },
        })
    };
    /** END CREATE EXTRA */

    /** EDIT EXTRA */
    const editModalShow = ref(false)

    const editForm = useForm({
        id: null,
        name: null,
        type: null,
        player:null,
        embed: null,
    });

    const updateSubmit = () => {
        editForm.put(route("admin.content.extra.update", {
            content: props.content.id,
            extra: editForm.id
        }),{
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: `Extra ${editForm.name} atualizado com sucesso`,
                    position: 'top',
                })

                editModalShow.value = false;
            },
        })
    };

    const edit = (extra) => {
        editModalShow.value = true;

        editForm.id = extra.id;
        editForm.name = extra.name;
        editForm.type = extra.type;
        editForm.player = extra.player;
        editForm.embed = extra.embed;
    }

    const hideEditModal = () => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Fechar janela?',
                message: 'Ao fechar esta janela, as alterações serão perdidas. Tem certeza que deseja fechar?',
            },
        }).onOk(() => {
            editModalShow.value = false;
            editForm.reset();
        })
    }
    /** END EDIT EXTRA */

    const optionsTypes = computed(() => {
        let options = new Array();

        for (const key in props.types) {
            options.push({ label: props.types[key], value: key })
        }

        return options;
    })

    const optionsPlayes = computed(() => {
        let options = new Array();

        for (const key in props.players) {
            options.push({ label: props.players[key], value: key })
        }

        return options;
    })

    const goBack = () => useForm().get(route('admin.content.index'))

</script>

<template>
    <AuthenticatedLayout>
        <Head :title="content.name" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8">
                    {{ content.name }}
                </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Conteúdos" class="text-grey"/>
                    <q-breadcrumbs-el :label="content.name" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                        color="primary"
                        class="q-mr-md"
                        rounded
                        no-caps
                        outline
                        @click="goBack"
                >
                    <q-icon name="chevron_left" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Voltar
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white adm-br-16">
            <q-tabs
                v-model="tab"
                dense
                class="text-grey adm-br-tl-16 adm-br-tr-16"
                active-color="primary"
                indicator-color="primary"
                align="justify"
                no-caps
            >
                <q-tab
                    name="main"
                    label="Dados do conteúdo"
                    class="q-py-md"
                    @click="goMainTab"
                />

                <q-tab
                    name="titles"
                    label="Títulos"
                    class="q-py-md"
                    @click="goTitlesTab"
                />

                <q-tab
                    name="extras"
                    label="Extras"
                    class="q-py-md"
                />
            </q-tabs>

            <div class="bg-white q-py-lg q-px-lg adm-br-16">
                <div v-for="extra in extras" class="row q-col-gutter-lg q-mb-md">
                    <div class="col-12 items-center q-mt-xs">
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                            Extra {{ extra.name }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsTypes"
                            outlined
                            v-model="extra.type"
                            label="Extra"
                            emit-value
                            map-options
                            disable
                        />
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="extra.name"
                            label="Nome do extra"
                            disable
                        />
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsPlayes"
                            outlined
                            v-model="extra.player"
                            label="Tipo de player"
                            emit-value
                            map-options
                            disable
                       />
                    </div>

                    <div class="col-12 col-md-5">
                        <q-input
                            outlined
                            v-model="extra.embed"
                            label="Código embed"
                            disable
                        />
                    </div>

                    <div class="col-12 col-md-1 flex justify-end items-center">
                        <q-btn-group flat>
                            <q-btn
                                color="negative"
                                @click="destroy(extra)"
                                flat
                                icon="close"
                                padding="xs"
                            />
                            <q-btn
                                color="primary"
                                @click="edit(extra)"
                                flat
                                icon="o_edit"
                                padding="xs"
                            />
                        </q-btn-group>
                    </div>
                </div>

                <div class="flex flex-center">
                    <q-btn
                        color="primary"
                        no-caps
                        flat
                        @click="createModalShow = true"
                    >
                        <q-icon name="add" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Adicionar extra
                        </div>
                    </q-btn>
                </div>
            </div>
        </div>

        <q-dialog v-model="editModalShow" persistent>
            <q-card
                style="width: 700px; max-width: 80vw;"
                class="adm-br-16 q-my-sm q-pa-sm"
            >
                <q-card-section class="row q-col-gutter-lg">
                    <div class="col-12 items-center q-mt-xs row justify-between">
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                            Editar extra
                        </div>

                        <q-btn
                            color="primary"
                            @click="hideEditModal"
                            flat
                        >
                            <q-icon name="close" size="xs"/>
                        </q-btn>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsTypes"
                            outlined
                            v-model="editForm.type"
                            label="Extra"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(editForm.errors.type)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editForm.errors.type }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="editForm.name"
                            label="Nome do extra"
                            :bottom-slots="Boolean(editForm.errors.name)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editForm.errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsPlayes"
                            outlined
                            v-model="editForm.player"
                            label="Tipo de player"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(editForm.errors.player)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editForm.errors.player }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="editForm.embed"
                            label="Código embed"
                            :bottom-slots="Boolean(editForm.errors.embed)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editForm.errors.embed }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 row justify-end">
                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            @click="hideEditModal"
                            size="sm"
                            class="q-my-md"
                            outline
                        >
                            <q-icon name="close" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cancelar
                            </div>
                        </q-btn>

                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            :disabled="editForm.processing"
                            size="sm"
                            class="q-my-md q-ml-sm"
                            @click="updateSubmit"
                        >
                            <q-icon name="check" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Atualizar
                            </div>
                        </q-btn>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-dialog v-model="createModalShow" persistent>
            <q-card
                style="width: 700px; max-width: 80vw;"
                class="adm-br-16 q-my-sm q-pa-sm"
            >
                <q-card-section class="row q-col-gutter-lg">
                    <div class="col-12 items-center q-mt-xs row justify-between">
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                            Adicionar extra
                        </div>

                        <q-btn
                            color="primary"
                            @click="hideCreateModal"
                            flat
                        >
                            <q-icon name="close" size="xs"/>
                        </q-btn>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsTypes"
                            outlined
                            v-model="createForm.type"
                            label="Extra"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(createForm.errors.type)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createForm.errors.type }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="createForm.name"
                            label="Nome do extra"
                            :bottom-slots="Boolean(createForm.errors.name)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createForm.errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsPlayes"
                            outlined
                            v-model="createForm.player"
                            label="Tipo de player"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(createForm.errors.player)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createForm.errors.player }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="createForm.embed"
                            label="Código embed"
                            :bottom-slots="Boolean(createForm.errors.embed)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createForm.errors.embed }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 row justify-end">
                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            @click="hideCreateModal"
                            size="sm"
                            class="q-my-md"
                            outline
                        >
                            <q-icon name="close" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cancelar
                            </div>
                        </q-btn>

                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            :disabled="createForm.processing"
                            size="sm"
                            class="q-my-md q-ml-sm"
                            @click="storeSubmit"
                        >
                            <q-icon name="check" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cadastrar
                            </div>
                        </q-btn>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </AuthenticatedLayout>
</template>
