<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue'
    import { useQuasar } from 'quasar'
    import { useDropzone } from "vue3-dropzone";

    const $q = useQuasar()

    const props = defineProps({
        content: Object,
        errors: Object,
        numbers: Array
    });

    const form = useForm({
        id: null,
        name: null,
        number_of_chapters: null,
        number: null,
        image: null,
    });

    const dropZoneImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            } else {
                form.image = acceptFiles[0];
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const imageSrc = computed(() => {
        return form.image == null ? '' : (typeof form.image === 'object') ? URL.createObjectURL(form.image) : form.image;
    });

    const removeImage = () => form.image = null;

    const submit = () => {
        form.post(route("admin.content.season.store", {
            content: props.content.id,
        }), {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: `Temporada ${form.name} cadastrada com sucesso`,
                    position: 'top',
                })
            },
        })
    };
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="content.name" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8">
                    Adicionar temporada
                </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Conteúdos" class="text-grey"/>
                    <q-breadcrumbs-el :label="content.name" class="text-grey"/>
                    <q-breadcrumbs-el label="Adicionar temporada" class="text-primary" />
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

                <div class="col-12 col-md-3">
                    <q-select
                        option-value="position"
                        :options="numbers"
                        :option-disable="opt => !opt.empty"
                        :option-label="opt => !opt.empty ? `${opt.position} (desabilitado)` : opt.position"
                        emit-value
                        map-options
                        outlined
                        v-model="form.number"
                        label="Número da temporada"
                        :bottom-slots="Boolean(errors.number)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.number }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-6">
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

                <div class="col-12 col-md-3">
                    <q-select
                        :options="[...Array(50).keys()].map(i => ++i)"
                        outlined
                        v-model="form.number_of_chapters"
                        label="Quantidade de episódios"
                        :bottom-slots="Boolean(errors.number_of_chapters)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.number_of_chapters }} </div>
                        </template>
                    </q-select>

                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Capa principal
                    </div>
                </div>

                <div class="col-12" v-if="imageSrc">
                    <q-img
                        :src="imageSrc"
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
                                @click="removeImage"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneImage.getRootProps()">
                                <input v-bind="dropZoneImage.getInputProps()"/>
                                Alterar imagem
                            </div>
                        </div>
                    </q-img>
                </div>

                <div class="col-12" v-else>
                    <div
                        v-bind="dropZoneImage.getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind="dropZoneImage.getInputProps()"/>

                        <q-icon name="o_photo" size="md"/>

                        <div class="q-mt-sm">
                            Clique aqui ou arraste sua imagem
                        </div>
                    </div>
                </div>

                <div class="col-12 flex justify-end items-center">
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
        </div>
    </AuthenticatedLayout>
</template>
