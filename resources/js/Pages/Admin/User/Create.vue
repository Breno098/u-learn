<script setup>
    import { ref, computed } from 'vue'
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
        roles: Array,
    });

    const form = useForm({
        id: null,
        name: null,
        email: null,
        cpf: null,
        phone: null,
        address: null,
        link: null,
        active: true,
        role_ids: [],
    });

    const submit = () => {
        form.post(route("admin.user.store", form.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Usuário cadastrado com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 1000
                    }
                })
            },
        })
    };

    const optionsRoles = ref(props.roles)

    const filterroles = (val, update, abort) => {
        update(() => optionsRoles.value = props.roles.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const goBack = () =>  useForm().get(route('admin.user.index'));
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Novo usuário" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_school" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Novo usuário
                    </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
                    <q-btn
                        color="indigo"
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                        icon="check"
                        label="Salvar"
                    />
                </div>
            </q-card-section>
        </q-card>

        <q-card flat>
            <q-card-section class="q-pb-none q-py-lg">
                <q-btn
                    dense
                    color="indigo"
                    class="absolute inset-shadow-down"
                    icon="chevron_left"
                    style="top: 0; left: 12px; transform: translateY(-50%);"
                    label="Voltar"
                    no-caps
                    @click="goBack"
                />

                <div class="row q-col-gutter-lg">
                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Informações
                        </div>
                    </div>

                    <div class="col-12 col-md-1">
                        <q-input
                            outlined
                            v-model="form.id"
                            label="ID"
                            disable
                            color="indigo"
                        />
                    </div>

                    <div class="col-12 col-md-5">
                        <q-input
                            outlined
                            v-model="form.name"
                            label="Nome"
                            :bottom-slots="Boolean(errors.name)"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-3">
                        <q-input
                            outlined
                            v-model="form.cpf"
                            label="CPF"
                            :bottom-slots="Boolean(errors.cpf)"
                            mask="###.###.###-##"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.cpf }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-3">
                        <q-select
                            :options="[{
                                label: 'Ativo',
                                value: true
                            }, {
                                label: 'Inativo',
                                value: false
                            }]"
                            outlined
                            v-model="form.active"
                            label="Status"
                            :bottom-slots="Boolean(errors.active)"
                            map-options
                            emit-value
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.active }} </div>
                            </template>

                            <template v-slot:selected-item="scope">
                                <q-chip
                                    :tabindex="scope.tabindex"
                                    text-color="white"
                                    :color="scope.opt.value ? 'green' : 'red'"
                                    dense
                                    class="q-my-none"
                                    square
                                >
                                    {{ scope.opt.label }}
                                </q-chip>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-md-9">
                        <q-input
                            outlined
                            v-model="form.email"
                            label="E-mail"
                            :bottom-slots="Boolean(errors.email)"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.email }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-3">
                        <q-input
                            outlined
                            v-model="form.phone"
                            label="Telefone"
                            :bottom-slots="Boolean(errors.phone)"
                            mask="(##) #########"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.phone }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-12">
                        <q-input
                            outlined
                            v-model="form.address"
                            label="Endereço"
                            :bottom-slots="Boolean(errors.address)"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.address }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-12">
                        <q-select
                            :options="optionsRoles"
                            option-value="id"
                            option-label="name"
                            emit-value
                            map-options
                            outlined
                            v-model="form.role_ids"
                            label="Grupos de permissão"
                            :bottom-slots="Boolean(errors.role_ids)"
                            clearable
                            use-input
                            @filter="filterroles"
                            multiple
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.role_ids }} </div>
                            </template>

                            <template v-slot:selected-item="scope">
                                <q-chip
                                    class="q-my-none"
                                    :label="scope.opt.name"
                                    dense
                                    color="indigo"
                                    text-color="white"
                                    square
                                >
                                    <q-icon
                                        name="cancel"
                                        size="xs"
                                        @click="scope.removeAtIndex(scope.index)"
                                        class="q-ml-xs cursor-pointer"
                                    />
                                </q-chip>
                            </template>
                        </q-select>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
