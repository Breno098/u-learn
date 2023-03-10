<script setup>
    import { ref, computed } from 'vue'
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
        groups: Array,

    });

    const form = useForm({
        id: null,
        name: null,
        email: null,
        password: null,
        cpf: null,
        phone: null,
        address: null,
        link: null,
        active: true,
        customer_cpf: null,
        customer_phone: null,
        customer_address: null,
        equal_data: false,
        group_ids: [],
    });

    const submit = () => {
        form
            .transform((data) => ({
                ...data,
                customer_cpf: customerCPF.value,
                customer_phone: customerPhone.value,
                customer_address: customerAddress.value,
             }))
            .post(route("admin.student.store"), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Aluno cadastrado com sucesso',
                        position: 'top',
                    })
                },
            })
    };

    const customerCPF = computed({
        get: () => form.equal_data ? form.cpf : form.customer_cpf,
        set: (newValue) => form.customer_cpf = newValue
    })

    const customerPhone = computed({
        get: () => form.equal_data ? form.phone : form.customer_phone,
        set: (newValue) => form.customer_phone = newValue
    })

    const customerAddress = computed({
        get: () => form.equal_data ? form.address : form.customer_address,
        set: (newValue) => form.customer_address = newValue
    })

    const optionsGroups = ref(props.groups)

    const filterGroups = (val, update, abort) => {
        update(() => optionsGroups.value = props.groups.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const goBack = () => useForm().get(route('admin.student.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Aluno | Novo" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Novo aluno </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Alunos" class="text-grey"/>
                    <q-breadcrumbs-el label="Novo aluno" class="text-primary" />
                </q-breadcrumbs>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Dados do aluno
                    </div>
                </div>

                <div class="col-12 col-md-2">
                    <q-input
                        outlined
                        v-model="form.id"
                        label="ID do aluno"
                        disable
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do aluno"
                        :bottom-slots="Boolean(errors.name)"
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
                        label="CPF do aluno"
                        :bottom-slots="Boolean(errors.cpf)"
                        mask="###.###.###-##"
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
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.active }} </div>
                        </template>

                        <template v-slot:selected-item="scope">
                            <q-chip
                                :tabindex="scope.tabindex"
                                text-color="white"
                                :class="{
                                    'adm-bg-positive':  scope.opt.value,
                                    'adm-bg-negative': !scope.opt.value
                                }"
                                dense
                                class="q-my-none"
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
                        label="E-mail do aluno"
                        :bottom-slots="Boolean(errors.email)"
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
                        label="Telefone do aluno"
                        :bottom-slots="Boolean(errors.phone)"
                        mask="(##) #########"
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
                            label="Endereço do aluno"
                            :bottom-slots="Boolean(errors.address)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.address }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-12">
                    <q-select
                            :options="optionsGroups"
                            option-value="id"
                            option-label="name"
                            emit-value
                            map-options
                            outlined
                            v-model="form.group_ids"
                            label="Grupos de alunos"
                            :bottom-slots="Boolean(errors.group_ids)"
                            clearable
                            use-input
                            @filter="filterGroups"
                            multiple
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.group_ids }} </div>
                        </template>

                        <template v-slot:selected-item="scope">
                            <q-chip
                                    class="adm-chip-primary q-my-none"
                                    :label="scope.opt.name"
                                    dense
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

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Dados do cliente
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="customerCPF"
                        label="CPF do cliente"
                        :bottom-slots="Boolean(errors.customer_cpf)"
                        mask="###.###.###-##"
                        :disable="form.equal_data"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.customer_cpf }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="customerPhone"
                        label="Telefone do cliente"
                        :bottom-slots="Boolean(errors.customer_phone)"
                        mask="(##) #########"
                        :disable="form.equal_data"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.customer_phone }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="customerAddress"
                        label="Endereço do cliente"
                        :bottom-slots="Boolean(errors.customer_address)"
                        :disable="form.equal_data"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.customer_address }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.equal_data"
                        label="Dados iguais do aluno"
                    />
                </div>

                <div class="col-12 flex justify-end items-center">
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

                    <q-btn
                        color="primary"
                        rounded
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                    >
                        <q-icon name="add" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Criar aluno
                        </div>
                    </q-btn>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
