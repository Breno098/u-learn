<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        newStudentsThisMonthCount: Number,
        newStudentsLastMonthCount: Number,
        newStudentsPercentage: Number,
        totalActiveStudentsCount: Number,

        valueOfOrdersCanceledInSelectedMonth: Number,
        valueOfOrdersCanceledInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersExpiredInSelectedMonth: Number,
        valueOfOrdersExpiredInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersToBeReceivedInSelectedMonth: Number,
        valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth: Number,
        valueOfOrdersToBeReceivedToday: Number,
        valueOfOrdersToBeReceivedYesterday: Number,
    });

    const valueOfOrdersToBeReceivedByDayPercentage = computed(() => {
        if (props.valueOfOrdersToBeReceivedYesterday > 0) {
            return (props.valueOfOrdersToBeReceivedToday - props.valueOfOrdersToBeReceivedYesterday) / props.valueOfOrdersToBeReceivedYesterday * 100;
        }

        return 0;
    })

    const valueOfOrdersToBeReceivedByMonthPercentage = computed(() => {
        if (props.valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth > 0) {
            return (props.valueOfOrdersToBeReceivedInSelectedMonth - props.valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth) / props.valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth * 100;
        }

        return 0;
    })

    const valueOfOrdersCanceledPercentage = computed(() => {
        if (props.valueOfOrdersCanceledInTheMonthBeforeSelectedMonth > 0) {
            return (props.valueOfOrdersCanceledInSelectedMonth - props.valueOfOrdersCanceledInTheMonthBeforeSelectedMonth) / props.valueOfOrdersCanceledInTheMonthBeforeSelectedMonth * 100;
        }

        return 0;
    })

    const valueOfOrdersExpiredPercentage = computed(() => {
        if (props.valueOfOrdersExpiredInTheMonthBeforeSelectedMonth > 0) {
            return (props.valueOfOrdersExpiredInSelectedMonth - props.valueOfOrdersExpiredInTheMonthBeforeSelectedMonth) / props.valueOfOrdersExpiredInTheMonthBeforeSelectedMonth * 100;
        }

        return 0;
    })
</script>

<template>
     <div class="row q-pb-lg q-col-gutter-lg">
        <!-- A receber diário -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    A receber diário

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Soma dos valores de pedidos com vencimento hoje
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="row items-center justify-between">
                    <div class="adm-text-primary adm-lh-48 adm-fs-25 adm-fw-700">
                        R$ {{ valueOfOrdersToBeReceivedToday.toLocaleString('pt-br', {minimumFractionDigits: 2}) }}
                    </div>

                    <div
                        class="adm-fs-23 row items-center"
                        :class="{
                            'adm-text-positive': valueOfOrdersToBeReceivedByDayPercentage > 0,
                            'adm-text-negative': valueOfOrdersToBeReceivedByDayPercentage < 0,
                        }"
                    >
                        {{ valueOfOrdersToBeReceivedByDayPercentage > 0 ? '+' : '' }}{{ valueOfOrdersToBeReceivedByDayPercentage.toFixed(2) }}%

                        <q-icon name="arrow_upward" size="lg" v-if="valueOfOrdersToBeReceivedByDayPercentage > 0"/>
                        <q-icon name="arrow_downward" size="lg" v-if="valueOfOrdersToBeReceivedByDayPercentage < 0"/>
                    </div>
                </q-card-section>

                <q-card-section class="text-grey-8 q-py-none">
                    (R$ {{ valueOfOrdersToBeReceivedYesterday.toLocaleString('pt-br', {minimumFractionDigits: 2}) }} ontem)
                </q-card-section>
            </q-card>
        </div>

        <!-- A receber mensal -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    A receber mensal

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Soma dos valores de pedidos com vencimento no mês selecionado
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="row items-center justify-between">
                    <div class="adm-text-primary adm-lh-48 adm-fs-25 adm-fw-700">
                        R$ {{ valueOfOrdersToBeReceivedInSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }}
                    </div>

                    <div
                        class="adm-fs-23 row items-center"
                        :class="{
                            'adm-text-positive': valueOfOrdersToBeReceivedByMonthPercentage > 0,
                            'adm-text-negative': valueOfOrdersToBeReceivedByMonthPercentage < 0,
                        }"
                    >
                        {{ valueOfOrdersToBeReceivedByMonthPercentage > 0 ? '+' : '' }}{{ valueOfOrdersToBeReceivedByMonthPercentage.toFixed(2) }}%

                        <q-icon name="arrow_upward" size="lg" v-if="valueOfOrdersToBeReceivedByMonthPercentage > 0"/>
                        <q-icon name="arrow_downward" size="lg" v-if="valueOfOrdersToBeReceivedByMonthPercentage < 0"/>
                    </div>
                </q-card-section>

                <q-card-section class="text-grey-8 q-py-none">
                    (R$ {{ valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }} no mês anterior)
                </q-card-section>
            </q-card>
        </div>

        <!-- Contratos vencidos -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    Contratos vencidos

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Soma dos valores de pedidos com status vencido com vencimento no mês selecionado
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="row items-center justify-between">
                    <div class="adm-text-primary adm-lh-48 adm-fs-25 adm-fw-700">
                        R$ {{ valueOfOrdersExpiredInSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }}
                    </div>

                    <div
                        class="adm-fs-23 row items-center"
                        :class="{
                            'adm-text-positive': valueOfOrdersExpiredPercentage < 0,
                            'adm-text-negative': valueOfOrdersExpiredPercentage > 0,
                        }"
                    >
                        {{ valueOfOrdersExpiredPercentage > 0 ? '+' : '' }}{{ valueOfOrdersExpiredPercentage.toFixed(2) }}%

                        <q-icon name="arrow_upward" size="lg" v-if="valueOfOrdersExpiredPercentage > 0"/>
                        <q-icon name="arrow_downward" size="lg" v-if="valueOfOrdersExpiredPercentage < 0"/>
                    </div>
                </q-card-section>

                <q-card-section class="text-grey-8 q-py-none">
                    (R$ {{ valueOfOrdersExpiredInTheMonthBeforeSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }} no mês anterior)
                </q-card-section>
            </q-card>
        </div>

        <!-- Contratos cancelados -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    Contratos cancelados

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Soma dos valores de pedidos com status cancelado com vencimento no mês selecionado
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="row items-center justify-between">
                    <div class="adm-text-primary adm-lh-48 adm-fs-25 adm-fw-700">
                        R$ {{ valueOfOrdersCanceledInSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }}
                    </div>

                    <div
                        class="adm-fs-23 row items-center"
                        :class="{
                            'adm-text-positive': valueOfOrdersCanceledPercentage < 0,
                            'adm-text-negative': valueOfOrdersCanceledPercentage > 0,
                        }"
                    >
                        {{ valueOfOrdersCanceledPercentage > 0 ? '+' : '' }}{{ valueOfOrdersCanceledPercentage.toFixed(2) }}%

                        <q-icon name="arrow_upward" size="lg" v-if="valueOfOrdersCanceledPercentage > 0"/>
                        <q-icon name="arrow_downward" size="lg" v-if="valueOfOrdersCanceledPercentage < 0"/>
                    </div>
                </q-card-section>

                <q-card-section class="text-grey-8 q-py-none">
                    (R$ {{ valueOfOrdersCanceledInTheMonthBeforeSelectedMonth.toLocaleString('pt-br', {minimumFractionDigits: 2}) }} no mês anterior)
                </q-card-section>
            </q-card>
        </div>
    </div>
</template>
