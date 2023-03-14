<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar';
    import { ref } from 'vue'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
        categories: Array,
        genres: Array,
    });

    const form = useForm({
        id: null,
        name: null,
        descritiption: null,
        level: null,
        duration: null,
        video: null,
        url_sale: null,
        category_id: null,
        certificate_id: null,
        wallpaper_image: null,
        tumb_image: null,
        genres: null,
    });

    const submit = () => {
        form.post(route("admin.course.store"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Curso cadastrado com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 1000
                    }
                })
            },
        })
    };

    const srcImage = (field) => {
        return field == null ? '' : (typeof field === 'object') ? URL.createObjectURL(field) : field;
    }

    const dropZoneWallpaper = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            } else {
                form.wallpaper_image = acceptFiles[0];
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const removeWallpaperImage = () => form.wallpaper_image = null;

    const dropZoneTumbImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            form.tumb_image = acceptFiles[0];

            if (rejectReasons.length > 0) {
                $q.notify({ message: 'Insira apenas uma imagem', position: 'center' })
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const removeTumbImage = () => form.tumb_image = null;

    const optionsCategories = ref(props.categories)

    const filterCategories = (val, update, abort) => {
        update(() => optionsCategories.value = props.categories.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsGenres = ref(props.genres)

    const filterGenres = (val, update, abort) => {
        update(() => optionsGenres.value = props.genres.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const goBack = () =>  useForm().get(route('admin.course.index'));
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Cadastrar curso" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_school" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Cadastrar curso
                    </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
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

                    <div class="col-12 col-md-6">
                        <q-input
                            outlined
                            v-model="form.name"
                            label="Nome do curso"
                            :bottom-slots="Boolean(errors.name)"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="[{
                                value: 'iniciante',
                                label: 'Iniciante'
                            }, {
                                value: 'intermediario',
                                label: 'Intermediário'
                            }, {
                                value: 'avancado',
                                label: 'Avançado'
                            }]"
                            emit-value
                            map-options
                            outlined
                            v-model="form.level"
                            label="Nível"
                            :bottom-slots="Boolean(errors.level)"
                            clearable
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.level }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12">
                        <q-input
                            outlined
                            v-model="form.descritiption"
                            label="Descrição"
                            :bottom-slots="Boolean(errors.descritiption)"
                            type="textarea"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.descritiption }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-6">
                        <q-select
                            :options="optionsCategories"
                            option-value="id"
                            option-label="name"
                            emit-value
                            map-options
                            outlined
                            v-model="form.category_id"
                            label="Categoria"
                            :bottom-slots="Boolean(errors.category_id)"
                            clearable
                            @filter="filterCategories"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.category_id }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12 col-xl-6">
                        <q-select
                            :options="optionsGenres"
                            option-value="id"
                            option-label="name"
                            emit-value
                            map-options
                            outlined
                            v-model="form.genres"
                            label="Genero"
                            :bottom-slots="Boolean(errors.genres)"
                            multiple
                            clearable
                            @filter="filterGenres"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.genres }} </div>
                            </template>
                        </q-select>
                    </div>

                    <div class="col-12">
                        <q-input
                            outlined
                            v-model="form.video"
                            label="Url Vídeo"
                            :bottom-slots="Boolean(errors.video)"
                            type="text-area"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.video }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12">
                        <q-input
                            outlined
                            v-model="form.url_sale"
                            label="Url para venda"
                            :bottom-slots="Boolean(errors.url_sale)"
                            type="text-area"
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.url_sale }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Capa
                        </div>
                    </div>

                    <div class="col-12" v-if="form.wallpaper_image">
                        <q-img
                            :src="srcImage(form.wallpaper_image)"
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
                                    @click="removeWallpaperImage"
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

                    <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Imagem de tumb
                        </div>
                    </div>

                    <div class="col-12" v-if="form.tumb_image">
                        <q-img
                            :src="srcImage(form.tumb_image)"
                            style="height: 400px"
                            class="adm-br-5"
                        >
                            <div class="absolute-bottom text-subtitle2 row items-center">
                                <q-btn
                                    color="indigo"
                                    class="absolute inset-shadow-down"
                                    icon="insert_link"
                                    v-bind="dropZoneTumbImage.getRootProps()"
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
                                    @click="removeTumbImage"
                                    style="top: 0; right: 12px; transform: translateY(-50%);"
                                >
                                    <q-tooltip>
                                        Clique para remover imagem
                                    </q-tooltip>
                                </q-btn>

                                <input v-bind="dropZoneTumbImage.getInputProps()"/>
                            </div>
                        </q-img>
                    </div>

                    <div class="col-12" v-else>
                        <div
                            v-bind="dropZoneTumbImage.getRootProps()"
                            class="column flex-center q-py-lg text-grey adm-border-dashed-indigo adm-br-5"
                        >
                            <input v-bind="dropZoneTumbImage.getInputProps()"/>

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
