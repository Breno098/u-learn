<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { useDropzone } from "vue3-dropzone";
    import AdminDialog from '@/Components/AdminDialog.vue';
    import CanvasElement from '@/Components/Canvas/CanvasElement.vue';

    const $q = useQuasar()

    const props = defineProps({
        certificate: Object,
        errors: Object,
    });

    const form = useForm({
        id: props.certificate.id,
        name: props.certificate.name,
        image: props.certificate.image,
    });

    const submit = () => {
        form.put(route("admin.certificate.update", form.id), {
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Certificado atualizada com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 1000
                    }
                })
            },
        })
    };

    const destroy = () => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir certificado',
                message: 'Ao excluir a certificado, todos os cursos serão desvinculados. Deseja realmente excluir?',
                confirm: true
            },
        }).onOk(() => {
            useForm().delete(route('admin.certificate.destroy', form.id), {
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: 'Certificado excluída com sucesso',
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })
                }
            })
        });
    }

    const srcImage = (field) => {
        return field == null ? '' : (typeof field === 'object') ? URL.createObjectURL(field) : field;
    }
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

    const removeImage = () => form.image = null;

</script>

<template>
    <AuthenticatedLayout>
        <Head :title="certificate.name" />

        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="card_membership" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md">
                        Certificado: {{ certificate.name }}
                    </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
                    <q-btn
                        color="negative"
                        class="q-mr-md"
                        no-caps
                        outline
                        @click="destroy"
                        icon="close"
                        label="Excluir"
                    />

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
                            color="indigo"
                        >
                            <template v-slot:hint>
                                <div class="text-red"> {{ errors.name }} </div>
                            </template>
                        </q-input>
                    </div>

                    <div class="col-12 col-md-12">
                        <canvas-element
                            :position="{ x: 10, y: 10 }"
                            :image="srcImage(form.image)"
                         />
                    </div>

                    <!-- <div class="col-12 items-center">
                        <div class="q-ml-sm text-blue-grey-10 adm-fs-23">
                            Imagem
                        </div>
                    </div>

                    <div class="col-12" v-if="form.image">
                        <q-img
                            :src="srcImage(form.image)"
                            style="height: 400px"
                            class="adm-br-5"
                        >
                            <div class="absolute-bottom text-subtitle2 row items-center">
                                <q-btn
                                    color="indigo"
                                    class="absolute inset-shadow-down"
                                    icon="insert_link"
                                    v-bind="dropZoneImage.getRootProps()"
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

                                <input v-bind="dropZoneImage.getInputProps()"/>
                            </div>
                        </q-img>
                    </div>

                    <div class="col-12" v-else>
                        <div
                            v-bind="dropZoneImage.getRootProps()"
                            class="column flex-center q-py-lg text-grey adm-border-dashed-indigo adm-br-5"
                        >
                            <input v-bind="dropZoneImage.getInputProps()"/>

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
