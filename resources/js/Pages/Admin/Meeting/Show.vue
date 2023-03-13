 <script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue'

    const props = defineProps({
        meeting: Object,
        errors: Object,
        types: Array,
        teachers: Array,
        students: Array,
        groups: Array,
    });

    const imageForDoc = 'https://i.pinimg.com/564x/04/54/7c/04547c2b354abb70a85ed8a2d1b33e5f.jpg';

    const form = useForm({
        id: props.meeting.id,
        name: props.meeting.name,
        type: props.meeting.type,
        student_id: props.meeting.student_id,
        groups: props.meeting.groups,
        teacher_id: props.meeting.teacher_id,
        number_of_students: props.meeting.number_of_students,
        start_at: props.meeting.start_at,
        end_at: props.meeting.end_at,
        live_offer: props.meeting.live_offer,
        name_offer: props.meeting.name_offer,
        description_offer: props.meeting.description_offer,
        embed_offer: props.meeting.embed_offer,
        materials: props.meeting.materials
    });

    const optionsTeachers = ref(props.teachers)

    const goBack = () => useForm().get(route('admin.meeting.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Encontro | Visualizar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Visualizar encontro </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Encontros" class="text-grey"/>
                    <q-breadcrumbs-el label="Visualizar encontro" class="text-primary" />
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

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do encontro"
                        disable
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-select
                        :options="types"
                        outlined
                        v-model="form.type"
                        label="Tipo"
                        disable
                    />
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model.number="form.number_of_students"
                        label="Quantidade de alunos"
                        type="number"
                        disable
                   />
                </div>

                <div class="col-12 col-md-4">
                    <q-select
                        :options="optionsTeachers"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.teacher_id"
                        label="Professor"
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

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="form.start_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de inicio"
                        placeholder="dia/mês/ano hora:min"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-4">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de término"
                        placeholder="dia/mês/ano hora:min"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
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

                            <q-btn
                                color="white"
                                class="absolute inset-shadow-down"
                                style="top: 8px; right: 8px"
                                flat
                                icon="close"
                                size="md"
                                @click="removeMaterial(index)"
                            />
                        </div>
                    </q-img>
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.live_offer"
                        label="Oferta ao vivo"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6" v-if="form.live_offer">
                    <q-input
                        outlined
                        v-model="form.name_offer"
                        label="Nome da oferta"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6" v-if="form.live_offer">
                    <q-input
                        outlined
                        v-model="form.embed_offer"
                        label="Link da oferta"
                        disable
                    />
                </div>

                <div class="col-12" v-if="form.live_offer">
                    <q-input
                        outlined
                        v-model="form.description_offer"
                        label="Descrição da oferta"
                        disable
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
