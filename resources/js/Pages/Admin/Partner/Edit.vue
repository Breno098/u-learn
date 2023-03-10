<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        partner: Object,
        errors: Object,

        canPartnerDestroy: Boolean,
    });

    const form = useForm({
        id: props.partner.id,
        name: props.partner.name,
        company_name: props.partner.company_name,
        phone: props.partner.phone,
        email: props.partner.email,
        link: props.partner.link,
        start_at: props.partner.start_at,
        end_at: props.partner.end_at,
    });

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.partner.update", form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Parceiro atualizado com sucesso',
                        position: 'top',
                    })
                },
            })
    };

    function destroy() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir parceiro',
                message: 'Tem certeza que deseja excluir esse parceiro?',
            },
        }).onOk(() => {
            useForm().delete(route('admin.patner.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Parceiro excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Parceiro | Editar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Editar parceiro </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Parceiros" class="text-grey"/>
                    <q-breadcrumbs-el label="Editar parceiro" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    v-if="canPartnerDestroy"
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroy"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir parceiro
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
                        :bottom-slots="Boolean(errors.name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.company_name"
                        label="Empresa"
                        :bottom-slots="Boolean(errors.company_name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.company_name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.email"
                        label="E-mail"
                        :bottom-slots="Boolean(errors.email)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.email }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.phone"
                        label="Telefone"
                        :bottom-slots="Boolean(errors.phone)"
                        mask="(##) #########"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.phone }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.start_at"
                        mask="##/##/####"
                        label="Data de inicio"
                        :bottom-slots="Boolean(errors.start_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.start_at }} </div>
                        </template>

                        <q-popup-proxy>
                            <q-date
                                v-model="form.start_at"
                                mask="DD/MM/YYYY"
                                square
                            >
                                <div class="row items-center justify-end">
                                    <q-btn
                                        label="Ok"
                                        color="primary"
                                        flat
                                        v-close-popup
                                    />
                                </div>
                            </q-date>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/####"
                        label="Data final"
                        :bottom-slots="Boolean(errors.end_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.end_at }} </div>
                        </template>

                        <q-popup-proxy>
                            <q-date
                                v-model="form.end_at"
                                mask="DD/MM/YYYY"
                                square
                            >
                                <div class="row items-center justify-end">
                                    <q-btn
                                        label="Ok"
                                        color="primary"
                                        flat
                                        v-close-popup
                                    />
                                </div>
                            </q-date>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.link"
                        label="link"
                        :bottom-slots="Boolean(errors.link)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.link }} </div>
                        </template>
                    </q-input>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
