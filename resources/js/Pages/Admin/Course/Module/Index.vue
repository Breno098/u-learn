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
                        message: 'Módulos ordenados',
                        icon: { name: 'playlist_add_check', color: 'green' },
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

    const createLesson = () =>  useForm().get(route('admin.course.module.create', {
        course: props.course.id
    }));

    const editLesson = (lesson, module) =>  useForm().get(route('admin.course.module.lesson.edit', {
        course: props.course.id,
        module: module.id,
        lesson: lesson.id
    }));

    const destroyLesson = (lesson, module) => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir aula',
                message: `Deseja realmente excluir a aula ${lesson.name}?`,
                confirm: true,
                icon: { name: 'close', color: 'red' },
            },
        }).onOk(() => {
            useForm().delete(route('admin.course.module.lesson.destroy', {
                course: props.course.id,
                module: module.id,
                lesson: lesson.id
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

                <div v-if="canDrag" class="text-indigo row flex-center">
                    <div class="q-mr-md">
                        Arraste e solte para ordenar os módulos
                    </div>

                    <q-icon
                        name="swipe_vertical"
                        color="indigo"
                        size="md"
                    />
                </div>

                <draggable
                    v-model="modulesDrag"
                    item-key="number"
                    :disabled="!canDrag"
                    :class="{
                        'q-mt-xl': canDrag
                    }"
                >
                    <template #item="{element: moduleData, index}">
                        <q-card
                            flat
                            bordered
                            class="q-mt-md"
                            :class="{'cursor-pointer': canDrag}"
                        >
                            <q-card-section class="q-py-none q-px-none">
                                <div class="row">
                                    <div class="col-2 ">
                                        <q-img
                                            :src="moduleData.image"
                                            :style="{ height: canDrag ? '100px' : '150px' }"
                                        />
                                    </div>

                                    <div class="col-9 column justify-center q-pl-md text-blue-grey-10 adm-fs-16 adm-fw-700">
                                        Módulo {{ moduleData.number }}: {{ moduleData.name }}
                                    </div>

                                    <div class="col-1 flex flex-center">
                                        <q-btn icon="more_vert" flat v-if="!canDrag">
                                            <q-menu :offset="[95, 0]">
                                                <q-list>
                                                    <q-item
                                                        clickable
                                                        @click="edit(moduleData.id)"
                                                        class="text-blue-grey-10 flex items-center"
                                                    >
                                                        <q-icon name="edit" size="xs" color="indigo" />

                                                        <q-item-section no-wrap>
                                                            <div class="q-ml-sm"> Editar módulo </div>
                                                        </q-item-section>
                                                    </q-item>

                                                    <q-separator/>

                                                    <q-item
                                                        clickable
                                                        @click="destroy(moduleData.id)"
                                                        class="text-blue-grey-10 flex items-center"
                                                    >
                                                        <q-icon name="close" size="xs" color="red"/>

                                                        <q-item-section no-wrap>
                                                            <div class="q-ml-sm"> Excluir módulo </div>
                                                        </q-item-section>
                                                    </q-item>
                                                </q-list>
                                            </q-menu>
                                        </q-btn>

                                        <q-icon
                                            name="swipe_vertical"
                                            color="indigo"
                                            size="sm"
                                            v-else
                                        />
                                    </div>
                                </div>

                                <q-slide-transition>
                                    <div v-show="!canDrag">
                                        <div v-if="moduleData.lessons.length == 0" class="flex flex-center text-blue-grey-10">
                                            Nenhuma aula adicionada
                                        </div>

                                        <q-markup-table v-else class="text-blue-grey-10" flat>
                                            <thead>
                                                <tr>
                                                    <th colspan="5" class="text-left">
                                                        <div class="text-blue-grey-10 adm-fs-16 adm-fw-700 flex items-center">
                                                            <q-icon
                                                                name="class"
                                                                color="indigo"
                                                                size="xs"
                                                            />

                                                            <div class="q-ml-sm">
                                                                {{ moduleData.lessons.length }} {{ moduleData.lessons.length > 1 ? 'aulas' : 'aula' }}
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>

                                            <!-- @todo Fazer ordenação de aulas -->
                                            <tbody>
                                                <draggable
                                                    v-model="moduleData.lessons"
                                                    item-key="number"
                                                    :disabled="true"
                                                >
                                                    <template #item="{element: lesson, index}">
                                                        <tr>
                                                            <td class="text-left" style="width: 5%;">{{ lesson.number }}</td>
                                                            <td class="text-left" style="width: 10%; padding: 0">
                                                                <q-img
                                                                    :src="lesson.wallpaper"
                                                                    style="height: 50px;"
                                                                />
                                                            </td>
                                                            <td class="text-left" style="width: 85%;">{{ lesson.name }}</td>
                                                            <td>
                                                                <q-btn icon="more_vert" flat>
                                                                    <q-menu :offset="[75, 0]">
                                                                        <q-list>
                                                                            <q-item
                                                                                clickable
                                                                                class="text-blue-grey-10 flex items-center"
                                                                                @click="editLesson(lesson, moduleData)"
                                                                            >
                                                                                <q-icon name="edit" size="xs" color="indigo" />

                                                                                <q-item-section no-wrap>
                                                                                    <div class="q-ml-sm"> Editar aula </div>
                                                                                </q-item-section>
                                                                            </q-item>

                                                                            <q-separator/>

                                                                            <q-item
                                                                                clickable
                                                                                class="text-blue-grey-10 flex items-center"
                                                                                @click="destroyLesson(lesson, moduleData)"
                                                                            >
                                                                                <q-icon name="close" size="xs" color="red"/>

                                                                                <q-item-section no-wrap>
                                                                                    <div class="q-ml-sm"> Excluir aula </div>
                                                                                </q-item-section>
                                                                            </q-item>
                                                                        </q-list>
                                                                    </q-menu>
                                                                </q-btn>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </draggable>
                                            </tbody>
                                        </q-markup-table>

                                        <div class="flex flex-center">
                                            <q-btn
                                                color="indigo"
                                                no-caps
                                                icon="add"
                                                icon-right="class"
                                                label="Adicionar aula"
                                                outline
                                                class="full-width"
                                            />
                                        </div>
                                    </div>
                                </q-slide-transition>
                            </q-card-section>
                        </q-card>
                    </template>
                </draggable>
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
