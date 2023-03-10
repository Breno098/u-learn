<script setup>
    import { computed } from 'vue';
    import { date } from 'quasar';
    import LiveEventChip from '@/Components/Schedule/LiveEventChip.vue';
    import MeetingChip from '@/Components/Schedule/MeetingChip.vue';

    const props = defineProps({
        dateLiveEvent: String,
        liveEventCount: {
            type: Number,
            default: 0
        },
        meetingCount: {
            type: Number,
            default: 0
        },
        nextEventDate: String,
        nextEvent: Object
    });

    const dateLiveEvent = computed(() => {
        return props.nextEventDate ? date.formatDate(new Date(props.nextEventDate), 'dddd, DD [de] MMMM') : null;
    })
</script>

<template>
     <div class="row q-pb-lg q-col-gutter-lg">
        <!-- A receber diário -->
        <div class="col-12 col-md-6">
            <q-card flat class="adm-br-16 q-pa-sm full-height">
                <!-- <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-pt-xs q-pb-none">
                    Evento mais próximo
                </q-card-section> -->

                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    Evento mais próximo

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Evento ao vivo ou encontro mais próximo
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="row items-stretch justify-between">
                    <div class="col-12 col-md-9 adm-text-primary adm-lh-48 adm-fs-35 adm-fw-700">
                        {{ dateLiveEvent }}
                    </div>

                    <div class="col-12 col-md-3 self-end">
                        <LiveEventChip
                            :liveEvent="nextEvent"
                            v-if="nextEvent.type === 'liveEvent'"
                            class="fit"
                        />

                        <MeetingChip
                            :meeting="nextEvent"
                            v-if="nextEvent.type === 'meeting'"
                            class="fit"
                        />
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <!-- Total de Eventos ao vivo -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    Total de Eventos ao vivo

                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Eventos ao vivo que ainda irão acontecer, ou seja, data e hora de início maiores do que data e hora atual
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="flex flex-center adm-text-primary adm-lh-48 adm-fs-39 adm-fw-700 fit">
                    {{ meetingCount }}
                </q-card-section>
            </q-card>
        </div>

        <!-- Total de Encontros -->
        <div class="col-12 col-md-3">
            <q-card flat class="adm-br-16 q-pa-md full-height">
                <q-card-section class="adm-text-primary-dark adm-fs-16 adm-lh-24 adm-fw-700 q-py-none flex items-center justify-between">
                    Total de Encontros
                    <q-icon name="o_info" size="xs" class="cursor-pointer">
                        <q-tooltip
                            anchor="top middle"
                            self="bottom middle"
                            class="text-body2 bg-grey-10"
                        >
                            Encontros que ainda irão acontecer, ou seja, data e hora de início maiores do que data e hora atual
                        </q-tooltip>
                    </q-icon>
                </q-card-section>

                <q-card-section class="flex flex-center adm-text-purple adm-lh-48 adm-fs-39 adm-fw-700 fit">
                    {{ liveEventCount }}
                </q-card-section>
            </q-card>
        </div>
    </div>
</template>
