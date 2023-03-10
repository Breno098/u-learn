<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue'

    import LiveEventChip from '@/Components/Schedule/LiveEventChip.vue';
    import MeetingChip from '@/Components/Schedule/MeetingChip.vue';
    import SummarySchedule from '@/Components/Schedule/Summary.vue';

    import { QCalendarMonth, today } from '@quasar/quasar-ui-qcalendar/src/index.js'

    const props = defineProps({
        errors: Object,
        liveEventCount: Number,
        meetingCount: Number,
        nextEventDate: String,
        nextEvent: Object,
        liveEvents: Array,
        meetings: Array,

        date: String,
        dateMonthName: String,
        dateMonth: Number,
        dateYear: Number,
    });

    const eventsMap = computed(() => {
        const map = {}

        props.liveEvents.forEach(event => {
            (map[ event.start_at ] = (map[ event.start_at ] || [])).push(event)
        })

        props.meetings.forEach(event => {
            (map[ event.start_at ] = (map[ event.start_at ] || [])).push(event)
        })

        return map;
    })

    const selectedDate = ref(props.date ?? today());

    function onNextMonth() {
        let month = props.dateMonth + 1;
        let year = props.dateYear;

        if (month == 13) {
            month = 1;
            year = props.dateYear + 1;
        }

        useForm({month: month, year: year}).get(route('admin.schedule.index'));
    }

    function onPrevMonth() {
        let month = props.dateMonth - 1;
        let year = props.dateYear;

        if (month == 0) {
            month = 12;
            year = props.dateYear - 1;
        }

        useForm({month: month, year: year}).get(route('admin.schedule.index'));
    }

    function onNextYear() {
        useForm({month: props.dateMonth, year: props.dateYear + 1 }).get(route('admin.schedule.index'));
    }

    function onPrevYear() {
        useForm({month: props.dateMonth, year: props.dateYear - 1}).get(route('admin.schedule.index'));
    }
</script>

<template>
    <Head title="Agenda"/>

    <AuthenticatedLayout>
        <div class="row q-pb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Agenda </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Agenda" class="text-primary" />
                </q-breadcrumbs>
            </div>
        </div>

        <SummarySchedule
            :liveEventCount="liveEventCount"
            :meetingCount="meetingCount"
            :nextEventDate="nextEventDate"
            :nextEvent="nextEvent"
        />

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg q-mb-lg">
                <div class="col-12 col-md-5 row justify-between items-center">
                    <q-btn
                        icon="chevron_left"
                        color="primary"
                        size="lg"
                        rounded
                        flat
                        @click="onPrevMonth"
                    />

                    <div class="text-grey-8 adm-fw-500 adm-lh-24 adm-fs-19">
                        {{ dateMonthName }}
                    </div>

                    <q-btn
                        icon="chevron_right"
                        color="primary"
                        flat
                        rounded
                        size="lg"
                        @click="onNextMonth"
                    />
                </div>

                <div class="col-md-2">

                </div>

                <div class="col-12 col-md-5 row justify-between items-center">
                    <q-btn
                        icon="chevron_left"
                        color="primary"
                        flat
                        rounded
                        size="lg"
                        @click="onPrevYear"
                    />

                    <div class="text-grey-8 adm-fw-500 adm-lh-24 adm-fs-19">
                        {{ dateYear }}
                    </div>

                    <q-btn
                        icon="chevron_right"
                        color="primary"
                        flat
                        rounded
                        size="lg"
                        @click="onNextYear"
                    />
                </div>
            </div>

            <QCalendarMonth
                day-min-height="160"
                locale="pt-BR"
                date-align="right"
                month-label-size="md"
                bordered
                no-active-date
                v-model="selectedDate"
            >
                <template #day="{ scope: { timestamp } }">
                    <div
                        v-for="event, index in eventsMap[timestamp.date]"
                        :key="event.id"
                        class="q-mr-md q-ml-sm q-my-xs"
                    >
                        <LiveEventChip
                            :liveEvent="event"
                            v-if="event.type === 'liveEvent'"
                            class="fit"
                        />

                        <MeetingChip
                            :meeting="event"
                            v-if="event.type === 'meeting'"
                            class="fit"
                        />
                    </div>
                </template>
            </QCalendarMonth>
        </div>
    </AuthenticatedLayout>
</template>
