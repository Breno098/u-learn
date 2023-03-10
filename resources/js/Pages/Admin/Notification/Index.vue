<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/inertia-vue3';
</script>


<template>
    <AuthenticatedLayout>
        <Head title="Notificações" />

        <div class="row q-pb-xl">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Notificações (em desenvolvimento) </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Notificações" class="text-primary" />
                </q-breadcrumbs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
