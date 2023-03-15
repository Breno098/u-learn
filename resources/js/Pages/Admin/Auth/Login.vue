<script setup>
    import GuestLayout from '@/Layouts/Admin/GuestLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    defineProps({
        status: String,
    });

    const form = useForm({
        email: 'admin@admin.com',
        password: 'admin',
        remember: false
    });

    const submit = () => {
        form.post(route('admin.auth.login'), {
            onFinish: () => form.reset('password'),
        });
    };

    const goResetPassword = () => useForm().get(route('admin.auth.forgot-password-form'));
</script>

<template>
    <Head title="Login" />

    <GuestLayout>
        <div class="row window-height">
            <div class="col-md-8 col-12 column flex-center">
                <q-card flat style="width: 900px; max-width: 90vw;" class="q-mb-lg">
                    <q-card-section class="flex flex-center">
                        <div class="adm-fs-28 adm-fw-700 text-blue-grey-10 q-mr-md">
                            Entrar
                        </div>

                        <q-icon name="login" color="indigo" size="lg"/>
                    </q-card-section>
                </q-card>

                <q-card flat style="width: 900px; max-width: 90vw;">
                    <q-card-section>
                        <q-form class="q-gutter-md">
                            <q-input
                                filled
                                v-model="form.email"
                                label="E-mail"
                                color="indigo"
                                type="email"
                                @keydown.enter.prevent="submit"

                            >
                                <template v-slot:prepend>
                                    <q-icon name="alternate_email" />
                                </template>
                            </q-input>

                            <q-input
                                filled
                                v-model="form.password"
                                label="Senha"
                                color="indigo"
                                type="password"
                                @keydown.enter.prevent="submit"
                                :bottom-slots="Boolean(form.errors.email)"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="password" />
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
                                <div class="text-grey q-mr-xs">
                                    Esqueceu a senha?
                                </div>
                                <div class="text-indigo cursor-pointer" @click="goResetPassword">
                                    Clique aqui
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <q-btn
                                color="indigo"
                                no-caps
                                @click="submit"
                                label="Entrar"
                                class="full-width"
                            />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-md-4 col-12 bg-indigo column flex-center">
                <q-icon name="o_school" color="white" size="10em"/>

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
