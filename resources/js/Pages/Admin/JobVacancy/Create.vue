<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { ref } from 'vue'

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
        students: Array,
        groups: Array,
        displayOptions: Array
    });

    const form = useForm({
        id: null,
        title: null,
        description: null,
        link: null,
        display_to: null,
        start_at: null,
        end_at: null,
        groups: [],
        students: [],
    });

    const submit = () => {
        form.post(route("admin.job-vacancy.store"), {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: 'Vaga cadastrada com sucesso',
                    position: 'top',
                })
            },
        })
    };

    const optionsStudents = ref(props.students)

    const filterStudents = (val, update, abort) => {
        update(() => {
            const needle = val.toLowerCase()
            optionsStudents.value = props.students.filter(s => s.name.toLowerCase().indexOf(needle) > -1)
        })
    }

    const optionsGroups = ref(props.groups)

    const filterGroups = (val, update, abort) => {
        update(() => {
            const needle = val.toLowerCase()
            optionsGroups.value = props.groups.filter(s => s.name.toLowerCase().indexOf(needle) > -1)
        })
    }

    const goBack = () => useForm().get(route('admin.job-vacancy.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Vaga | Adicionar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Novo vaga </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Vagas" class="text-grey"/>
                    <q-breadcrumbs-el label="Novo vaga" class="text-primary" />
                </q-breadcrumbs>
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
                        :bottom-slots="Boolean(errors.title)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.title }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-select
                        :options="displayOptions"
                        outlined
                        v-model="form.display_to"
                        label="Mostrar para"
                        :bottom-slots="Boolean(errors.display_to)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.display_to }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição"
                        :bottom-slots="Boolean(errors.description)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.description }} </div>
                        </template>
                    </q-input>
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
                        :bottom-slots="Boolean(errors.students)"
                        multiple
                        clearable
                        use-chips
                        use-input
                        @filter="filterStudents"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.students }} </div>
                        </template>

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
                        :bottom-slots="Boolean(errors.groups)"
                        multiple
                        clearable
                        use-chips
                        use-input
                        @filter="filterGroups"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.groups }} </div>
                        </template>

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
                        :bottom-slots="Boolean(errors.start_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.start_at }} </div>
                        </template>

                        <q-popup-proxy>
                            <q-date
                                v-model="form.start_at"
                                mask="DD/MM/YYYY"
                                square
                            >
                                <div class="row items-center justify-end">
                                    <q-btn
                                        label="Ok"
                                        color="primary"
                                        flat
                                        v-close-popup
                                    />
                                </div>
                            </q-date>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/####"
                        label="Data final"
                        :bottom-slots="Boolean(errors.end_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.end_at }} </div>
                        </template>

                        <q-popup-proxy>
                            <q-date
                                v-model="form.end_at"
                                mask="DD/MM/YYYY"
                                square
                            >
                                <div class="row items-center justify-end">
                                    <q-btn
                                        label="Ok"
                                        color="primary"
                                        flat
                                        v-close-popup
                                    />
                                </div>
                            </q-date>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.link"
                        label="Link"
                        :bottom-slots="Boolean(errors.link)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.link }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 flex justify-end items-center">
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

                    <q-btn
                        color="primary"
                        rounded
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                    >
                        <q-icon name="add" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Criar vaga
                        </div>
                    </q-btn>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
