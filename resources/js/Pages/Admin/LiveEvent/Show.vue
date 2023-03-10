<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue'

    const props = defineProps({
        liveEvent: Object,
        errors: Object,
        types: Object,
        responsible: Array,
        students: Array,
        groups: Array,
        campaigns: Array,
        linkableTypes: Object,
        contents: Array,
        seasonsOrChapters: Array,
    });

    const form = useForm({
        id: props.liveEvent.id,
        name: props.liveEvent.name,
        description: props.liveEvent.description,
        event_type: props.liveEvent.event_type,
        type: props.liveEvent.type,
        embed: props.liveEvent.embed,
        responsible_id: props.liveEvent.responsible_id,
        number_of_students : props.liveEvent.number_of_students,
        start_at: props.liveEvent.start_at,
        end_at: props.liveEvent.end_at,
        group_ids: props.liveEvent.group_ids,
        student_ids: props.liveEvent.student_ids,
        campaign_ids: props.liveEvent.campaign_ids,
        materials: props.liveEvent.materials,
        image: props.liveEvent.image,
        linkable_id: props.liveEvent.linkable_id,
        linkable_type: props.liveEvent.linkable_type,
        course_id: props.liveEvent.course_id,
        has_link_with_content: props.liveEvent.has_link_with_content,
    });

    const imageSrc = computed(() => {
        if (! form.image)
            return '';

        return (typeof form.image === 'object') ? URL.createObjectURL(form.image) : form.image;
    });

    const imageForDoc = 'https://i.pinimg.com/564x/04/54/7c/04547c2b354abb70a85ed8a2d1b33e5f.jpg';

    const optionsStudents = ref(props.students)

    const optionsResponsible = ref(props.responsible)

    const optionsGroups = ref(props.groups)

    const optionsCampaigns = ref(props.campaigns)

    const optionsTypes = computed(() => {
        let options = new Array();

        for (const key in props.types) {
            options.push({ label: props.types[key], value: key })
        }

        return options;
    })

    const optionsContents = ref(props.contents)

    const labelOptionsSeasonsOrChapters = computed(() => {
        if (form.linkable_type === 'season') {
            return 'Temporada'
        }

        if (form.linkable_type === 'chapter') {
            return 'Episódio';
        }

        return '';
    })

    const goBack = () => useForm().get(route('admin.live-event.index'))

    const optionsSeasonsOrChapters = ref(props.seasonsOrChapters)

    const optionsLinkableTypes = computed(() => {
        let options = new Array();

        for (const key in props.linkableTypes) {
            options.push({ label: props.linkableTypes[key], value: key })
        }

        return options;
    })
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Evento ao vivo | Visualizar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Visualizar evento ao vivo </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Eventos ao vivo" class="text-grey"/>
                    <q-breadcrumbs-el label="Visualizar evento ao vivo" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
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
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Informações
                    </div>
                </div>

               <div class="col-12 col-xl-6">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do evento"
                        disable
                    />
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.start_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de inicio"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de término"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.event_type"
                        label="Tipo"
                        disable
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição do evento"
                        disable
                    />
                </div>

                <div class="col-12">
                    <div class="q-gutter-sm row items-center">
                        <div> Participação </div>

                        <q-radio
                            v-model="form.type"
                            :val="optionType.value"
                            :label="optionType.label"
                            v-for="optionType in optionsTypes"
                            disable
                        />
                    </div>
                </div>

                <div class="col-12 col-md-6" v-if="form.type == 'individual'">
                    <q-select
                        :options="optionsStudents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.student_ids"
                        label="Aluno"
                        multiple
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

                <div class="col-12 col-md-6" v-if="form.type == 'grupo'">
                    <q-select
                        :options="optionsGroups"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.group_ids"
                        label="Grupos"
                        multiple
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

                <div class="col-12 col-md-6">
                    <q-select
                        :options="optionsResponsible"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.responsible_id"
                        label="Responsável pelo evento"
                        clearable
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

                <div class="col-12 col-md-6">
                    <q-select
                        :options="optionsCampaigns"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.campaign_ids"
                        label="Campanhas"
                        multiple
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

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.embed"
                        label="Link embed"
                        disable
                    />
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.has_link_with_content"
                        label="Vincular encontro à um conteúdo"
                        disable
                    />
                </div>

                <div class="col-12 col-md-5" v-if="form.has_link_with_content">
                    <q-select
                        :options="optionsContents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.course_id"
                        label="Vincular ao conteúdo"
                        disable
                    >
                        <template v-slot:selected-item="scope">
                            <q-chip
                                class="adm-chip-primary q-my-none"
                                :label="scope.opt.name"
                                dense
                            />
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-4" v-if="form.has_link_with_content">
                    <q-select
                        v-if="form.course_id"
                        :options="optionsLinkableTypes"
                        emit-value
                        map-options
                        outlined
                        v-model="form.linkable_type"
                        label="Momento de vinculação do evento"
                        disable
                    />
                </div>

                <div class="col-12 col-md-3" v-if="form.has_link_with_content">
                    <q-select
                        v-if="form.linkable_type !== 'content'"
                        :options="optionsSeasonsOrChapters"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.linkable_id"
                        :label="labelOptionsSeasonsOrChapters"
                        disable
                    >
                        <template v-slot:selected-item="scope">
                            <q-chip
                                class="adm-chip-primary q-my-none"
                                :label="scope.opt.name"
                                dense
                            />
                        </template>
                    </q-select>
                </div>

                <div class="col-12 items-center q-mt-xs" v-if="imageSrc">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Imagem de capa
                    </div>
                </div>

                <div class="col-12 col-md-12" v-if="imageSrc">
                    <q-img
                        :src="imageSrc"
                        style="height: 400px"
                        class="adm-br-16"
                    >
                        <div class="absolute-bottom text-subtitle2 row items-center">
                            <q-icon name="attach_file" size="md" class="rotate-225 q-mr-md"/>
                        </div>
                    </q-img>
                </div>

                <div class="col-12 items-center" v-if="form.materials.length">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Material
                    </div>
                </div>

                <div class="col-12 col-md-3" v-for="(material, index) in form.materials" :key="`material-${index}}}`">
                    <q-img
                        :src="material.is_image ? material.link : imageForDoc"
                        style="height: 240px"
                        class="adm-br-16"
                    >
                        <div class="absolute-bottom text-subtitle2 row items-center">
                            <q-icon name="attach_file" size="md" class="rotate-225 q-mr-md"/>

                            <div class="column">
                                <span class="text-caption">
                                    {{ material.name.length > 25 ? material.name.slice(0, 25) + '...' : material.name }}
                                </span>

                                <span class="text-caption">
                                    ({{ material.size ?? 0 }} kb)
                                </span>
                            </div>
                        </div>
                    </q-img>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
