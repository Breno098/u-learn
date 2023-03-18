<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';
    import draggable from 'vuedraggable'
    import { ref, computed } from 'vue'

    const $q = useQuasar()

    const props = defineProps({
        module: Object,
        course: Object,
        exam: Object,
        errors: Object,
        answerTypes: Object,
    });

    const form = useForm({
        id: props.exam.id,
        name: props.exam.name,
        questions: props.exam.questions,
        answer_file: props.exam.answer_file,
    });

     const dropZoneAnswerFile = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            form.answer_file = acceptFiles[0];

            if (rejectReasons.length > 0) {
                $q.notify({
                    message: 'Insira apenas uma arquivo',
                    position: 'center',
                })
            }
        },
        maxFiles: 1
    });

    const srcImage = (field) => {
        return field == null ? '' : (typeof field === 'object') ? URL.createObjectURL(field) : field;
    }

    const removeAnswerFile = () => form.answer_file = null;

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.module.exam.update", {
                module: props.module.id,
                exam: props.exam.id
            }), {
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: `Avaliação atualizada com sucesso`,
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })
                },
            })
    };

    const optionsAnswerTypes = computed(() => {
        let options = new Array();

        for (const key in props.answerTypes) {
            options.push({ label: props.answerTypes[key], value: key })
        }

        return options;
    })

    const goBack = () => useForm().get(route('admin.course.module.index', {
        course: props.course.id
    }));
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Editar avaliação ${exam.name} do módulo ${module.name}`" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-9 justify-start items-center">
                    <q-icon name="o_view_agenda" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Editar avaliação <b>{{ exam.name }}</b> do módulo <b>{{ module.name }}</b>
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
                            label="Título da avaliação"
                            :bottom-slots="Boolean(errors.name)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Questões
                        </div>
                    </div>

                    <div class="col-12">
                        <draggable
                            :list="form.questions"
                            item-key="index"
                        >
                            <template #item="{element, index}">
                                <div class="row q-col-gutter-lg q-mb-lg cursor-pointer">
                                    <div class="col-md-6 col-12">
                                        <q-input
                                            outlined
                                            v-model="element.title"
                                            :label="`Titulo da questão ${index + 1}`"
                                            disable
                                        >

                                        <template v-slot:before>
                                            <q-icon
                                                name="swipe_vertical"
                                                color="indigo"
                                                size="sm"
                                            />
                                        </template>
                                    </q-input>
                                    </div>

                                    <div class="col-md-6 offset-md-0 col-11 offset-1">
                                        <q-select
                                            :options="optionsAnswerTypes"
                                            outlined
                                            label="Tipo de questão"
                                            v-model="element.answer_type"
                                            emit-value
                                            map-options
                                            disable
                                        >
                                            <template v-slot:after>
                                                <q-btn-group flat>
                                                    <q-btn
                                                        color="negative"
                                                        flat
                                                        icon="close"
                                                        padding="xs"
                                                        @click="destroyQuestion(index)"
                                                    />
                                                    <q-btn
                                                        color="primary"
                                                        flat
                                                        icon="o_edit"
                                                        padding="xs"
                                                        @click="editQuestion(index, element)"
                                                    />
                                                </q-btn-group>
                                            </template>
                                        </q-select>
                                    </div>

                                    <div
                                        class="col-12 row"
                                        v-for="alternative in element.alternatives"
                                        v-if="element.answer_type === 'fechada'"
                                    >
                                        <div class="col-md-4 col-10 offset-2">
                                            <q-input
                                                outlined
                                                :label="`Opção resposta ${alternative.number}`"
                                                v-model="alternative.name"
                                                disable
                                            />
                                        </div>
                                    </div>

                                    <div class="offset-1 col-11 flex items-center">
                                        <div class="text-grey-8">
                                            Anexos vinculados:
                                        </div>

                                        <q-checkbox
                                            v-model="element.has_video"
                                            label="Vídeo"
                                            disable
                                            color="indigo"
                                            checked-icon="task_alt"
                                            unchecked-icon="radio_button_unchecked"
                                        />

                                        <q-checkbox
                                            v-model="element.has_audio"
                                            label="Áudio"
                                            disable
                                            color="indigo"
                                            checked-icon="task_alt"
                                            unchecked-icon="radio_button_unchecked"
                                        />

                                        <q-checkbox
                                            v-model="element.has_image"
                                            label="Imagem"
                                            disable
                                            color="indigo"
                                            checked-icon="task_alt"
                                            unchecked-icon="radio_button_unchecked"
                                        />
                                    </div>

                                    <div class="offset-1 col-11" v-if="element.has_video">
                                        <q-input
                                            outlined
                                            label="Link do vídeo"
                                            v-model="element.video"
                                            disable
                                        />
                                    </div>

                                    <div class="offset-1 col-11" v-if="element.has_audio">
                                        <q-input
                                            outlined
                                            label="Link do áudio"
                                            v-model="element.audio"
                                            disable
                                        />
                                    </div>

                                    <div
                                        class="offset-1 col-11 col-md-4"
                                        v-if="element.has_image"
                                    >
                                        <q-img
                                            :src="srcImage(element.image)"
                                            style="height: 400px"
                                            class="adm-br-16"
                                        >
                                            <div class="absolute-bottom text-subtitle2 row items-center">
                                                <q-icon name="o_photo" size="md" class="q-mr-md"/>
                                            </div>
                                        </q-img>
                                    </div>
                                </div>
                            </template>
                        </draggable>

                        <div class="flex flex-center">
                            <q-btn
                                color="primary"
                                no-caps
                                flat
                            >
                                <q-icon name="add" size="xs"/>

                                <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                    Adicionar questão
                                </div>
                            </q-btn>
                        </div>
                    </div>

<!--
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
                    </div> -->
                </div>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
