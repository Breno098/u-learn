<script setup>
    import GuestLayout from '@/Layouts/Admin/GuestLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue';

    defineProps({
        errors: Object
    });

    const form = useForm({
        email: null,
    });

    const emailSuccessfullySent = ref(false);

    const submit = () => {
        form.post(route('admin.auth.forgot-password-send-link'), {
            onSuccess: () => {
                emailSuccessfullySent.value = true;
            },
            onFinish: () => form.email = null,
        });
    };

    const goBack = () => {
        useForm().get(route('admin.auth.sign-in'))
    }
</script>

<template>
    <Head title="Esqueci minha senha" />

    <GuestLayout>
        <div class="row window-height">
            <div class="col-md-8 col-12 column flex-center">
                <q-card flat style="width: 900px; max-width: 90vw;" class="q-mb-lg">
                    <q-card-section class="flex flex-center">
                        <div class="adm-fs-28 adm-fw-700 text-blue-grey-10">
                            Esqueceu sua senha
                        </div>

                        <q-icon name="question_mark" color="indigo" size="lg" />
                    </q-card-section>
                </q-card>

                <q-card flat style="width: 900px; max-width: 90vw;">
                    <q-card-section class="text-center text-red" v-if="errors.send_reset_password_mail">
                        {{ errors.send_reset_password_mail }}
                    </q-card-section>

                    <q-card-section class="text-center text-green adm-fs-18" v-if="emailSuccessfullySent">
                        Acabamos de enviar uma mensagem de redefinição de senha para seu e-mail.
                    </q-card-section>

                    <q-card-section class="text-center text-blue-grey-10" v-if="!emailSuccessfullySent && !errors.send_reset_password_mail">
                        Sem problemas. <br />
                        Basta nos informar seu endereço de e-mail e nós lhe enviaremos um link de <br />
                        redefinição de senha que permitirá que você escolha uma nova.
                    </q-card-section>

                    <q-card-section>
                        <q-form class="q-gutter-md">
                            <q-input
                                filled
                                v-model="form.email"
                                label="E-mail"
                                color="indigo"
                                type="email"
                                @keydown.enter.prevent="submit"
                                :bottom-slots="Boolean(form.errors.email)"
                                :disable="emailSuccessfullySent"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="alternate_email" />
                                </template>

                                <template v-slot:hint>
                                    <div class="text-red"> {{ form.errors.email }} </div>
                                </template>
                            </q-input>
                        </q-form>
                    </q-card-section>

                    <q-card-section class="row q-col-gutter-sm">
                        <div class="col-12 col-md-9">
                            <div class="row items-center juntify-start">
                                <div class="text-indigo cursor-pointer" @click="goBack">
                                    Voltar a tela de login
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <q-btn
                                color="indigo"
                                no-caps @click="submit"
                                label="Reefinir senha"
                                class="full-width"
                                :loading="form.processing"
                                :disable="emailSuccessfullySent"
                            >
                                <template v-slot:loading>
                                    <div class="q-mr-sm"> Enviando email </div>
                                    <q-spinner-ios class="on-left" size="sm" />
                                </template>
                            </q-btn>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-md-4 col-12 bg-indigo column flex-center">
                <q-icon name="o_school" color="white" size="10em" />

                <div>
                    <div class="text-white text-h3 q-mb-sm">
                        Bem vindo ao {{ $page.props.title }}
                    </div>

                    <div class="text-white">
                        Uma nova experiência de aprendizagem de modo simples e engajado.
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
