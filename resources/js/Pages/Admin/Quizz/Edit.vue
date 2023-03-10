<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { Inertia } from '@inertiajs/inertia';
    import { ref, computed } from 'vue'
    import DialogConfirm from '@/Components/DialogConfirm.vue';
    import draggable from 'vuedraggable'
    import { useDropzone } from "vue3-dropzone";

    const $q = useQuasar()

    const props = defineProps({
        quizz: Object,
        errors: Object,
        answerTypes: Object,
        linkableTypes: Object,
        contents: Array,
        seasonsOrChapters: Array,
    });

    const form = useForm({
        id: props.quizz.id,
        name: props.quizz.name,
        description: props.quizz.description,
        questions: props.quizz.questions,
        course_id: props.quizz.course_id,
        linkable_id: props.quizz.linkable_id,
        linkable_type: props.quizz.linkable_type,
        answer_file: props.quizz.answer_file,
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

    const imageForDoc = 'https://i.pinimg.com/564x/04/54/7c/04547c2b354abb70a85ed8a2d1b33e5f.jpg';

    const answerFileSrc = computed(() => {
        if (! form.answer_file)
            return '';

        if (typeof form.answer_file === 'object')
            return form.answer_file.type.includes('image') ? URL.createObjectURL(form.answer_file) : imageForDoc;

        return form.answer_file;
    });

    const removeAnswerFile = () => form.answer_file = null;

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.quizz.update", form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Quizz atualizado com sucesso',
                        position: 'top',
                    })

                    form.questions = props.quizz.questions;
                },
            })
    };

    function destroy() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir quizz?',
                message: 'Tem certeza que deseja excluir essa quizz?',
            },
        }).onOk(() => {
            Inertia.delete(route('admin.quizz.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Quizz excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    const optionsContents = ref(props.contents)

    const filterContents = (val, update, abort) => {
        update(() => optionsContents.value = props.contents.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const labelOptionsSeasonsOrChapters = computed(() => {
        if (form.linkable_type === 'season') {
            if (loadingOptionsSeasonsOrChapters.value) {
                return 'Carregando episódios';
            }
            if (optionsSeasonsOrChaptersNotFiltered.value.length === 0) {
                return 'Conteúdo não possuí temporadas';
            }
            return 'Selecione a temporada';
        }

        if (form.linkable_type === 'chapter') {
            if (loadingOptionsSeasonsOrChapters.value) {
                return 'Carregando episódios';
            }
            if (optionsSeasonsOrChaptersNotFiltered.value.length === 0) {
                return 'Conteúdo não possuí episódios';
            }
            return 'Selecione o episódio';
        }

        return 'Selecione';
    })

    const optionsSeasonsOrChapters = ref(props.seasonsOrChapters)
    const optionsSeasonsOrChaptersNotFiltered = ref(props.seasonsOrChapters)
    const loadingOptionsSeasonsOrChapters = ref(false)

    const filterSeasonsOrChapters = (val, update, abort) => {
        update(() => optionsSeasonsOrChapters.value = optionsSeasonsOrChaptersNotFiltered.value.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const fetchTypeSelected = () => {
        form.linkable_id = null;
        optionsSeasonsOrChapters.value = [];
        optionsSeasonsOrChaptersNotFiltered.value = [];
        loadingOptionsSeasonsOrChapters.value = true;

        if (form.linkable_type !== 'content') {
            let url = route('admin.quizz.linkables', {content: form.course_id, type: form.linkable_type});

            axios.get(url).then(response => {
                response.data.items.forEach(element => {
                    optionsSeasonsOrChapters.value.push({id: element.id, name: element.name});
                    optionsSeasonsOrChaptersNotFiltered.value.push({id: element.id, name: element.name});
                });

                loadingOptionsSeasonsOrChapters.value = false;
            })
        } else {
            loadingOptionsSeasonsOrChapters.value = false;
        }
    }

    const optionsLinkableTypes = computed(() => {
        let options = new Array();

        for (const key in props.linkableTypes) {
            options.push({ label: props.linkableTypes[key], value: key })
        }

        return options;
    })

    const optionsAnswerTypes = computed(() => {
        let options = new Array();

        for (const key in props.answerTypes) {
            options.push({ label: props.answerTypes[key], value: key })
        }

        return options;
    })

     /** CREATE QUESTION */
     const createQuestionModalShow = ref(false)

     const createQuestionForm = useForm({
        title: null,
        answer_type: 'aberta',
        text:null,
        alternatives: [],
        has_video: false,
        video: null,
        has_image: false,
        image: null,
        has_audio: false,
        audio: null,
    });

    const addAlternativeCreateQuestion = () => createQuestionForm.alternatives.push({ name: null })

    const removeAlternativeCreateQuestion = (position) => {
        createQuestionForm.alternatives = [
            ...createQuestionForm.alternatives.slice(0, position),
            ...createQuestionForm.alternatives.slice(position + 1)
        ]
    }

    const hideCreateQuestionModal = () => {
        if (createQuestionForm.isDirty) {
            $q.dialog({
                component: DialogConfirm,
                componentProps: {
                    title: 'Fechar janela?',
                    message: 'Ao fechar esta janela, os dados serão perdidos. Tem certeza que deseja fechar?',
                },
            }).onOk(() => {
                createQuestionModalShow.value = false;
                createQuestionForm.reset();
            })
        } else {
            createQuestionModalShow.value = false;
            createQuestionForm.reset();
        }
    }

    const storeQuestionSubmit = () => {
        createQuestionForm.clearErrors()

        if (!createQuestionForm.title) {
            createQuestionForm.setError('title', 'Preencha o titulo');
        }

        if (createQuestionForm.answer_type === 'fechada' && createQuestionForm.alternatives.length === 0) {
            createQuestionForm.setError('alternatives', 'Insira ao menos uma alternativa');
        }

        if (createQuestionForm.has_audio) {
            if (!createQuestionForm.audio) {
                createQuestionForm.setError('audio', 'Insira o link do aúdio');
            } else if (!isValidURL(createQuestionForm.audio)) {
                createQuestionForm.setError('audio', 'Link inválido');
            }
        }

        if (createQuestionForm.has_video) {
            if (!createQuestionForm.video) {
                createQuestionForm.setError('video', 'Insira o link do vídeo');
            } else if (!isValidURL(createQuestionForm.video)) {
                createQuestionForm.setError('video', 'Link inválido');
            }
        }

        if (createQuestionForm.has_image && !createQuestionForm.image) {
            createQuestionForm.setError('image', 'Insira a imagem');
        }

        if(createQuestionForm.hasErrors) return;

        let newQuestion = {
            title: createQuestionForm.title,
            answer_type: createQuestionForm.answer_type,
            alternatives: [],
            has_audio: createQuestionForm.has_audio,
            has_video: createQuestionForm.has_video,
            has_image: createQuestionForm.has_image,
            audio: createQuestionForm.audio,
            video: createQuestionForm.video,
            image: createQuestionForm.image,
        }

        createQuestionForm.alternatives.forEach(alt => newQuestion.alternatives.push({name: alt.name}))

        form.questions.push(newQuestion)

        createQuestionModalShow.value = false;
        createQuestionForm.reset();
    }

    const dropZoneCreateQuestionImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            } else {
                createQuestionForm.image = acceptFiles[0];
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const removeCreateQuestionImage = () => createQuestionForm.image = null
    /** END CREATE QUESTION */

    /** EDIT QUESTION */
    const editQuestionModalShow = ref(false)
    const editQuestionOriginalData = ref({})
    const editQuestionIndex = ref(null)

    const editQuestionForm = useForm({
        title: null,
        answer_type: 'aberta',
        text:null,
        alternatives: [],
        has_video: false,
        video: null,
        has_image: false,
        image: null,
        has_audio: false,
        audio: null,
    });

    const editQuestion = (index, question) => {
        editQuestionOriginalData.value = question;
        editQuestionModalShow.value = true;
        editQuestionIndex.value = index;

        editQuestionForm.id = question.id;
        editQuestionForm.answer_type = question.answer_type;
        editQuestionForm.title = question.title;
        editQuestionForm.alternatives = question.alternatives;
        editQuestionForm.has_video = question.has_video;
        editQuestionForm.video = question.video;
        editQuestionForm.has_image = question.has_image;
        editQuestionForm.image = question.image;
        editQuestionForm.has_audio = question.has_audio;
        editQuestionForm.audio = question.audio;
    }

    const addAlternativeEditQuestion = () => editQuestionForm.alternatives.push({ name: null })

    const removeAlternativeEditQuestion = (position) => {
        editQuestionForm.alternatives = [
            ...editQuestionForm.alternatives.slice(0, position),
            ...editQuestionForm.alternatives.slice(position + 1)
        ]
    }

    const hideEditQuestionModal = () => {
        if (
            editQuestionForm.id == editQuestionOriginalData.value.id &&
            editQuestionForm.answer_type == editQuestionOriginalData.value.answer_type &&
            editQuestionForm.title == editQuestionOriginalData.value.title &&
            editQuestionForm.alternatives == editQuestionOriginalData.value.alternatives &&
            editQuestionForm.has_video == editQuestionOriginalData.value.has_video &&
            editQuestionForm.video == editQuestionOriginalData.value.video &&
            editQuestionForm.has_image == editQuestionOriginalData.value.has_image &&
            editQuestionForm.image == editQuestionOriginalData.value.image &&
            editQuestionForm.has_audio == editQuestionOriginalData.value.has_audio &&
            editQuestionForm.audio == editQuestionOriginalData.value.audio
        ) {
            editQuestionModalShow.value = false;
            editQuestionForm.alternatives = [];
            editQuestionForm.reset();
        }
        else
        {
            $q.dialog({
                component: DialogConfirm,
                componentProps: {
                    title: 'Fechar janela?',
                    message: 'Ao fechar esta janela, os dados serão perdidos. Tem certeza que deseja fechar?',
                },
            }).onOk(() => {
                editQuestionModalShow.value = false;
                editQuestionForm.alternatives = [];
                editQuestionForm.reset();
            })
        }
    }

    const updateQuestionSubmit = () => {
        editQuestionForm.clearErrors()

        if (!editQuestionForm.title) {
            editQuestionForm.setError('title', 'Preencha o titulo');
        }

        if (editQuestionForm.answer_type === 'fechada' && editQuestionForm.alternatives.length === 0) {
            editQuestionForm.setError('alternatives', 'Insira ao menos uma alternativa');
        }

        if (editQuestionForm.has_audio) {
            if (!editQuestionForm.audio) {
                editQuestionForm.setError('audio', 'Insira o link do aúdio');
            } else if (!isValidURL(editQuestionForm.audio)) {
                editQuestionForm.setError('audio', 'Link inválido');
            }
        }

        if (editQuestionForm.has_video) {
            if (!editQuestionForm.video) {
                editQuestionForm.setError('video', 'Insira o link do vídeo');
            } else if (!isValidURL(editQuestionForm.video)) {
                editQuestionForm.setError('video', 'Link inválido');
            }
        }

        if (editQuestionForm.has_image && !editQuestionForm.image) {
            editQuestionForm.setError('image', 'Insira a imagem');
        }

        if(editQuestionForm.hasErrors) return;

        let updateQuestion = form.questions[editQuestionIndex.value];

        updateQuestion.title = editQuestionForm.title;
        updateQuestion.answer_type = editQuestionForm.answer_type;
        updateQuestion.alternatives = [];
        updateQuestion.has_audio = editQuestionForm.has_audio;
        updateQuestion.has_video = editQuestionForm.has_video;
        updateQuestion.has_image = editQuestionForm.has_image;
        updateQuestion.audio = editQuestionForm.audio;
        updateQuestion.video = editQuestionForm.video;
        updateQuestion.image = editQuestionForm.image;

        editQuestionForm.alternatives.forEach(alt => updateQuestion.alternatives.push({name: alt.name}))

        editQuestionModalShow.value = false;
        editQuestionForm.reset();
    }

    const dropZoneEditQuestionImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            } else {
                editQuestionForm.image = acceptFiles[0];
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const removeEditQuestionImage = () => editQuestionForm.image = null

    /** DESTROY CHAPTER */
    const destroyQuestion = (position) => {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: `Excluir questão`,
                message: `Tem certeza que deseja excluir essa questão?`,
            },
        }).onOk(() => {
            form.questions = [
                ...form.questions.slice(0, position),
                ...form.questions.slice(position + 1)
            ];
        });
    }
    /** END DESTROY CHAPTER */

    const imageSrc = (image) => {
        if (image == null) {
            return '';
        }

        return (typeof image === 'object') ? URL.createObjectURL(image) : image;
    };

    const isValidURL = (string) => {
        if (! string) return '';

        var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
        return (res !== null)
    };
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Quizz | Editar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Editar quizz </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="quizzs" class="text-grey"/>
                    <q-breadcrumbs-el label="Editar quizz" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroy"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir quizz
                    </div>
                </q-btn>

                <q-btn
                    color="primary"
                    rounded
                    no-caps
                    @click="submit"
                    :disabled="form.processing"
                >
                    <q-icon name="check" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Salvar
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
                        v-model="form.name"
                        label="Nome do Quizz"
                        :bottom-slots="Boolean(form.errors.name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ form.errors.name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-select
                        :options="optionsContents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.course_id"
                        label="Vincular quizz ao conteúdo"
                        :bottom-slots="Boolean(errors.course_id)"
                        clearable
                        use-input
                        @filter="filterContents"
                        @update:model-value="fetchTypeSelected"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.course_id }} </div>
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

                <div class="col-12 col-md-6" >
                    <q-select
                        :options="optionsLinkableTypes"
                        emit-value
                        map-options
                        outlined
                        v-model="form.linkable_type"
                        label="Momento de vinculação do quizz"
                        :bottom-slots="Boolean(errors.linkable_type)"
                        @update:model-value="fetchTypeSelected"
                        :disable="optionsSeasonsOrChaptersNotFiltered.length === 0"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.linkable_type }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-6">
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
                        :bottom-slots="Boolean(errors.linkable_id)"
                        @filter="filterSeasonsOrChapters"
                        use-input
                        :loading="loadingOptionsSeasonsOrChapters"
                        :disable="loadingOptionsSeasonsOrChapters || optionsSeasonsOrChaptersNotFiltered.length === 0"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.linkable_id }} </div>
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

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Questões do Quizz
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
                                        :label="`Questão ${index + 1}`"
                                        disable
                                    >

                                    <template v-slot:before>
                                        <q-icon name="subject" />
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

                                    <q-checkbox v-model="element.has_video" label="Vídeo" disable/>
                                    <q-checkbox v-model="element.has_audio" label="Áudio" disable/>
                                    <q-checkbox v-model="element.has_image" label="Imagem" disable/>
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
                                        :src="imageSrc(element.image)"
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
                            @click="createQuestionModalShow = true"
                        >
                            <q-icon name="add" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Adicionar questão
                            </div>
                        </q-btn>
                    </div>
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Gabarito
                    </div>
                </div>

                <div class="col-12 col-md-6" v-if="answerFileSrc">
                    <q-img
                        :src="answerFileSrc"
                        style="height: 400px"
                        class="adm-br-16"
                    >
                        <div class="absolute-bottom text-subtitle2 row items-center">
                            <q-icon name="attach_file" size="md" class="rotate-225 q-mr-md"/>

                            <q-btn
                                color="white"
                                class="absolute"
                                style="top: 8px; right: 8px"
                                flat
                                icon="close"
                                size="md"
                                @click="removeAnswerFile"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneAnswerFile.getRootProps()">
                                <input v-bind=" dropZoneAnswerFile.getInputProps()"/>
                                Alterar imagem
                            </div>
                        </div>
                    </q-img>
                </div>

                <div class="col-12" v-else>
                    <div
                        v-bind="dropZoneAnswerFile.getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind=" dropZoneAnswerFile.getInputProps()"/>

                        <q-icon name="attach_file" size="md" class="rotate-225"/>

                        <div class="q-mt-sm">
                            Clique aqui ou arraste o documento
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <q-dialog v-model="createQuestionModalShow" persistent>
            <q-card
                style="width: 1500px; max-width: 80vw; border-radius: 16px;"
                class="adm-br-16 q-my-sm q-pa-sm"
            >
                <q-card-section class="row q-col-gutter-lg">
                    <div class="col-12 items-center q-mt-xs row justify-between">
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                            Adicionar questão
                        </div>

                        <q-btn
                            color="primary"
                            @click="hideCreateQuestionModal"
                            flat
                        >
                            <q-icon name="close" size="xs"/>
                        </q-btn>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="createQuestionForm.title"
                            label="Questão"
                            :bottom-slots="Boolean(createQuestionForm.errors.title)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createQuestionForm.errors.title }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsAnswerTypes"
                            outlined
                            v-model="createQuestionForm.answer_type"
                            label="Tipo de questão"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(createQuestionForm.errors.answer_type)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createQuestionForm.errors.answer_type }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12" v-if="createQuestionForm.answer_type === 'fechada'">
                        <draggable
                            :list="createQuestionForm.alternatives"
                            item-key="index"
                        >
                            <template #item="{element, index}">
                                <div class="row q-col-gutter-lg q-mb-lg cursor-pointer">
                                    <div class="col-md-5 col-11 offset-1">
                                        <q-input
                                            outlined
                                            :label="`Opção resposta ${index + 1}`"
                                            v-model="element.name"
                                        >
                                            <template v-slot:before>
                                                <q-icon name="subject" />
                                            </template>

                                            <template v-slot:after>
                                                <q-btn
                                                    v-if="createQuestionForm.alternatives.length > 1"
                                                    color="negative"
                                                    flat
                                                    icon="close"
                                                    padding="xs"
                                                    @click="removeAlternativeCreateQuestion(index)"
                                                />
                                            </template>
                                        </q-input>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>

                    <div class="col-12 column flex-center"  v-if="createQuestionForm.answer_type === 'fechada'">
                        <div class="text-center text-red" v-if="createQuestionForm.errors.alternatives">
                            {{ createQuestionForm.errors.alternatives }}
                        </div>

                        <q-btn
                            color="primary"
                            no-caps
                            flat
                            @click="addAlternativeCreateQuestion"
                        >
                            <q-icon name="add" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Adicionar alternativa
                            </div>
                        </q-btn>
                    </div>

                    <div class="col-12 flex items-center">
                        <div class="text-grey-8">
                            Vincular anexo:
                        </div>

                        <q-checkbox v-model="createQuestionForm.has_video" label="Vídeo" />
                        <q-checkbox v-model="createQuestionForm.has_audio" label="Áudio" />
                        <q-checkbox v-model="createQuestionForm.has_image" label="Imagem" />
                    </div>

                    <div class="col-12" v-if="createQuestionForm.has_video">
                        <q-input
                            outlined
                            label="Link do vídeo"
                            v-model="createQuestionForm.video"
                            :bottom-slots="Boolean(createQuestionForm.errors.video)"
                        >
                             <template v-slot:hint>
                                <div class="text-red"> {{ createQuestionForm.errors.video }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12" v-if="createQuestionForm.has_audio">
                        <q-input
                            outlined
                            label="Link do áudio"
                            v-model="createQuestionForm.audio"
                            :bottom-slots="Boolean(createQuestionForm.errors.audio)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ createQuestionForm.errors.audio }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div
                        class="col-12 col-md-4"
                        v-if="createQuestionForm.has_image && createQuestionForm.image"
                    >
                        <q-img
                            :src="imageSrc(createQuestionForm.image)"
                            style="height: 400px"
                            class="adm-br-16"
                        >
                            <div class="absolute-bottom text-subtitle2 row items-center">
                                <q-icon name="o_photo" size="md" class="q-mr-md"/>

                                <q-btn
                                    color="white"
                                    class="absolute"
                                    style="top: 8px; right: 8px"
                                    flat
                                    icon="close"
                                    size="md"
                                    @click="removeCreateQuestionImage"
                                />

                                <div class="flex cursor-pointer" v-bind="dropZoneCreateQuestionImage.getRootProps()">
                                    <input v-bind="dropZoneCreateQuestionImage.getInputProps()"/>
                                    Alterar imagem
                                </div>
                            </div>
                        </q-img>
                    </div>

                    <div class="col-12 text-center text-red" v-if="createQuestionForm.errors.image">
                        {{ createQuestionForm.errors.image }}
                    </div>

                    <div
                        class="col-12"
                        v-if="createQuestionForm.has_image && !createQuestionForm.image"
                    >
                        <div
                            v-bind="dropZoneCreateQuestionImage.getRootProps()"
                            class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                        >
                            <input v-bind="dropZoneCreateQuestionImage.getInputProps()"/>

                            <q-icon name="o_photo" size="md"/>

                            <div class="q-mt-sm">
                                Clique aqui ou arraste sua imagem
                            </div>
                        </div>
                    </div>

                    <div class="col-12 row justify-end">
                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            @click="hideCreateQuestionModal"
                            size="sm"
                            class="q-my-md"
                            outline
                        >
                            <q-icon name="close" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cancelar
                            </div>
                        </q-btn>

                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            :disabled="createQuestionForm.processing"
                            size="sm"
                            class="q-my-md q-ml-sm"
                            @click="storeQuestionSubmit"
                        >
                            <q-icon name="check" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cadastrar
                            </div>
                        </q-btn>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>

        <q-dialog v-model="editQuestionModalShow" persistent>
            <q-card
                style="width: 1500px; max-width: 80vw; border-radius: 16px;"
                class="adm-br-16 q-my-sm q-pa-sm"
            >
                <q-card-section class="row q-col-gutter-lg">
                    <div class="col-12 items-center q-mt-xs row justify-between">
                        <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                            Editar questão
                        </div>

                        <q-btn
                            color="primary"
                            @click="hideEditQuestionModal"
                            flat
                        >
                            <q-icon name="close" size="xs"/>
                        </q-btn>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="editQuestionForm.title"
                            label="Questão"
                            :bottom-slots="Boolean(editQuestionForm.errors.title)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editQuestionForm.errors.title }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsAnswerTypes"
                            outlined
                            v-model="editQuestionForm.answer_type"
                            label="Tipo de questão"
                            emit-value
                            map-options
                            :bottom-slots="Boolean(editQuestionForm.errors.answer_type)"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ editQuestionForm.errors.answer_type }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12" v-if="editQuestionForm.answer_type === 'fechada'">
                        <draggable
                            :list="editQuestionForm.alternatives"
                            item-key="index"
                        >
                            <template #item="{element, index}">
                                <div class="row q-col-gutter-lg q-mb-lg cursor-pointer">
                                    <div class="col-md-5 col-11 offset-1">
                                        <q-input
                                            outlined
                                            :label="`Opção resposta ${index + 1}`"
                                            v-model="element.name"
                                        >
                                            <template v-slot:before>
                                                <q-icon name="subject" />
                                            </template>

                                            <template v-slot:after>
                                                <q-btn
                                                    v-if="editQuestionForm.alternatives.length > 1"
                                                    color="negative"
                                                    flat
                                                    icon="close"
                                                    padding="xs"
                                                    @click="removeAlternativeEditQuestion(index)"
                                                />
                                            </template>
                                        </q-input>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>

                    <div class="col-12 flex flex-center" v-if="editQuestionForm.answer_type === 'fechada'">
                        <div class="text-center text-red" v-if="editQuestionForm.errors.alternatives">
                            {{ editQuestionForm.errors.alternatives }}
                        </div>

                        <q-btn
                            color="primary"
                            no-caps
                            flat
                            @click="addAlternativeEditQuestion"
                        >
                            <q-icon name="add" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Adicionar alternativa
                            </div>
                        </q-btn>
                    </div>

                    <div class="col-12 flex items-center">
                        <div class="text-grey-8">
                            Vincular anexo:
                        </div>

                        <q-checkbox v-model="editQuestionForm.has_video" label="Vídeo" />
                        <q-checkbox v-model="editQuestionForm.has_audio" label="Áudio" />
                        <q-checkbox v-model="editQuestionForm.has_image" label="Imagem" />
                    </div>

                    <div class="col-12" v-if="editQuestionForm.has_video">
                        <q-input
                            outlined
                            label="Link do vídeo"
                            v-model="editQuestionForm.video"
                            :bottom-slots="Boolean(editQuestionForm.errors.video)"
                        >
                             <template v-slot:hint>
                                <div class="text-red"> {{ editQuestionForm.errors.video }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12" v-if="editQuestionForm.has_audio">
                        <q-input
                            outlined
                            label="Link do áudio"
                            v-model="editQuestionForm.audio"
                            :bottom-slots="Boolean(editQuestionForm.errors.audio)"
                        >
                             <template v-slot:hint>
                                <div class="text-red"> {{ editQuestionForm.errors.audio }} </div>
                            </template>
                        </q-input>
                    </div>


                    <div
                        class="col-12 col-md-4"
                        v-if="editQuestionForm.has_image && editQuestionForm.image"
                    >
                        <q-img
                            :src="imageSrc(editQuestionForm.image)"
                            style="height: 400px"
                            class="adm-br-16"
                        >
                            <div class="absolute-bottom text-subtitle2 row items-center">
                                <q-icon name="o_photo" size="md" class="q-mr-md"/>

                                <q-btn
                                    color="white"
                                    class="absolute"
                                    style="top: 8px; right: 8px"
                                    flat
                                    icon="close"
                                    size="md"
                                    @click="removeEditQuestionImage"
                                />

                                <div class="flex cursor-pointer" v-bind="dropZoneEditQuestionImage.getRootProps()">
                                    <input v-bind="dropZoneEditQuestionImage.getInputProps()"/>
                                    Alterar imagem
                                </div>
                            </div>
                        </q-img>
                    </div>

                    <div class="col-12 text-center text-red" v-if="editQuestionForm.errors.image">
                        {{ editQuestionForm.errors.image }}
                    </div>

                    <div
                        class="col-12"
                        v-if="editQuestionForm.has_image && !editQuestionForm.image"
                    >
                        <div
                            v-bind="dropZoneEditQuestionImage.getRootProps()"
                            class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                        >
                            <input v-bind="dropZoneEditQuestionImage.getInputProps()"/>

                            <q-icon name="o_photo" size="md"/>

                            <div class="q-mt-sm">
                                Clique aqui ou arraste sua imagem
                            </div>
                        </div>
                    </div>

                    <div class="col-12 row justify-end">
                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            @click="hideEditQuestionModal"
                            size="sm"
                            class="q-my-md"
                            outline
                        >
                            <q-icon name="close" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Cancelar
                            </div>
                        </q-btn>

                        <q-btn
                            color="primary"
                            rounded
                            no-caps
                            :disabled="editQuestionForm.processing"
                            size="sm"
                            class="q-my-md q-ml-sm"
                            @click="updateQuestionSubmit"
                        >
                            <q-icon name="check" size="xs"/>

                            <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                                Atualizar
                            </div>
                        </q-btn>
                    </div>
                </q-card-section>
            </q-card>
        </q-dialog>
    </AuthenticatedLayout>
</template>
