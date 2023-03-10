<script setup>
    import { useForm } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue'

    const leftDrawerOpen = ref(true)

    const toggleLeftDrawer = () => {
        leftDrawerOpen.value = !leftDrawerOpen.value
    }

    const logout = () => {
        useForm().post(route('customer.auth.logout'));
    }
</script>

<template>
    <q-layout view="hHh lpR lFf">
        <q-header bordered class="bg-brown-8 text-white">
            <q-toolbar class="q-pa-md">
                <q-toolbar-title>
                    {{ $page.props.title }}
                </q-toolbar-title>

                <q-btn icon="account_circle" flat >
                    <q-menu>
                        <div class="row no-wrap q-pa-md">
                            <div class="column">
                                <div class="text-h6 q-mb-md">Settings</div>
                                <!-- <q-toggle v-model="mobileData" label="Use Mobile Data" />
                                <q-toggle v-model="bluetooth" label="Bluetooth" /> -->
                            </div>

                            <q-separator vertical inset class="q-mx-lg" />

                            <div class="column items-center q-pa-lg q-px-xl">
                                <q-avatar size="72px">
                                    <img src="https://cdn.quasar.dev/img/avatar2.jpg">
                                </q-avatar>

                                <div class="text-subtitle1 q-mt-md q-mb-md text-center">
                                    {{ $page.props.auth.user.name.split(' ').shift() }}
                                </div>

                                <q-btn
                                    color="brown-8"
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

        <q-page-container>
            <q-page padding>
                <slot />
            </q-page>
        </q-page-container>

        <!-- <q-footer bordered class="bg-primary text-white">
            <q-toolbar>
                <q-toolbar-title>
                    <q-avatar>
                        <q-btn icon="account_circle" flat >
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
                    </q-avatar>
                </q-toolbar-title>
            </q-toolbar>
        </q-footer> -->
    </q-layout>
</template>
