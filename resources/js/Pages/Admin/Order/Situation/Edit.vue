<script setup>
    import { ref, computed } from 'vue'
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import DialogConfirm from '@/Components/DialogConfirm.vue';
    import { useQuasar } from 'quasar'

    const $q = useQuasar()

    const props = defineProps({
        order: Object,
        errors: Object,
        paymentMethods: Object,
        statuses: Object,
        campaigns: Object,
    });

    const form = useForm({
        // id: props.user.id,
        // name: props.user.name,
        // email: props.user.email,
        // cpf: props.user.cpf,
        // phone: props.user.phone,
        // address: props.user.address,
        // link: props.user.link,
        // active: props.user.active,
        // group_id: props.user.group_id,
        // profile_image: null,
        // customer_cpf: props.user.customer_cpf,
        // customer_phone: props.user.customer_phone,
        // customer_address: props.user.customer_address,
        // equal_data: props.user.equal_data,
    });

    function cancelRecurrence() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Cancelar recorrência?',
                message: 'Tem certeza que deseja cancelar recorrência?',
            },
        }).onOk(() => {
            useForm().post(route('admin.order.cancel', props.order.id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Recorrência cancelada com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    const optionsCampaigns = ref(props.campaigns)

    const optionsPaymentMethods = computed(() => {
        let options = new Array();

        for (const key in props.paymentMethods) {
            options.push({ label: props.paymentMethods[key], value: key })
        }

        return options;
    })

    const optionsStatuses = computed(() => {
        let options = new Array();

        for (const key in props.statuses) {
            options.push({ label: props.statuses[key], value: key })
        }

        return options;
    })

    const getClassStatus = (status) => {
        switch (status) {
            case 'adimplente': return 'adm-bg-positive';
            case 'cancelado': return 'adm-bg-error';
            case 'nao_renovou': return 'adm-bg-negative';
            case 'vencido': return 'adm-bg-caution';
            default: return 'bg-white'
        }
    }

    const registrationTypeComputed = computed(() => props.order.registration_type.replace(/^./, str => str.toUpperCase()));
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Aluno | Situação" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Situação do aluno </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Situação do aluno" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="indigo-8"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                >
                    <q-icon name="attach_money" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Notas fiscais
                    </div>
                </q-btn>

                <q-btn
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="cancelRecurrence"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Cancelar recorrência
                    </div>
                </q-btn>

                <q-btn
                    color="primary"
                    rounded
                    no-caps
                >
                    <q-icon name="check" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Atualizar aluno
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Situação do aluno
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <q-select
                        :options="optionsStatuses"
                        outlined
                        v-model="order.status"
                        label="Status do aluno"
                        map-options
                        emit-value
                    >
                        <template v-slot:selected-item="scope">
                            <q-chip
                                text-color="white"
                                :class="getClassStatus(scope.opt.value)"
                                class="q-my-none"
                            >
                                {{ scope.opt.label }}
                            </q-chip>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-4">
                    <q-select
                        :options="optionsPaymentMethods"
                        outlined
                        v-model="order.payment_method"
                        label="Forma de pagamento"
                        map-options
                        emit-value
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="registrationTypeComputed"
                        label="Tipo de cadastrado"
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="order.payment_value"
                        label="Valor pago"
                        mask="R$ ####.##"
                        fill-mask="0"
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="order.hiring_start_at"
                        mask="##/##/####"
                        label="Data da contratação"
                        placeholder="dia/mês/ano"
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="order.hiring_end_at"
                        mask="##/##/####"
                        label="Data final da contratação"
                        placeholder="dia/mês/ano"
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Dados do aluno
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <q-input
                        outlined
                        v-model="order.student.id"
                        label="ID do aluno"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="order.student.name"
                        label="Nome do aluno"
                        disable
                    />
                </div>

                <div class="col-12 col-md-3">
                    <q-input
                        outlined
                        v-model="order.student.cpf"
                        label="CPF do aluno"
                        mask="###.###.###-##"
                        disable
                    />
                </div>


                <div class="col-12 col-md-9">
                    <q-input
                        outlined
                        v-model="order.student.email"
                        label="E-mail do aluno"
                        disable
                    />
                </div>

                <div class="col-12 col-md-3">
                    <q-input
                        outlined
                        v-model="order.student.phone"
                        label="Telefone do aluno"
                        mask="(##) #########"
                        disable
                    />
                </div>

                <div class="col-12 col-md-5">
                    <q-input
                        outlined
                        label="Produto"
                        disable
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-select
                        :options="optionsCampaigns"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="order.campaign_id"
                        label="Campanha"
                        disable
                    >
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

                <div class="col-12 col-md-3">
                    <q-input
                        outlined
                        v-model="order.id"
                        label="Pedido"
                        disable
                    />
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Dados do cliente
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="order.student.customer_cpf"
                        label="CPF do cliente"
                        mask="###.###.###-##"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="order.student.customer_phone"
                        label="Telefone do cliente"
                        mask="(##) #########"
                        disable
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="order.student.customer_address"
                        label="Endereço do cliente"
                        disable
                    />
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="order.student.equal_data"
                        label="Dados iguais para cliente e aluno"
                        disable
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
