<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue';
    import { useQuasar } from 'quasar'
    import DialogConfirm from '@/Components/DialogConfirm.vue';
    import SummaryFinancial from '@/Components/Financial/Summary.vue';

    const $q = useQuasar()

    const props = defineProps({
        orders: Object,
        errors: Object,
        query: Object,

        paymentMethods: Object,
        statuses: Object,
        campaigns: Object,
        students: Array,

        ordersPaidCount: Number,
        ordersExpiredCount: Number,
        ordersCanceledCount: Number,
        ordersNotRenewedCount: Number,

        churnRateThisMonth: Number,
        churnRateLastMonth: Number,
        differenceInChurnRates: Number,

        canOrderCancel: Boolean,
        canOrderSituationUpdate: Boolean,
        canOrderSituationShow: Boolean,

        valueOfOrdersCanceledInSelectedMonth: Number,
        valueOfOrdersCanceledInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersExpiredInSelectedMonth: Number,
        valueOfOrdersExpiredInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersToBeReceivedInSelectedMonth: Number,
        valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersToBeReceivedToday: Number,
        valueOfOrdersToBeReceivedYesterday: Number,

        selectedMonth: Number,
    });

    const columns = [{
        name: 'student_id',
        align: 'left',
        label: 'ID do aluno',
        field: order => order.student.id,
        style: 'width: 5%',
    }, {
        name: 'student_name',
        align: 'left',
        label: 'Nome do aluno',
        field: order => order.student.name,
        style: 'width: 20%',
    }, {
        name: 'student_email',
        align: 'left',
        label: 'E-mail do aluno',
        field: order => order.student.email,
        style: 'width: 25%',
    }, {
        name: 'registration_type',
        align: 'left',
        label: 'Tipo de cadastro',
        field: order => order.registration_type.replace(/^./, str => str.toUpperCase()),
        style: 'width: 15%',
    }, {
        name: 'payment_method',
        align: 'left',
        label: 'Forma de pagamento',
        field: order => getPaymentMethodsLabel(order.payment_method),
        style: 'width: 25%',
    }, {
        name: 'status',
        align: 'left',
        label: 'Status',
        field: 'status',
        style: 'width: 10%',
    }, {
        name: 'actions',
        label: 'Ação',
    }];

    const requestData = useForm({
        orderBy: props.query.orderBy,
        sort: props.query.sort,
        page: props.query.page,
        rowsPerPage: props.query.rowsPerPage,
        filters: {
            payment_method: props.query.filters.payment_method,
            status: props.query.filters.status,
            hiring_start_at: props.query.filters.hiring_start_at,
            campaign_id: props.query.filters.campaign_id,
            student_id: props.query.filters.student_id,
        },
    });

    const sortBy = (field) => {
        if (requestData.orderBy === field) {
            requestData.sort = requestData.sort == 'desc' ? 'asc' : 'desc';
        } else {
            requestData.sort == 'asc';
        }

        requestData.orderBy = field;
        requestData.page = 1;

        submit();
    }

    const removeFilter = (filterName) => {
        requestData.filters[filterName] = null;
        submit();
    }

    const submit = () => {
        requestData.get(route('admin.financial.index'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => showFilters.value = false,
        });
    }

    const cancelRecurrence = (id) => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Cancelar recorrência?',
                message: 'Tem certeza que deseja cancelar recorrência?',
            },
        }).onOk(() => {
            useForm().post(route('admin.order.cancel', id), {
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

    const edit = (id) => useForm().get(route('admin.order.situation.edit', id));

    const show = (id) => useForm().get(route('admin.order.situation.show', id));

    const countAppliedFilters = computed(() => Object.values(props.query.filters).filter(fil => fil).length);

    const showFilters = ref(false)

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

    const optionsCampaigns = ref(props.campaigns)

    const filterCampaigns = (val, update, abort) => {
        update(() => optionsCampaigns.value = props.campaigns.filter(i => i.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsStudents = ref(props.students)

    const filterStudents = (val, update, abort) => {
        update(() => optionsStudents.value = props.students.filter(i => i.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const getPaymentMethodsLabel = (index) => props.paymentMethods[index];

    const getStatusLabel = (index) => props.statuses[index];

    const getClassStatus = (status) => {
        switch (status) {
            case 'adimplente': return 'adm-bg-positive';
            case 'cancelado': return 'adm-bg-error';
            case 'nao_renovou': return 'adm-bg-negative';
            case 'vencido': return 'adm-bg-caution';
            default: return 'bg-white'
        }
    }

    const optionsMonths = computed(() => [
        { value: 1, label: 'Janeiro' },
        { value: 2, label: 'Fevereiro'},
        { value: 3, label: 'Março' },
        { value: 4, label: 'Abril' },
        { value: 5, label: 'Maio' },
        { value: 6, label: 'Junho' },
        { value: 7, label: 'Julho' },
        { value: 8, label: 'Agosto' },
        { value: 9, label: 'Setembro' },
        { value: 10, label: 'Outubro' },
        { value: 11, label: 'Novembro' },
        { value: 12, label: 'Dezembro' },
    ])

    const month = ref(props.selectedMonth)

    const selectedMonthSubmit = () => {
        useForm({ month: month.value }).get(route('admin.financial.index'), {
            preserveState: true,
            preserveScroll: true,
        });
    }

    const clearFilters = () => useForm().get(route('admin.financial.index'))

</script>

<template>
    <Head title="Financeiro"/>

    <AuthenticatedLayout>
        <div class="row q-pb-sm">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Financeiro </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Financeiro" class="text-primary" />
                </q-breadcrumbs>
            </div>
        </div>

        <div class="row justify-end">
            <div class="row items-center">
                <q-select
                    :options="optionsMonths"
                    borderless
                    emit-value
                    map-options
                    v-model="month"
                    @update:model-value="selectedMonthSubmit"
                    class="q-mr-sm"
                />
            </div>
        </div>

        <SummaryFinancial
            :valueOfOrdersCanceledInSelectedMonth="valueOfOrdersCanceledInSelectedMonth"
            :valueOfOrdersCanceledInTheMonthBeforeSelectedMonth="valueOfOrdersCanceledInTheMonthBeforeSelectedMonth"
            :valueOfOrdersExpiredInSelectedMonth="valueOfOrdersExpiredInSelectedMonth"
            :valueOfOrdersExpiredInTheMonthBeforeSelectedMonth="valueOfOrdersExpiredInTheMonthBeforeSelectedMonth"
            :valueOfOrdersToBeReceivedInSelectedMonth="valueOfOrdersToBeReceivedInSelectedMonth"
            :valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth="valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth"
            :valueOfOrdersToBeReceivedToday="valueOfOrdersToBeReceivedToday"
            :valueOfOrdersToBeReceivedYesterday="valueOfOrdersToBeReceivedYesterday"
        />

        <q-card flat class="adm-br-16 q-pa-md q-mb-lg">
            <q-card-section class="flex flex-center q-py-sm">
                <q-chip class="adm-bg-positive" text-color="white">
                    {{ ordersPaidCount }} Adimplentes
                </q-chip>

                <q-chip class="adm-bg-caution" text-color="white">
                    {{ ordersExpiredCount }} Vencidos
                </q-chip>

                <q-chip class="adm-bg-error" text-color="white">
                    {{ ordersCanceledCount }} Cancelados
                </q-chip>

                <q-chip class="adm-bg-negative" text-color="white">
                    {{ ordersNotRenewedCount }} Não renovados
                </q-chip>
            </q-card-section>
        </q-card>

        <q-card flat class="adm-br-16 q-pa-sm q-mb-lg">
            <q-card-section class="row items-center q-py-sm q-px-lg">
                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.student_id"
                    :label="`Aluno = ${students.filter(t => t.id == query.filters.student_id).shift().name}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('student_id')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.status"
                    :label="`Status = ${optionsStatuses.filter(optS => optS.value == query.filters.status).shift().label}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('status')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.campaign_id"
                    :label="`Campanha = ${campaigns.filter(t => t.id == query.filters.campaign_id).shift().name}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('campaign_id')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.payment_method"
                    :label="`Forma de pagamento = ${optionsPaymentMethods.filter(optPM => optPM.value == query.filters.payment_method).shift().label}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('payment_method')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-chip
                    class="adm-chip-primary"
                    v-if="query.filters.hiring_start_at"
                    :label="`Data da contratação em = ${query.filters.hiring_start_at}`"
                >
                    <q-icon
                        name="cancel"
                        size="xs"
                        @click="removeFilter('hiring_start_at')"
                        class="q-ml-xs cursor-pointer"
                    />
                </q-chip>

                <q-space/>

                <q-btn round dense flat color="primary">
                    <q-badge
                        color="primary"
                        floating
                        rounded
                        :label="countAppliedFilters"
                        v-if="countAppliedFilters"
                    />

                    <q-icon name="tune" class="rotate-90"/>

                    <q-menu
                        style="min-width: 500px"
                        max-width='500px'
                        class="bg-white q-pa-md adm-br-16"
                        v-model="showFilters"
                    >
                        <div class="row q-col-gutter-sm">
                            <div class="col-12">
                                <q-select
                                    :options="optionsStudents"
                                    option-value="id"
                                    option-label="name"
                                    emit-value
                                    map-options
                                    outlined
                                    v-model="requestData.filters.student_id"
                                    label="Aluno"
                                    clearable
                                    use-chips
                                    use-input
                                    @filter="filterStudents"
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

                            <div class="col-12">
                                <q-select
                                    :options="optionsStatuses"
                                    outlined
                                    v-model="requestData.filters.status"
                                    label="Status"
                                    emit-value
                                    map-options
                                    clearable
                                />
                            </div>

                            <div class="col-12">
                                <q-select
                                    :options="optionsPaymentMethods"
                                    outlined
                                    v-model="requestData.filters.payment_method"
                                    label="Forma de pagamento"
                                    emit-value
                                    map-options
                                    clearable
                                />
                            </div>

                            <div class="col-12">
                                <q-select
                                    :options="optionsCampaigns"
                                    option-value="id"
                                    option-label="name"
                                    emit-value
                                    map-options
                                    outlined
                                    v-model="requestData.filters.campaign_id"
                                    label="Campanha"
                                    clearable
                                    use-chips
                                    use-input
                                    @filter="filterCampaigns"
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

                            <div class="col-12">
                                <q-input
                                    outlined
                                    v-model="requestData.filters.hiring_start_at"
                                    mask="##/##/####"
                                    label="Data da contratação"
                                    placeholder="dia/mês/ano"
                                    :bottom-slots="Boolean(errors['filters.hiring_start_at'])"
                                >
                                    <template v-slot:hint>
                                        <div class="text-red"> {{ errors['filters.hiring_start_at'] }} </div>
                                    </template>

                                    <template v-slot:prepend>
                                        <q-icon name="o_calendar_today" />
                                    </template>
                                </q-input>
                            </div>
                        </div>

                        <div class="flex flex-center q-pt-lg q-pa-md q-gutter-sm">
                            <q-btn
                                    color="primary"
                                    rounded
                                    no-caps
                                    @click="submit"
                            >
                                <q-icon name="check" size="xs"/>

                                <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                    Filtrar
                                </div>
                            </q-btn>

                            <q-btn
                                    color="primary"
                                    rounded
                                    outline
                                    no-caps
                                    @click="clearFilters"
                            >
                                <q-icon name="close" size="xs"/>

                                <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20 m-4">
                                    Limpar
                                </div>
                            </q-btn>
                        </div>
                    </q-menu>
                </q-btn>
            </q-card-section>

            <q-card-section class="q-py-none">
                <q-separator color="grey-3"/>
            </q-card-section>

            <q-card-section class="q-py-none">
                <q-table
                    :rows="orders.data"
                    :columns="columns"
                    flat
                    class="text-grey-8"
                    hide-pagination
                    :pagination.sync="{rowsPerPage: requestData.rowsPerPage}"
                >
                    <template v-slot:header-cell="props">
                        <q-th :props="props">
                            <div class="adm-fw-700 adm-fs-16 cursor-pointer" @click="sortBy(props.col.name)">
                                {{ props.col.label }}

                                <q-icon
                                    :name="query.sort == 'desc' ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                                    v-if="props.col.name === query.orderBy"
                                />
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:header-cell-actions="props">
                        <q-th :props="props" auto-width>
                            <div class="flex flex-center adm-fw-700 adm-fs-16">
                                {{  props.col.label  }}
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:body-cell-status="props">
                        <q-td :props="props">
                            <q-chip text-color="white" :class="getClassStatus(props.row.status)">
                                {{ getStatusLabel(props.row.status) }}
                            </q-chip>
                        </q-td>
                    </template>

                    <template v-slot:body-cell-actions="props">
                        <q-td :props="props">
                            <div class="adm-fw-700 adm-fs-16">
                                <q-btn icon="more_vert" flat v-if="canOrderSituationUpdate || canOrderCancel">
                                    <q-menu >
                                        <q-list>
                                            <q-item
                                                v-if="canOrderSituationUpdate"
                                                clickable
                                                @click="edit(props.row.id)"
                                                class="text-blue-grey-10 flex items-center"
                                            >
                                                <q-icon name="edit" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Editar situação </div>
                                                </q-item-section>
                                            </q-item>

                                            <q-separator/>

                                            <q-item
                                                clickable
                                                @click="show(props.row.id)"
                                                class="text-blue-grey-10 flex items-center"
                                            >
                                                <q-icon name="visibility" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Vizualizar situação </div>
                                                </q-item-section>
                                            </q-item>

                                            <q-separator/>

                                            <q-item
                                                v-if="canOrderCancel"
                                                clickable
                                                @click="cancelRecurrence(props.row.id)"
                                                class="text-blue-grey-10 flex flex-center"
                                            >
                                                <q-icon name="close" size="xs"/>

                                                <q-item-section no-wrap>
                                                    <div class="q-ml-sm"> Cancelar recorrência </div>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </q-menu>
                                </q-btn>

                                <q-btn
                                    v-else
                                    @click="show(props.row.id)"
                                    class="text-blue-grey-10 flex flex-center text-no-wrap"
                                    flat
                                    no-caps
                                >
                                    <q-icon name="visibility" size="xs"/>
                                    <div class="q-ml-sm"> Visualizar </div>
                                </q-btn>
                            </div>
                        </q-td>
                    </template>
                </q-table>
            </q-card-section>

            <div class="row justify-end">
                <div class="row items-center text-grey">
                    Resultado por página

                    <q-select
                        :options="[5, 10, 15]"
                        v-model="requestData.rowsPerPage"
                        borderless
                        class="q-ml-md"
                        @update:model-value="submit"
                    />
                </div>

                <q-pagination
                    v-model="requestData.page"
                    :max="orders.meta.last_page"
                    @update:model-value="submit"
                    direction-links
                    boundary-links
                    color="grey"
                    input
                    icon-first="keyboard_double_arrow_left"
                    icon-last="keyboard_double_arrow_right"
                />
            </div>
        </q-card>
    </AuthenticatedLayout>
</template>
