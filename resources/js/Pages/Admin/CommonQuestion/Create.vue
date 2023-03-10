<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
    });

    const form = useForm({
        id: null,
        question_text: null,
        answer_text: null,
        show: true,
    });

    const submit = () => {
        form.post(route("admin.common-question.store"), {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: 'Pergunta cadastrada com sucesso',
                    position: 'top',
                })
            },
        })
    };

    const goBack = () => useForm().get(route('admin.common-question.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pergunta | Adicionar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Novo pergunta </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Perguntas" class="text-grey"/>
                    <q-breadcrumbs-el label="Novo pergunta" class="text-primary" />
                </q-breadcrumbs>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.question_text"
                        label="Pergunta"
                        :bottom-slots="Boolean(form.errors.question_text)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ form.errors.question_text }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.answer_text"
                        label="Resposta"
                        :bottom-slots="Boolean(form.errors.answer_text)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ form.errors.answer_text }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.show"
                        label="Exibir para o aluno"
                    />
                </div>

                <div class="col-12 flex justify-end items-center">
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

                    <q-btn
                        color="primary"
                        rounded
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                    >
                        <q-icon name="add" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Criar pergunta
                        </div>
                    </q-btn>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
