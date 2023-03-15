<script setup>
    import { computed } from 'vue';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        permissions: Object,
        errors: Object,
    });

    const form = useForm({
        id: null,
        name: null,
        can_delete: true,
        permission_ids: []
    });

    const submit = () => {
        form.post(route("admin.role.store", form.id), {
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Grupo cadastrado com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 3000
                    }
                })
            },
        })
    };

    const allSelected = computed({
        get: () => {
            let countPermissions = 0;
            for (const key in props.permissions) {
                countPermissions += props.permissions[key].length;
            }
            return form.permission_ids.length === countPermissions;
        },
        set: (value) => {
            form.permission_ids = [];

            if (value) {
                for (const key in props.permissions) {
                    props.permissions[key].forEach(p => form.permission_ids.push(p.id))
                }
            }
        }
    })

    const goBack = () =>  useForm().get(route('admin.role.index'));
</script>

<template>
    <Head title="Novo grupo de permissão" />

    <AuthenticatedLayout>
        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_school" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Novo grupo de permissão
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

                    <div class="col-12 col-md-12">
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

                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Permissões
                        </div>
                    </div>

                    <div class="col-12 items-center q-mt-xs">
                        <q-checkbox
                            :label="allSelected ? 'Desselecionar todas as permissões' : 'Selecionar todas as permissões'"
                            v-model="allSelected"
                            color="indigo"
                            checked-icon="task_alt"
                            unchecked-icon="radio_button_unchecked"
                            indeterminate-icon="remove_circle_outline"
                        />
                    </div>

                    <div
                        class="col-12 col-md-3"
                        v-for="perm, index in permissions"
                    >
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-16">
                            {{ index }}
                        </div>

                        <div v-for="permission in perm">
                            <q-checkbox
                                v-model="form.permission_ids"
                                :label="permission.label"
                                :val="permission.id"
                                color="indigo"
                                checked-icon="task_alt"
                                unchecked-icon="radio_button_unchecked"
                            />
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
