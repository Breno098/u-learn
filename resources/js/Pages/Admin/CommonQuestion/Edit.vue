<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { Inertia } from '@inertiajs/inertia';
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        commonQuestion: Object,
        errors: Object,
    });

    const form = useForm({
        id: props.commonQuestion.id,
        question_text: props.commonQuestion.question_text,
        answer_text: props.commonQuestion.answer_text,
        show: props.commonQuestion.show,
    });

    const submit = () => {
        form.put(route("admin.common-question.update", form.id), {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: 'Pergunta atualizada com sucesso',
                    position: 'top',
                })
            },
        })
    };

    function destroy() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir pergunta',
                message: 'Tem certeza que deseja excluir essa pergunta?',
            },
        }).onOk(() => {
            Inertia.delete(route('admin.common-question.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Pergunta excluída com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pergunta | Editar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Editar pergunta </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Perguntas" class="text-grey"/>
                    <q-breadcrumbs-el label="Editar pergunta" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroy"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir pergunta
                    </div>
                </q-btn>

                <q-btn
                    color="primary"
                    rounded
                    no-caps
                    @click="submit"
                    :disabled="form.processing"
                >
                    <q-icon name="check" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Salvar
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.question_text"
                        label="Pergunta"
                        :bottom-slots="Boolean(errors.question_text)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.question_text }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.answer_text"
                        label="Resposta"
                        :bottom-slots="Boolean(errors.answer_text)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.answer_text }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.show"
                        label="Exibir para o aluno"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
