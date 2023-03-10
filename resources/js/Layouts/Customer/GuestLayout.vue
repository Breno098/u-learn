<script setup>
</script>

<template>
    <q-layout class="my-bg">
        <q-page-container>
            <q-page class="window-height window-width flex flex-center">
                <slot />
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<style>
    .my-bg {
        background: url('/img/wallpaper-login-customer.jpg') no-repeat;
        background-size: cover;
    }
</style>
