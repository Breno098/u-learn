<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';
    import draggable from 'vuedraggable'
    import { ref, computed, watch } from 'vue'

    const $q = useQuasar()

    const props = defineProps({
        index: {
            default: 0,
            type: Number
        },
        answerTypes: Object,
        question: Object
    });

    const form = ref({
        id: null,
        title: null,
        text: null,
        answer_type: null,
        number: null,
        video: null,
        audio: null,
        image: null,
        has_video: null,
        has_audio: null,
        has_image: null,
        alternatives: []
    })

    const srcImage = (field) => {
        return field == null ? '' : (typeof field === 'object') ? URL.createObjectURL(field) : field;
    }

    const optionsAnswerTypes = computed(() => {
        let options = new Array();

        for (const key in props.answerTypes) {
            options.push({ label: props.answerTypes[key], value: key })
        }

        return options;
    })

    const addAlternative = () => props.question.alternatives.push({
        name: null,
        is_correct: false
    })

    const removeAlternative = (positionQuestion, positionAlternative) => {
        props.question.alternatives = [
            ...props.question.alternatives.slice(0, positionAlternative),
            ...props.question.alternatives.slice(positionAlternative + 1)
        ]
    }

    const numberToChar = (number) => String.fromCharCode(97 + number).toUpperCase()

    watch(props.question.alternatives, (newValue, oldValue) => {
        let last = props.question.alternatives[props.question.alternatives.lenght -]

        console.log(last.name);
    })
</script>

<template>
    <q-card flat>
        <q-card-section>
            <div class="row q-col-gutter-lg cursor-pointer">
                <div class="col-12 text-blue-grey-10 adm-fs-16 text-bold">
                    Questão {{ index + 1 }}
                </div>

                <div class="col-md-6 col-12">
                    <q-input
                        outlined
                        v-model="question.title"
                        label="Titulo"
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
                        label="Tipo de resposta"
                        v-model="question.answer_type"
                        emit-value
                        map-options
                    >
                        <!-- <template v-slot:after>
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
                                    @click="editQuestion(index, question)"
                                />
                            </q-btn-group>
                        </template> -->
                    </q-select>
                </div>

                <div
                    class="col-12 row"
                    v-for="(alternative, indexAlt) in question.alternatives"
                    v-if="question.answer_type === 'fechada'"
                >
                    <div class="col-md-11 col-11 offset-1">
                        <q-input
                            outlined
                            :label="`Opção resposta ${numberToChar(indexAlt)}`"
                            v-model="alternative.name"
                        >
                            <!-- <template v-slot:append>
                                <q-checkbox
                                    v-model="alternative.is_correct"
                                    label="Resposta correta"
                                    color="green"
                                    class="adm-fs-13"
                                    checked-icon="task_alt"
                                    unchecked-icon="radio_button_unchecked"
                                />
                            </template> -->
                            <template v-slot:append>
                                <q-btn
                                    color="negative"
                                    flat
                                    icon="close"
                                    padding="xs"
                                    @click="removeAlternative(index, indexAlt)"
                                >
                                    <q-tooltip
                                        anchor="center left"
                                        self="center right"
                                        :offset="[10, 10]"
                                        class="text-body2 bg-grey-10"
                                    >
                                        Remover alternativa {{ numberToChar(indexAlt) }}
                                    </q-tooltip>
                                </q-btn>
                            </template>
                        </q-input>
                    </div>
                </div>

                <div
                    class="col-12 column flex-center"
                    v-if="question.answer_type === 'fechada'"
                >
                    <q-btn
                        color="primary"
                        no-caps
                        flat
                        @click="addAlternative"
                    >
                        <q-icon name="add" size="xs"/>

                        <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                            Adicionar alternativa
                        </div>
                    </q-btn>
                </div>


                <!-- <div class="offset-1 col-11 flex items-center">
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
                    class="offset-1 col-11 col-md-11"
                    v-if="element.has_image"
                >
                    <q-img
                        :src="srcImage(element.image)"
                        style="height: 400px"
                        class="adm-br-16"
                />
                </div> -->
            </div>
        </q-card-section>
    </q-card>
</template>
