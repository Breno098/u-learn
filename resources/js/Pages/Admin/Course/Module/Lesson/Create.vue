<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        module: Object,
        course: Object,
        lesson: Object,
        errors: Object,
        numbers: Array
    });

    const form = useForm({
        id: null,
        name: null,
        description: null,
        number: null,
        duration: null,
        video: null,
        wallpaper: null,
        can_comments: true,
    });

    const dropZoneWallpaper = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            } else {
                form.wallpaper = acceptFiles[0];
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const srcImage = (field) => {
        return field == null ? '' : (typeof field === 'object') ? URL.createObjectURL(field) : field;
    }

    const removeImage = () => form.wallpaper = null;

    const submit = () => {
        form.post(route("admin.course.module.lesson.store", {
            course: props.course.id,
            module: props.module.id,
        }), {
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: `Aula ${form.name} cadastrada com sucesso`,
                        icon: { name: 'check', color: 'green' },
                        timeout: 3000
                    }
                })
            },
        })
    };

    const goBack = () => useForm().get(route('admin.course.module.index', {
        course: props.course.id
    }));
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Nova aula para o módulo ${module.name}`" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-9 justify-start items-center">
                    <q-icon name="o_view_agenda" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Nova aula para o módulo <b>{{ module.name }}</b>
                    </div>
                </div>

                <div class="col-12 col-md-3 flex justify-end items-center">
                    <q-btn
                        color="indigo"
                        no-caps
                        @click="submit"
                        :disabled="form.processing"
                        icon="check"
                        label="Salvar"
                    />
                </div>
            </q-card-section>
        </q-card>

        <q-card flat>
            <q-card-section class="q-pb-none q-py-lg">
                <q-btn
                    dense
                    color="indigo"
                    class="absolute inset-shadow-down"
                    icon="chevron_left"
                    style="top: 0; left: 12px; transform: translateY(-50%);"
                    label="Voltar"
                    no-caps
                    @click="goBack"
                />

                <div class="row q-col-gutter-lg">
                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Informações
                        </div>
                    </div>

                    <div class="col-12 col-md-12">
                        <q-input
                            outlined
                            v-model="form.name"
                            label="Nome"
                            :bottom-slots="Boolean(errors.name)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-12">
                        <q-input
                            outlined
                            v-model="form.video"
                            label="Link para Vídeo"
                            :bottom-slots="Boolean(errors.video)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.video }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12">
                        <q-input
                            outlined
                            v-model="form.description"
                            label="Descrição"
                            :bottom-slots="Boolean(errors.description)"
                            type="textarea"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.description }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12">
                        <q-checkbox
                            v-model="form.can_comments"
                            label="Alunos podem comentar"
                            color="indigo"
                            checked-icon="task_alt"
                            unchecked-icon="radio_button_unchecked"
                        />
                    </div>

                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Capa
                        </div>
                    </div>

                    <div class="col-12" v-if="form.wallpaper">
                        <q-img
                            :src="srcImage(form.wallpaper)"
                            style="height: 400px"
                            class="adm-br-5"
                        >
                            <div class="absolute-bottom text-subtitle2 row items-center">
                                <q-btn
                                    color="indigo"
                                    class="absolute inset-shadow-down"
                                    icon="insert_link"
                                    v-bind="dropZoneWallpaper.getRootProps()"
                                    style="top: 0; right: 80px; transform: translateY(-50%);"
                                >
                                    <q-tooltip>
                                        Clique para alterar imagem
                                    </q-tooltip>
                                </q-btn>

                                <q-btn
                                    color="red"
                                    class="absolute inset-shadow-down"
                                    icon="o_hide_image"
                                    @click="removeImage"
                                    style="top: 0; right: 12px; transform: translateY(-50%);"
                                >
                                    <q-tooltip>
                                        Clique para remover imagem
                                    </q-tooltip>
                                </q-btn>

                                <input v-bind="dropZoneWallpaper.getInputProps()"/>
                            </div>
                        </q-img>
                    </div>

                    <div class="col-12" v-else>
                        <div
                            v-bind="dropZoneWallpaper.getRootProps()"
                            class="column flex-center q-py-lg text-grey adm-border-dashed-indigo adm-br-5"
                        >
                            <input v-bind="dropZoneWallpaper.getInputProps()"/>

                            <q-icon name="o_photo" size="md"/>

                            <div class="q-mt-sm">
                                Clique aqui ou arraste sua imagem
                            </div>
                        </div>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
