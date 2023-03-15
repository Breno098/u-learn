<script setup>
    import GuestLayout from '@/Layouts/Admin/GuestLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    const props = defineProps({
        email: String,
        token: String,
        errors: Object,
    });

    const form = useForm({
        token: props.token,
        email: props.email,
        password: null,
        password_confirmation: null,
    });

    const submit = () => {
        form.post(route('admin.auth.password.reset'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

    const goAuth = () => {
        useForm().get(route('admin.auth.sign-in'))
    }
</script>

<template>
    <Head title="Redefinir a senha"/>

    <GuestLayout>
        <div class="row window-height">
            <div class="col-md-8 col-12 column flex-center">
                <q-card flat style="width: 900px; max-width: 90vw;" class="q-mb-lg">
                    <q-card-section class="flex flex-center">
                        <div class="adm-fs-28 adm-fw-700 text-blue-grey-10 q-mr-md">
                            Redefinir senha
                        </div>

                        <q-icon name="lock_reset" color="indigo" size="lg" />
                    </q-card-section>
                </q-card>

                <q-card flat style="width: 900px; max-width: 90vw;">
                    <q-card-section class="text-center text-red" v-if="errors.password_reset">
                        {{ errors.password_reset }}
                    </q-card-section>

                    <q-card-section>
                        <q-form class="q-gutter-md">
                            <q-input
                                filled
                                v-model="form.password"
                                label="Nova Senha"
                                color="indigo"
                                type="password"
                                :bottom-slots="Boolean(form.errors.password)"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="password" />
                                </template>

                                <template v-slot:hint>
                                    <div class="text-red"> {{ form.errors.password }} </div>
                                </template>
                            </q-input>

                            <q-input
                                filled
                                v-model="form.password_confirmation"
                                label="Confirme a Senha"
                                color="indigo"
                                type="password"
                                :bottom-slots="Boolean(form.errors.password_confirmation)"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="password" />
                                </template>

                                <template v-slot:hint>
                                    <div class="text-red"> {{ form.errors.password_confirmation }} </div>
                                </template>
                            </q-input>
                        </q-form>
                    </q-card-section>

                    <q-card-section class="row q-col-gutter-sm">
                        <div class="col-12 col-md-9">
                            <div class="row items-center juntify-start">
                                <div class="text-indigo cursor-pointer" @click="goAuth">
                                    Ir para login
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <q-btn
                                color="indigo"
                                no-caps @click="submit"
                                label="Reefinir"
                                class="full-width"
                                :loading="form.processing"
                            >
                                <template v-slot:loading>
                                    <div class="q-mr-sm"> Redefinindo.. </div>
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

    <!-- <GuestLayout>
        <div class="column">
            <div class="row">
                <q-card bordered class="shadow-1" style="width: 50vw">
                    <q-card-section>
                        <q-form class="q-gutter-md">
                            <q-input
                                filled
                                v-model="form.email"
                                label="Nova Senha"
                                :bottom-slots="Boolean(errors.email)"
                                disable
                            >
                                <template v-slot:hint>
                                    <div class="text-red"> {{ errors.email }} </div>
                                </template>
                            </q-input>

                            <q-input
                                filled
                                v-model="form.password"
                                type="password"
                                label="Nova Senha"
                                :bottom-slots="Boolean(errors.password)"
                            >
                                <template v-slot:hint>
                                    <div class="text-red"> {{ errors.password }} </div>
                                </template>
                            </q-input>

                            <q-input
                                filled
                                v-model="form.password_confirmation"
                                type="password"
                                label="Confirme a Senha"
                                :bottom-slots="Boolean(errors.password_confirmation)"
                            >
                                <template v-slot:hint>
                                    <div class="text-red"> {{ errors.password_confirmation }} </div>
                                </template>
                            </q-input>
                        </q-form>
                    </q-card-section>

                    <q-card-actions class="q-px-md">
                        <q-btn
                            unelevated
                            color="primary"
                            size="lg"
                            class="full-width"
                            @click="submit"
                            :disabled="form.processing"
                            :loading="form.processing"
                        >
                            <div class="flex flex-center">
                                <div class="q-mr-sm"> Redefinir </div>
                                <q-icon name="lock_reset" size="sm"/>
                            </div>

                            <template v-slot:loading>
                                <div class="q-mr-sm"> Redefinindo... </div>
                                <q-spinner-ios class="on-left" size="sm"/>
                            </template>
                        </q-btn>
                    </q-card-actions>

                    <q-card-section class="text-center q-pb-none">
                        <p class="text-grey-6"> {{ (new Date()).getFullYear() }}</p>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </GuestLayout> -->
</template>
