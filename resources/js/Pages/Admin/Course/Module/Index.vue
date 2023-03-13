<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';
    import draggable from 'vuedraggable'

    const $q = useQuasar()

    const props = defineProps({
        modules: Object,
        course: Object,
        errors: Object,
        query: Object,
    });

    const create = () =>  useForm().get(route('admin.course.module.create', {
        course: props.course.id
    }));

    const edit = (id) =>  useForm().get(route('admin.course.module.edit', {
        course: props.course.id,
        module: id
    }));

    const destroy = (id) => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir módulo',
                message: 'Ao excluir módulo, as aulas pertencentes a ele também serão excluídas. Tem certeza disso?',
                confirm: true,
                icon: { name: 'close', color: 'red' },
            },
        }).onOk(() => {
            useForm().delete(route('admin.course.module.destroy', {
                course: props.course.id,
                module: id
            }), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: 'Módulo excluído com sucesso',
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })

                    restoreModules();
                }
            })
        });
    }

    const goBackCourse = () => useForm().get(route('admin.course.edit', {
        course: props.course.id
    }));

    const canDrag = ref(false)

    const modulesDrag = ref(props.modules);

    const storeReorder = () => {
        useForm({
            modules: modulesDrag.value
        }).post(route('admin.course.module.reorder', {
            course: props.course.id
        }), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Módulo ordenados com sucesso',
                        icon: { name: 'check', color: 'green' },
                        timeout: 2000
                    }
                })

                restoreModules();
            }
        });
    }

    const restoreModules = () => {
        canDrag.value = false;
        modulesDrag.value = props.modules
    }
</script>

