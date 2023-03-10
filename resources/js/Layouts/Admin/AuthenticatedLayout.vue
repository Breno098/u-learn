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

    const thumbStyle = {
        right: '4px',
        borderRadius: '5px',
        backgroundColor: '#4F8BE6',
        width: '5px',
        opacity: 0.75
    }

    const barStyle = {
        right: '2px',
        borderRadius: '9px',
        backgroundColor: '#cce7ff',
        width: '9px',
        opacity: 0.2
    }

</script>

<template>
    <q-layout view="lhh lpR lFf">
        <q-header class="bg-white">
            <q-toolbar style="height: 80px;" class="adm-top-bar">
                <q-btn dense flat icon="menu" color="grey" @click="toggleLeftDrawer" />

                <q-toolbar-title class="text-primary row items-center">
                    <q-icon
                        name="o_calendar_today"
                        color="grey"
                    />

                    <div class="q-ml-sm adm-fs-16 adm-fw-400 text-grey-8">
                        {{ dateNow }}
                    </div>
                </q-toolbar-title>

                <q-btn round dense flat color="grey" icon="search" class="q-mx-xs"/>

                <q-btn round dense flat class="q-mx-xs">
                    <q-icon name="notifications" class="material-icons-outlined" color="grey"/>
                    <q-tooltip>Notificações</q-tooltip>
                </q-btn>

                <q-btn flat round  class="q-ml-sm">
                    <q-avatar size="26px">
                        <img src="https://cdn.quasar.dev/img/avatar2.jpg">
                    </q-avatar>

                    <q-icon name="keyboard_arrow_down" color="grey" class="q-ml-xs"/>

                    <q-tooltip>Perfil</q-tooltip>

                    <q-menu>
                        <div class="row no-wrap q-pa-md">
                            <div class="column items-center q-pa-lg q-px-xl">
                                <q-avatar size="72px">
                                    <img src="https://cdn.quasar.dev/img/avatar2.jpg">
                                </q-avatar>

                                <div class="text-subtitle1 q-mt-md q-mb-md text-center">
                                    {{ $page.props.auth.user.name.split(' ').shift() }}
                                </div>

                                <q-btn
                                    color="primary"
                                    push
                                    size="sm"
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
            :width="256"
            :breakpoint="500"
            class="adm-drawer"
        >
            <q-scroll-area
                class="fit"
                :thumb-style="thumbStyle"
                :bar-style="barStyle"
            >
                <q-img
                    src="/img/admin/logo_ligia_academy.png"
                    style="max-width: 150px; max-height: 80px"
                    class="q-mx-xl q-mt-md "
               />

                <q-list
                    padding
                    v-for="(menuItem, index) in $page.props.menu"
                    :key="index"
                >
                    <q-item-label header overline class="q-pl-xl">
                        {{ menuItem.label }}
                    </q-item-label>

                    <div v-for="(item, i) in menuItem.items">
                        <q-item
                            v-ripple
                            :active="route().current(item.active)"
                            active-class="adm-active-link"
                            class="cursor-pointer adm-drawer-item"
                            @click="goRoute(item.route)"
                            clickable
                        >
                            <q-item-section avatar>
                                <q-icon
                                    :name="item.icon"
                                    :class="route().current(item.active) ? 'adm-active-color' : 'text-white'"
                                />
                            </q-item-section>

                            <q-item-section
                                :class="route().current(item.active) ? 'adm-active-color' : 'text-white'"
                                class="adm-fw-500 adm-fs-16 adm-lh-16"
                            >
                                {{ item.label }}
                            </q-item-section>
                        </q-item>

                        <q-separator class="adm-active-link"/>
                    </div>
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <q-page-container>
            <q-page class="adm-main-container">
                <slot />
            </q-page>
        </q-page-container>
    </q-layout>
</template>
