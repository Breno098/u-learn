<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        category: Object,
        errors: Object,
    });

    const form = useForm({
        id: props.category.id,
        name: props.category.name,
    });

    const submit = () => {
        form.put(route("admin.category.update", form.id), {
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Categoria atualizada com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 1000
                    }
                })
            },
        })
    };

    const destroy = () => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir categoria',
                message: 'Ao excluir a categoria, todos os cursos serão desvinculados. Deseja realmente excluir?',
                confirm: true
            },
        }).onOk(() => {
            useForm().delete(route('admin.category.destroy', form.id), {
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: 'Categoria excluída com sucesso',
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })
                }
            })
        });
    }
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="category.name" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_school" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Categoria: {{ category.name }}
                    </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
                    <q-btn
                        color="negative"
                        class="q-mr-md"
                        no-caps
                        outline
                        @click="destroy"
                        icon="close"
                        label="Excluir"
                    />

                    <q-btn
                        color="indigo"
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                        icon="check"
                        label="Salvar"
                    />
                </div>
            </q-card-section>
        </q-card>

        <q-card flat>
            <q-card-section class="q-pb-none q-py-lg">
                <div class="row q-col-gutter-lg">
                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Informações
                        </div>
                    </div>

                    <div class="col-12 col-md-12">
                        <q-input
                            outlined
                            v-model="form.name"
                            label="Nome da categoria"
                            :bottom-slots="Boolean(errors.name)"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
