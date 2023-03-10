<script setup>
    import GuestLayout from '@/Layouts/Admin/GuestLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    const form = useForm({
        email: 'admin@admin.com',
        password: 'admin',
    });

    const submit = () => {
        form.post(route('admin.auth.login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <q-img
            src="/img/admin/logo_ligia_academy.png"
            class="q-mx-auto absolute-top adm-auth-logo"
        />

        <q-card
            flat
            class="q-pa-md adm-br-16 adm-auth-card-form"
        >
            <q-card-section class="row q-col-gutter-sm q-py-lg q-px-lg">
                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.email"
                        type="email"
                        label="E-mail"
                        :hint="form.errors.email"
                    />
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.password"
                        type="password"
                        label="Senha"
                    />
                </div>
            </q-card-section>

            <q-card-actions class="flex flex-center q-px-lg">
                <q-btn
                    color="primary"
                    rounded
                    unelevated
                    label="Entrar"
                    @click="submit"
                    no-caps
                    style="width: 201px;"
                />
            </q-card-actions>
        </q-card>
    </GuestLayout>
</template>
