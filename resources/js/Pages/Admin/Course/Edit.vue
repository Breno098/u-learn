<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar';
    import { ref, computed } from 'vue'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        course: Object,
        errors: Object,
        categories: Array,
        genres: Array,
    });

    const form = useForm({
        id: props.course.id,
        name: props.course.name,
        descritiption: props.course.descritiption,
        level: props.course.level,
        duration: props.course.duration,
        video: props.course.video,
        url_sale: props.course.url_sale,
        category_id: props.course.category_id,
        certificate_id: props.course.certificate_id,
        wallpaper_image: props.course.wallpaper_image,
        tumb_image: props.course.tumb_image,
        genres: props.course.genres.map(s => s.id),
    });

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.course.update", form.id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Curso atualizado com sucesso',
                        position: 'top',
                    })
                },
            })
    };

    function destroy() {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir curso',
                message: 'Tem certeza que deseja excluir esse curso?',
                confirm: true
            },
        }).onOk(() => {
            useForm().delete(route('admin.course.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Curso excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    const wallpaperSrc = computed(() => {
        return form.wallpaper_image == null ? '' : (typeof form.wallpaper_image === 'object') ? URL.createObjectURL(form.wallpaper_image) : form.wallpaper_image;
    });

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

    const tumbImageSrc = computed(() => {
        return form.tumb_image == null ? '' : (typeof form.tumb_image === 'object') ? URL.createObjectURL(form.tumb_image) : form.tumb_image;
    });

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

    const tab = ref('main')

    const goExtraTab = () => {
        if (form.isDirty) {
            tab.value = 'main';

            $q.dialog({
                component: AdminDialog,
                componentProps: {
                    title: 'Alterações não salvas',
                    message: 'Ao sair suas alterações serão perdidas. Tem certeza disso?',
                    confirm: true
                }
            }).onOk(() => {
                useForm().get(route('admin.course.extra.index', props.course.id))
            })
        } else {
            useForm().get(route('admin.course.extra.index', props.course.id))
        }
    };

    const goTitlesTab = () => {
        if (form.isDirty) {
            tab.value = 'main';

            $q.dialog({
                component: AdminDialog,
                componentProps: {
                    title: 'Alterações não salvas',
                    message: 'Ao sair suas alterações serão perdidas. Tem certeza disso?',
                    confirm: true
                }
            }).onOk(() => {
                useForm().get(route('admin.course.titles', props.course.id))
            })
        } else {
            useForm().get(route('admin.course.titles', props.course.id))
        }
    };

    const goBack = () => {
        if (form.isDirty) {
            tab.value = 'main';

            $q.dialog({
                component: AdminDialog,
                componentProps: {
                    title: 'Alterações não salvas',
                    message: 'Ao sair suas alterações serão perdidas. Tem certeza disso?',
                    confirm: true
                }
            }).onOk(() => {
                useForm().get(route('admin.course.index'))
            })
        } else {
            useForm().get(route('admin.course.index'))
        }
    };
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="course.name" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8">
                    {{ course.name }}
                </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Cursos" class="text-grey"/>
                    <q-breadcrumbs-el :label="course.name" class="text-primary" />
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
                        Excluir curso
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
                        Salvar curso
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <!-- <q-tabs
                v-model="tab"
                dense
                class="text-grey adm-br-tl-16 adm-br-tr-16"
                active-color="primary"
                indicator-color="primary"
                align="justify"
                no-caps
            >
                <q-tab
                    name="main"
                    label="Dados do curso"
                    class="q-py-md"
                />

                <q-tab
                    name="titles"
                    label="Títulos"
                    class="q-py-md"
                    @click="goTitlesTab"
                />

                <q-tab
                    name="extras"
                    label="Extras"
                    class="q-py-md"
                    @click="goExtraTab"
                />
            </q-tabs> -->

            <div class="row q-col-gutter-lg q-py-lg q-px-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Informações
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do curso"
                        :bottom-slots="Boolean(errors.name)"
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
                        label="Categoria"
                        :bottom-slots="Boolean(errors.level)"
                        clearable
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
                        type="text-area"
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
                        stack-label
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
                        stack-label
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
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.url_sale }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Capa
                    </div>
                </div>

                <div class="col-12" v-if="wallpaperSrc">
                    <q-img
                        :src="wallpaperSrc"
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
                                @click="removeWallpaperImage"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneWallpaper.getRootProps()">
                                <input v-bind="dropZoneWallpaper.getInputProps()"/>
                                Alterar imagem
                            </div>
                        </div>
                    </q-img>
                </div>

                <div class="col-12" v-else>
                    <div
                        v-bind="dropZoneWallpaper.getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind="dropZoneWallpaper.getInputProps()"/>

                        <q-icon name="o_photo" size="md"/>

                        <div class="q-mt-sm">
                            Clique aqui ou arraste sua imagem
                        </div>
                    </div>
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Imagem de tumb
                    </div>
                </div>

                <div class="col-12" v-if="tumbImageSrc">
                    <q-img
                        :src="tumbImageSrc"
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
                                @click="removeTumbImage"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneTumbImage.getRootProps()">
                                <input v-bind="dropZoneTumbImage.getInputProps()"/>
                                Alterar imagem
                            </div>
                        </div>
                    </q-img>
                </div>

                <div class="col-12" v-else>
                    <div
                        v-bind="dropZoneTumbImage.getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind="dropZoneTumbImage.getInputProps()"/>

                        <q-icon name="o_photo" size="md"/>

                        <div class="q-mt-sm">
                            Clique aqui ou arraste sua imagem
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
