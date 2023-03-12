<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    const props = defineProps({
        errors: Object,
    });
</script>

<template>
    <Head title="Home"/>

    <AuthenticatedLayout>
        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_home" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md"> Home </div>
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
