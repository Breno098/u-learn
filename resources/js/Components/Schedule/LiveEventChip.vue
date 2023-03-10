<script setup>
    import { useForm } from '@inertiajs/inertia-vue3';
    import { computed } from 'vue';

    const props = defineProps({
        liveEvent: Object,
    });

    const show = () => useForm().get(route('admin.live-event.show', props.liveEvent.id));

    const label = computed(() => `${props.liveEvent.students_count}/${props.liveEvent.number_of_students} ${props.liveEvent.name}`)
</script>

<template>
    <q-chip
        icon="chat_bubble_outline"
        :label="label"
        class="adm-bg-primary q-px-sm q-py-xs"
        text-color="white"
        @click="show"
        clickable
    />
</template>