<template>
    <Head :title="`Módulos do curso ${course.name}`" />

    <AuthenticatedLayout>
        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_view_agenda" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md"> Módulos do curso {{ course.name }} </div>
                </div>

                <div class="col-12 col-md-6 flex justify-end items-center">
                    <q-btn
                        color="indigo"
                        no-caps
                        @click="create"
                        icon="add"
                        label="Novo módulo"
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
                    :label="`Volta para curso ${course.name}`"
                    no-caps
                    @click="goBackCourse"
                />

                <q-btn
                    dense
                    color="indigo-10"
                    class="absolute inset-shadow-down"
                    icon="swipe_vertical"
                    style="top: 0; right: 12px; transform: translateY(-50%);"
                    label="Reordenar módulos"
                    no-caps
                    @click="canDrag = true"
                    v-if="!canDrag"
                />

                <q-btn
                    dense
                    color="indigo"
                    class="absolute inset-shadow-down"
                    icon="check"
                    style="top: 0; right: 12px; transform: translateY(-50%);"
                    label="Salvar ordenação"
                    no-caps
                    @click="storeReorder"
                    v-if="canDrag"
                />

                <q-btn
                    dense
                    color="grey-8"
                    class="absolute inset-shadow-down"
                    icon="close"
                    style="top: 0; right: 12px; transform: translateY(80%);"
                    label="Cancelar"
                    no-caps
                    @click="restoreModules"
                    v-if="canDrag"
                />

                <draggable
                    v-model="modulesDrag"
                    item-key="number"
                    :disabled="!canDrag"
                    :class="{
                        'q-mt-xl': canDrag
                    }"
                >
                    <template #item="{element, index}">
                        <q-card
                            flat
                            bordered
                            class="q-mt-sm"
                        >
                            <q-card-section class="q-pa-none">
                                <q-expansion-item header-class="q-pa-none">
                                    <template v-slot:header>
                                        <div class="row full-width">
                                            <div class="col-1 flex flex-center" v-if="canDrag">
                                                <q-icon name="swipe_vertical" color="indigo" size="md"/>
                                            </div>

                                            <div class="col-2 ">
                                                <q-img :src="element.image" style="min-height: 152px;"/>
                                            </div>

                                            <div
                                                class="column justify-center q-pl-md"
                                                :class="{'col-9': !canDrag, 'col-8': canDrag}"
                                            >
                                                <div class="adm-fs-16 adm-fw-700 text-blue-grey-10">
                                                    Temporada {{ element.number }}: {{ element.name }}
                                                </div>
                                                <div class="text-blue-grey-10">
                                                    {{ element.lessons.length ?? 0 }} {{ element.lessons.length > 1 ? 'episódios' : 'episódio' }}
                                                </div>
                                            </div>

                                            <div class="col-1 flex items-center justify-end">
                                                <q-btn icon="more_vert" flat>
                                                    <q-menu :offset="[45, 0]">
                                                        <q-list>
                                                            <q-item
                                                                clickable
                                                                @click="edit(element.id)"
                                                                class="text-blue-grey-10 flex items-center"
                                                            >
                                                                <q-icon name="edit" size="xs" color="indigo" />

                                                                <q-item-section no-wrap>
                                                                    <div class="q-ml-sm"> Editar </div>
                                                                </q-item-section>
                                                            </q-item>

                                                            <q-separator/>

                                                            <q-item
                                                                clickable
                                                                @click="destroy(element.id)"
                                                                class="text-blue-grey-10 flex items-center"
                                                            >
                                                                <q-icon name="close" size="xs" color="red"/>

                                                                <q-item-section no-wrap>
                                                                    <div class="q-ml-sm"> Excluir </div>
                                                                </q-item-section>
                                                            </q-item>
                                                        </q-list>
                                                    </q-menu>
                                                </q-btn>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-slot:default>
                                        <div>
                                            <div v-if="element.lessons.length == 0" class="adm-br-16 flex flex-center text-blue-grey-10">
                                                Nenhuma aula adicionada
                                            </div>

                                            <q-markup-table v-else class="text-blue-grey-10" flat>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            <div class="adm-fs-16">
                                                                Número
                                                            </div>
                                                        </th>
                                                        <th class="text-left">
                                                            <div class="adm-fs-16">
                                                                Capa
                                                            </div>
                                                        </th>
                                                        <th class="text-left">
                                                            <div class="adm-fs-16">
                                                                Nome do episódio
                                                            </div>
                                                        </th>
                                                        <th class="text-left">
                                                            <div class="adm-fs-16">
                                                                Duração
                                                            </div>
                                                        </th>
                                                        <th class="text-left">
                                                            <div class="adm-fs-16">
                                                                Ação
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="lesson in element.lessons">
                                                        <td class="text-left">{{ lesson.number }}</td>
                                                        <td class="text-left" style="padding: 0">
                                                            <q-img
                                                                :src="lesson.wallpaper"
                                                                style="height: 80px; width: 150;"
                                                            />
                                                        </td>
                                                        <td class="text-left">{{ lesson.name }}</td>
                                                        <td class="text-left">{{ lesson.duration }}</td>
                                                        <td>
                                                            <q-btn icon="more_vert" flat>
                                                                <q-menu :offset="[45, 0]">
                                                                    <q-list>
                                                                        <q-item
                                                                            clickable
                                                                            class="text-blue-grey-10 flex items-center"
                                                                        >
                                                                            <q-icon name="edit" size="xs" color="indigo" />

                                                                            <q-item-section no-wrap>
                                                                                <div class="q-ml-sm"> Editar </div>
                                                                            </q-item-section>
                                                                        </q-item>

                                                                        <q-separator/>

                                                                        <q-item
                                                                            clickable
                                                                            class="text-blue-grey-10 flex items-center"
                                                                        >
                                                                            <q-icon name="close" size="xs" color="red"/>

                                                                            <q-item-section no-wrap>
                                                                                <div class="q-ml-sm"> Excluir </div>
                                                                            </q-item-section>
                                                                        </q-item>
                                                                    </q-list>
                                                                </q-menu>
                                                            </q-btn>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </q-markup-table>
                                        </div>
                                    </template>
                                </q-expansion-item>
                            </q-card-section>
                        </q-card>
                    </template>
                </draggable>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
