<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { ref, computed } from 'vue'
    import { useDropzone } from "vue3-dropzone";

    const $q = useQuasar()

    const props = defineProps({
        errors: Object,
        types: Object,
        responsible: Array,
        students: Array,
        groups: Array,
        campaigns: Array,
        linkableTypes: Object,
        contents: Array,
        seasonsOrChapters: Array,
    });

    const form = useForm({
        id: null,
        name: null,
        description: null,
        event_type: 'live',
        type: 'individual',
        embed: null,
        responsible_id: null,
        number_of_students: 1,
        start_at: null,
        end_at: null,
        group_ids: [],
        student_ids: [],
        campaign_ids: [],
        materials: [],
        image: null,
        linkable_id: null,
        linkable_type: 'content',
        content_id: null,
        has_link_with_content: false,
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

    const { getRootProps, getInputProps, isDragActive } = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            for (var x = 0; x < acceptFiles.length; x++) {
                form.materials.push({
                    id: null,
                    name: acceptFiles[x].name,
                    size: acceptFiles[x].size,
                    link: URL.createObjectURL(acceptFiles[x]),
                    uploadedFile: acceptFiles[x],
                    is_image: acceptFiles[x].type.includes('image')
                });
            }
        }
    });

    const removeMaterial = (position) => {
        form.materials = [
            ...form.materials.slice(0, position),
            ...form.materials.slice(position + 1)
        ];
    }

    const imageForDoc = 'https://i.pinimg.com/564x/04/54/7c/04547c2b354abb70a85ed8a2d1b33e5f.jpg';

    const submit = () => {
        form.post(route("admin.live-event.store"), {
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: 'Evento cadastrado com sucesso',
                    position: 'top',
                })
            },
        })
    };

    const optionsStudents = ref(props.students)

    const filterStudents = (val, update, abort) => {
        update(() => optionsStudents.value = props.students.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsResponsible = ref(props.responsible)

    const filterResponsible = (val, update, abort) => {
        update(() => optionsResponsible.value = props.responsible.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsGroups = ref(props.groups)

    const filterGroups = (val, update, abort) => {
        update(() => optionsGroups.value = props.groups.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsCampaigns = ref(props.campaigns)

    const filterCampaigns = (val, update, abort) => {
        update(() => optionsCampaigns.value = props.campaigns.filter(s => s.name.toLowerCase().indexOf(val.toLowerCase()) > -1))
    }

    const optionsTypes = computed(() => {
        let options = new Array();

        for (const key in props.types) {
            options.push({ label: props.types[key], value: key })
        }

        return options;
    })

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
            let url = route('admin.quizz.linkables', {content: form.content_id, type: form.linkable_type});

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


    const goBack = () => useForm().get(route('admin.live-event.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Evento ao vivo | Adicionar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Novo evento ao vivo </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Eventos ao vivo" class="text-grey"/>
                    <q-breadcrumbs-el label="Novo evento ao vivo" class="text-primary" />
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

               <div class="col-12 col-xl-6">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do evento"
                        :bottom-slots="Boolean(errors.name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.start_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de inicio"
                        :bottom-slots="Boolean(errors.start_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.start_at }} </div>
                        </template>

                        <q-popup-proxy class="row">
                            <q-date
                                v-model="form.start_at"
                                mask="DD/MM/YYYY HH:mm"
                                flat
                                square
                            />

                            <q-time
                                v-model="form.start_at"
                                mask="DD/MM/YYYY HH:mm"
                                format24h
                                flat
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
                            </q-time>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.end_at"
                        mask="##/##/#### ##:##"
                        label="Data e hora de término"
                        :bottom-slots="Boolean(errors.end_at)"
                        clearable
                    >
                        <template v-slot:prepend>
                            <q-icon name="o_calendar_today"/>
                        </template>

                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.end_at }} </div>
                        </template>

                        <q-popup-proxy class="row">
                            <q-date
                                v-model="form.end_at"
                                mask="DD/MM/YYYY HH:mm"
                                flat
                                square
                            />

                            <q-time
                                v-model="form.end_at"
                                mask="DD/MM/YYYY HH:mm"
                                format24h
                                flat
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
                            </q-time>
                        </q-popup-proxy>
                    </q-input>
                </div>

                <div class="col-12 col-xl-2 col-md-4">
                    <q-input
                        outlined
                        v-model="form.event_type"
                        label="Tipo"
                        disable
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição do evento"
                        :bottom-slots="Boolean(errors.description)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.description }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <div class="q-gutter-sm row items-center">
                        <div> Participação </div>

                        <q-radio
                            v-model="form.type"
                            :val="optionType.value"
                            :label="optionType.label"
                            v-for="optionType in optionsTypes"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-6" v-if="form.type == 'individual'">
                    <q-select
                        :options="optionsStudents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.student_ids"
                        label="Aluno"
                        :bottom-slots="Boolean(errors.student_ids)"
                        multiple
                        clearable
                        use-input
                        @filter="filterStudents"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.student_ids }} </div>
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

                <div class="col-12 col-md-6" v-if="form.type == 'grupo'">
                    <q-select
                        :options="optionsGroups"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.group_ids"
                        label="Grupos"
                        :bottom-slots="Boolean(errors.group_ids)"
                        multiple
                        clearable
                        use-input
                        @filter="filterGroups"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.group_ids }} </div>
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
                    <q-select
                        :options="optionsResponsible"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.responsible_id"
                        label="Responsável pelo evento"
                        :bottom-slots="Boolean(errors.responsible_id)"
                        clearable
                        use-input
                        @filter="filterResponsible"
                        stack-label
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.responsible_id }} </div>
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
                    <q-select
                        :options="optionsCampaigns"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.campaign_ids"
                        label="Campanhas"
                        :bottom-slots="Boolean(errors.campaign_ids)"
                        multiple
                        clearable
                        use-chips
                        use-input
                        @filter="filterCampaigns"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.campaign_ids }} </div>
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
                        v-model="form.embed"
                        label="Link embed"
                        :bottom-slots="Boolean(errors.embed)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.embed }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-checkbox
                        v-model="form.has_link_with_content"
                        label="Vincular encontro à um conteúdo"
                    />
                </div>

                <div class="col-12 col-md-5" v-if="form.has_link_with_content">
                    <q-select
                        :options="optionsContents"
                        option-value="id"
                        option-label="name"
                        emit-value
                        map-options
                        outlined
                        v-model="form.content_id"
                        label="Vincular ao conteúdo"
                        :bottom-slots="Boolean(errors.content_id)"
                        clearable
                        use-input
                        @filter="filterContents"
                        @update:model-value="fetchTypeSelected"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.content_id }} </div>
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

                <div class="col-12 col-md-4" v-if="form.has_link_with_content">
                    <q-select
                        v-if="form.content_id"
                        :options="optionsLinkableTypes"
                        emit-value
                        map-options
                        outlined
                        v-model="form.linkable_type"
                        label="Momento de vinculação do evento"
                        :bottom-slots="Boolean(errors.linkable_type)"
                        @update:model-value="fetchTypeSelected"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.linkable_type }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-3" v-if="form.has_link_with_content">
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
                        Imagem de capa
                    </div>
                </div>

                <div class="col-12 col-md-12" v-if="imageSrc">
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

                <div class="col-12 items-center">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Material
                    </div>
                </div>

                <div class="col-12 col-md-3" v-for="(material, index) in form.materials" :key="`material-${index}}}`">
                    <q-img
                        :src="material.is_image ? material.link : imageForDoc"
                        style="height: 240px"
                        class="adm-br-16"
                    >
                        <div class="absolute-bottom text-subtitle2 row items-center">
                            <q-icon name="attach_file" size="md" class="rotate-225 q-mr-md"/>

                            <div class="column">
                                <span class="text-caption">
                                    {{ material.name.length > 25 ? material.name.slice(0, 25) + '...' : material.name }}
                                </span>

                                <span class="text-caption">
                                    ({{ material.size ?? 0 }} kb)
                                </span>
                            </div>

                            <q-btn
                                color="white"
                                class="absolute"
                                style="top: 8px; right: 8px"
                                flat
                                icon="close"
                                size="md"
                                @click="removeMaterial(index)"
                            />
                        </div>
                    </q-img>
                </div>

                <div class="col-12">
                    <div
                        v-bind="getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind="getInputProps()"/>

                        <q-icon name="attach_file" size="md" class="rotate-225"/>

                        <div class="q-mt-sm" v-if="isDragActive">
                           Selecionando...
                        </div>

                        <div class="q-mt-sm" v-else>
                            Clique aqui ou arraste seus arquivos
                        </div>
                    </div>
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
                            Criar evento ao vivo
                        </div>
                    </q-btn>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
