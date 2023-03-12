<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { useQuasar } from 'quasar'
    import AdminDialog from '@/Components/AdminDialog.vue';

    const $q = useQuasar()

    const props = defineProps({
        modules: Object,
        course: Object,
        errors: Object,
        query: Object,

        canCourseStore: Boolean,
        canCourseEdit: Boolean,
        canCourseDestroy: Boolean,
    });

    const create = () => useForm().get(route('admin.course.create'));

    const edit = (id) => useForm().get(route('admin.course.edit', id));

    const destroy = (id) => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir curso',
                message: 'Tem certeza que deseja excluir essa curso?',
                confirm: true,
                icon: { name: 'close', color: 'red' },
            },
        }).onOk(() => {
            useForm().delete(route('admin.course.destroy', id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    $q.dialog({
                        component: AdminDialog,
                        componentProps: {
                            message: 'Curso excluído com sucesso',
                            icon: { name: 'check', color: 'green' },
                            timeout: 3000
                        }
                    })
                }
            })
        });
    }

    const destroySelected = () => {
        $q.dialog({
            component: AdminDialog,
            componentProps: {
                title: 'Excluir selecionados',
                message: 'Tem certeza que deseja excluir cursos selecionadas?',
                confirm: true,
                icon: { name: 'playlist_remove', color: 'red' }
            },
      }).onOk(() => {
        useForm({ ids: selected.value.map(s => s.id) }).post(route('admin.course.destroy-multiples'), {
            onSuccess: () => {
                selected.value = [];

                $q.dialog({
                    component: AdminDialog,
                    componentProps: {
                        message: 'Cursos excluídos com sucesso',
                        icon: { name: 'playlist_add_check', color: 'green' },
                        timeout: 3000
                    }
                })
            }
        })
      });
    }

    const selected = ref([])

    const goBackCourse = () =>  useForm().get(route('admin.course.edit', {
        course: props.course.id
    }));
</script>

<template>
    <Head :title="`Módulos de ${course.name}`" />

    <AuthenticatedLayout>
        <q-card flat class="q-mb-lg">
            <q-card-section class="row items-center q-px-lg">
                <div class="flex col-12 col-md-6 justify-start items-center">
                    <q-icon name="o_view_agenda" color="indigo" size="md"/>

                    <div class="adm-fs-28 text-blue-grey-10 q-ml-md"> Módulos de {{ course.name }} </div>
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
                    class="absolute"
                    icon="chevron_left"
                    style="top: 0; left: 12px; transform: translateY(-50%);"
                    :label="`Volta para curso ${course.name}`"
                    no-caps
                    @click="goBackCourse"
                />

                <q-card
                    v-for="_module in modules"
                    flat
                    bordered
                    class="q-mt-sm"
                >
                    <q-card-section class="q-pa-none">
                        <q-expansion-item header-class="q-pa-none">
                            <template v-slot:header>
                                <div class="row full-width">
                                    <div class="col-2 ">
                                        <q-img
                                            :src="_module.image"
                                            style="min-height: 152px;"
                                        />
                                    </div>

                                    <div class="col-9 column justify-center q-pl-md">
                                        <div class="adm-fs-16 adm-fw-700 text-blue-grey-10">
                                            Temporada {{ _module.number }}: {{ _module.name }}
                                        </div>
                                        <div class="text-blue-grey-10">
                                            {{ _module.lessons.length ?? 0 }} episódios
                                        </div>
                                    </div>

                                    <div class="col-1 flex items-center justify-end">
                                        <q-btn icon="more_vert" flat>
                                            <q-menu>
                                                <q-list>
                                                    <q-item
                                                        clickable
                                                        class="text-grey-7 flex flex-center"
                                                    >
                                                        <q-icon name="edit" size="xs"/>
                                                        <div class="q-ml-sm"> Editar </div>
                                                    </q-item>

                                                    <q-separator/>


                                                    <q-item
                                                        clickable
                                                        class="text-grey-7 flex flex-center"
                                                    >
                                                        <q-icon name="close" size="xs"/>
                                                        <div class="q-ml-sm"> Excluir </div>
                                                    </q-item>
                                                </q-list>
                                            </q-menu>
                                        </q-btn>
                                    </div>
                                </div>
                            </template>

                            <template v-slot:default>
                                <div>
                                    <div v-if="_module.lessons.length == 0" class="adm-br-16 flex flex-center text-blue-grey-10">
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
                                            <tr v-for="lesson in _module.lessons">
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
                                                        <q-menu>
                                                            <q-list>
                                                                <q-item
                                                                    clickable
                                                                    class="text-grey-7 flex flex-center"
                                                                >
                                                                    <q-icon name="edit" size="xs"/>
                                                                    <div class="q-ml-sm"> Editar </div>
                                                                </q-item>

                                                                <q-separator/>

                                                                <q-item
                                                                    clickable
                                                                    class="text-grey-7 flex flex-center"
                                                                >
                                                                    <q-icon name="close" size="xs"/>
                                                                    <div class="q-ml-sm"> Excluir </div>
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
            </q-card-section>
        </q-card>
    </AuthenticatedLayout>
</template>
