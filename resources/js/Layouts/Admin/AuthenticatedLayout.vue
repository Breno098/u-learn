<script setup>
    import { useForm } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue';
    import { date } from 'quasar';
    import { Inertia } from '@inertiajs/inertia';
    import { useQuasar } from 'quasar'

    // const $q = useQuasar()

    // Inertia.on('error', (errors) => {
    //     for (const key in errors.detail.errors) {
    //         $q.notify({type: 'negative', message: errors.detail.errors[key], position: 'top'})
    //     }
    // })

    const dateNow = date.formatDate(Date.now(), 'dddd, DD [de] MMMM [de] YYYY')

    const leftDrawerOpen = ref(true)

    const toggleLeftDrawer = () => {
        leftDrawerOpen.value = !leftDrawerOpen.value
    }

    const logout = () => useForm().post(route('admin.auth.logout'));

    const goRoute = (routeName) => useForm().get(route(routeName));

</script>

<template>
    <q-layout view="lhh lpR lFf">
        <q-header class="bg-white">
            <q-toolbar  class="adm-top-bar">
                <q-btn
                    dense
                    flat
                    icon="menu_open"
                    color="indigo"
                    @click="toggleLeftDrawer"
                    :class="{
                        'rotate-180': !leftDrawerOpen
                    }"
                />

                <q-toolbar-title class="text-primary row items-center">
                    <!-- <q-icon
                        name="o_calendar_today"
                        color="grey"
                    />

                    <div class="q-ml-sm adm-fs-16 adm-fw-400 text-grey-8">
                        {{ dateNow }}
                    </div> -->
                </q-toolbar-title>

                <q-btn round dense flat class="q-mx-xs">
                    <q-icon name="notifications" class="material-icons-outlined" color="indigo"/>
                    <q-tooltip>Notificações</q-tooltip>
                </q-btn>

                <q-btn flat round  class="q-ml-sm">
                    <q-avatar size="26px">
                        <img src="https://cdn.quasar.dev/img/avatar2.jpg">
                    </q-avatar>

                    <q-icon name="keyboard_arrow_down" color="indigo" class="q-ml-xs"/>

                    <q-tooltip>Perfil</q-tooltip>

                    <q-menu>
                        <div class="row no-wrap q-pa-md">
                            <div class="column items-center q-pa-md q-px-xl">
                                <q-avatar size="72px">
                                    <img src="https://cdn.quasar.dev/img/avatar2.jpg">
                                </q-avatar>

                                <div class="text-subtitle1 q-mt-md q-mb-md text-center">
                                    {{ $page.props.auth.user.name.split(' ').shift() }}
                                </div>

                                <q-btn
                                    color="indigo"
                                    v-close-popup
                                    @click="logout"
                                    label="Sair"
                                />
                            </div>
                        </div>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>

        <q-drawer
            v-model="leftDrawerOpen"
            :width="260"
            :breakpoint="500"
            class="bg-indigo-8"
        >
            <q-scroll-area class="fit">
                <!-- <q-img
                    src="/img/admin/logo_ligia_academy.png"
                    style="max-width: 150px; max-height: 80px"
                    class="q-mx-xl q-mt-md "
               /> -->

                <q-list
                    padding
                    v-for="(menuItem, index) in $page.props.menu"
                    :key="index"
                >
                    <q-item-label
                        v-if="menuItem.label"
                        header
                        overline
                        class="text-white q-pb-sm"
                    >
                        {{ menuItem.label }}
                    </q-item-label>

                    <div v-for="(item, i) in menuItem.items">
                        <q-item
                            v-ripple
                            :active="route().current(item.active)"
                            active-class="bg-indigo-10"
                            class="cursor-pointer q-pl-xl"
                            @click="goRoute(item.route)"
                            clickable
                        >
                            <q-item-section avatar>
                                <q-icon :name="item.icon" color="white"/>
                            </q-item-section>

                            <q-item-section class="text-white adm-fs-16">
                                {{ item.label }}
                            </q-item-section>
                        </q-item>
                    </div>
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <q-page-container>
            <q-page class="adm-main-container bg-indigo-1">
                <slot />
            </q-page>
        </q-page-container>
    </q-layout>
</template>
