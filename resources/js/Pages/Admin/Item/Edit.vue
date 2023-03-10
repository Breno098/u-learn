<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { Inertia } from '@inertiajs/inertia';
    import { ref, computed } from 'vue'
    import DialogConfirm from '@/Components/DialogConfirm.vue';
    import { useDropzone } from "vue3-dropzone";

    const $q = useQuasar()

    const props = defineProps({
        item: Object,
        errors: Object,
        campaigns: Array,

        canItemDestroy: Boolean,
    });

    const form = useForm({
        id: props.item.id,
        name: props.item.name,
        description: props.item.description,
        campaign_id: props.item.campaign_id,
        image: props.item.image,
    });

    const dropZoneImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            form.image = acceptFiles[0];

            if (rejectReasons.length > 0) {
                $q.notify({
                    message: 'Insira apenas uma imagem',
                    position: 'center',
                })
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const imageSrc = computed(() => {
        if (! form.image)
            return '';

        return (typeof form.image === 'object') ? URL.createObjectURL(form.image) : form.image;
    });

    const removeImage = () => form.image = null

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.item.update", form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Brinde atualizado com sucesso',
                        position: 'top',
                    })
                },
            })
    };

    function destroy() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir brinde',
                message: 'Tem certeza que deseja excluir esse brinde?',
            },
        }).onOk(() => {
            Inertia.delete(route('admin.item.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Brinde excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

    const optionsCampaigns = ref(props.campaigns)

    const filterCampaigns = (val, update, abort) => {
        update(() => optionsCampaigns.value = props.campaigns.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Brinde | Editar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Editar brinde </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Brindes" class="text-grey"/>
                    <q-breadcrumbs-el label="Editar brinde" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    v-if="canItemDestroy"
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroy"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir Brinde
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
                        label="Nome"
                        :bottom-slots="Boolean(errors.name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-select
                        :options="optionsCampaigns"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.campaign_id"
                        label="Campanha"
                        :bottom-slots="Boolean(errors.campaign_id)"
                        clearable
                        use-chips
                        use-input
                        @filter="filterCampaigns"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.campaigns }} </div>
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

                <div class="col-12 col-md-6" v-if="imageSrc">
                    <q-img
                        :src="imageSrc"
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
                                @click="removeImage"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneImage.getRootProps()">
                                <input v-bind=" dropZoneImage.getInputProps()"/>
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
                        <input v-bind=" dropZoneImage.getInputProps()"/>

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
