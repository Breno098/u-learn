<script setup>
    import { ref } from 'vue'
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';

    const props = defineProps({
        jobVacancy: Object,
        errors: Object,
        students: Array,
        groups: Array,
        displayOptions: Array
    });

    const form = useForm({
        id: props.jobVacancy.id,
        title: props.jobVacancy.title,
        description: props.jobVacancy.description,
        link: props.jobVacancy.link,
        display_to: props.jobVacancy.display_to,
        start_at: props.jobVacancy.start_at,
        end_at: props.jobVacancy.end_at,
        groups: props.jobVacancy.groups,
        students: props.jobVacancy.students,
    });

    const optionsStudents = ref(props.students)

    const optionsGroups = ref(props.groups)

    const goBack = () => useForm().get(route('admin.job-vacancy.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Vaga | Adicionar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Visualizar vaga </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Vagas" class="text-grey"/>
                    <q-breadcrumbs-el label="Visualizar vaga" class="text-primary" />
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

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.title"
                        label="Título"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-select
                        :options="displayOptions"
                        outlined
                        v-model="form.display_to"
                        label="Mostrar para"
                        disable
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição"
                        type="textarea"
                        disable
                    />
                </div>

                <div class="col-12" v-if="form.display_to == 'Usuário'">
                    <q-select
                        :options="optionsStudents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.students"
                        label="Alunos"
                        multiple
                        clearable
                        use-chips
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

                <div class="col-12" v-if="form.display_to == 'Grupo'">
                    <q-select
                        :options="optionsGroups"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.groups"
                        label="Grupos"
                        multiple
                        clearable
                        use-chips
                        use-input
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
                        v-model="form.start_at"
                        mask="##/##/####"
                        label="Data de inicio"
                        placeholder="dia/mês/ano"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/####"
                        label="Data final"
                        placeholder="dia/mês/ano"
                        disable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today" />
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.link"
                        label="Link"
                        disable
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
