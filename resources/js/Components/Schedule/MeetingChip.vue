<script setup>
    import { useForm } from '@inertiajs/inertia-vue3';
    import { computed } from 'vue';

    const props = defineProps({
        meeting: Object,
    });

    const show = () => useForm().get(route('admin.meeting.show', props.meeting.id));

    const label = computed(() => `${props.meeting.students_count}/${props.meeting.number_of_students} ${props.meeting.name}`)
</script>

<template>
    <q-chip
        icon="wifi"
        :label="label"
        class="adm-bg-purple q-px-sm q-py-xs"
        text-color="white"
        @click="show"
        clickable
    />
</template>
