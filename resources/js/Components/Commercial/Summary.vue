<script setup>
import { computed } from 'vue';

    const props = defineProps({
        newStudentsInSelectedMonthCount: Number,
        newStudentsInTheMonthBeforeTheSelectedMonthCount: Number,
        totalActiveStudentsCount: Number,
        churnRateInSelectedMonth: Number,
        churnRateInTheMonthBeforeTheSelectedMonth: Number,
    });

    const differenceInChurnRates = computed(() => (props.churnRateInSelectedMonth ?? 0 - props.churnRateInTheMonthBeforeTheSelectedMonth ?? 0))

    const newStudentsPercentage = computed(() => {
        if (props.newStudentsInTheMonthBeforeTheSelectedMonthCount > 0) {
            return (props.newStudentsInSelectedMonthCount - props.newStudentsInTheMonthBeforeTheSelectedMonthCount) / props.newStudentsInTheMonthBeforeTheSelectedMonthCount * 100;
        }

        return 0;
    })
</script>

<template>
    <div class="row q-pb-lg q-col-gutter-lg">
            <!-- Novos alunos -->
            <div class="col-12 col-md-4">
                <q-card flat class="adm-br-16 q-pa-md full-height">
                    <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                        Novos alunos

                        <q-icon name="o_info" size="xs" class="cursor-pointer">
                            <q-tooltip
                                anchor="top middle"
                                self="bottom middle"
                                class="text-body2 bg-grey-10"
                            >
                                Alunos cadastrados no mês selecionado
                            </q-tooltip>
                        </q-icon>
                    </q-card-section>

                    <q-card-section class="row items-center justify-between">
                        <div class="adm-text-primary adm-lh-48 adm-fs-39 adm-fw-700">
                            {{ newStudentsInSelectedMonthCount }}
                        </div>

                        <div
                            class="adm-fs-23 row items-center"
                            :class="{
                                'adm-text-positive': newStudentsPercentage > 0,
                                'adm-text-negative': newStudentsPercentage < 0
                            }"
                        >
                            {{ newStudentsPercentage > 0 ? '+' : '' }}{{ newStudentsPercentage.toFixed(2) }}%

                            <q-icon name="arrow_upward" size="lg" v-if="newStudentsPercentage > 0"/>
                            <q-icon name="arrow_downward" size="lg" v-if="newStudentsPercentage < 0"/>
                        </div>
                    </q-card-section>

                    <q-card-section class="text-grey-8 q-py-none">
                       ({{ newStudentsInTheMonthBeforeTheSelectedMonthCount }} no mês anterior)
                    </q-card-section>
                </q-card>
            </div>

            <!-- Alunos ativos -->
            <div class="col-12 col-md-4">
                <q-card flat class="adm-br-16 q-pa-md full-height">
                    <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                        Alunos ativos

                        <q-icon name="o_info" size="xs" class="cursor-pointer">
                            <q-tooltip
                                anchor="top middle"
                                self="bottom middle"
                                class="text-body2 bg-grey-10"
                            >
                                Total de alunos ativos
                            </q-tooltip>
                        </q-icon>
                    </q-card-section>

                    <q-card-section class="row items-center justify-between">
                        <div class="adm-text-primary adm-lh-48 adm-fs-39 adm-fw-700">
                            {{ totalActiveStudentsCount }}
                        </div>
                    </q-card-section>
                </q-card>
            </div>

            <!-- Taxa Churn -->
            <div class="col-12 col-md-4">
                <q-card flat class="adm-br-16 q-pa-md full-height">
                    <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                        Taxa Churn

                        <q-icon name="o_info" size="xs" class="cursor-pointer">
                            <q-tooltip
                                anchor="top middle"
                                self="bottom middle"
                                class="text-body2 bg-grey-10"
                            >
                                Pedidos cancelados durante o mês selecionado dividido pelo total de pedidos criados no mesmo mês
                            </q-tooltip>
                        </q-icon>
                    </q-card-section>

                    <q-card-section class="row items-center justify-between">
                        <div class="adm-text-primary adm-lh-48 adm-fs-39 adm-fw-700">
                            {{ churnRateInSelectedMonth.toFixed(2) }}%
                        </div>

                        <div
                            class="adm-fs-23 row items-center"
                            :class="{
                                'adm-text-positive': differenceInChurnRates < 0,
                                'adm-text-negative': differenceInChurnRates > 0
                            }"
                        >
                            {{ differenceInChurnRates > 0 ? '+' : '' }}{{ differenceInChurnRates.toFixed(2) }}%

                            <q-icon name="arrow_upward" size="lg" v-if="differenceInChurnRates > 0"/>
                            <q-icon name="arrow_downward" size="lg" v-if="differenceInChurnRates < 0"/>
                        </div>
                    </q-card-section>

                    <q-card-section class="text-grey-8 q-py-none">
                       ({{ churnRateInTheMonthBeforeTheSelectedMonth.toFixed(2) }}% no mês anterior)
                    </q-card-section>
                </q-card>
            </div>
        </div>
</template>
