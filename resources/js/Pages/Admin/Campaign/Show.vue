<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    const props = defineProps({
        campaign: Object,
        errors: Object,
    });

    const form = useForm({
        id: props.campaign.id,
        name: props.campaign.name,
        start_at: props.campaign.start_at,
        end_at: props.campaign.end_at,
    });

    const goBack = () => useForm().get(route('admin.meeting.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Campanha | Visualizar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Campanha </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Campanha" class="text-grey"/>
                    <q-breadcrumbs-el label="Visualizar Campanha" class="text-primary" />
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

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Informações
                    </div>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.start_at"
                        mask="##/##/####"
                        label="Data de inicio"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/####"
                        label="Data final"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
