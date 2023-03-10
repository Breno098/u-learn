<script setup>
    import { useDialogPluginComponent } from 'quasar'
    import { computed, onMounted } from 'vue';

    const props = defineProps({
        title: String,
        message: String,
        icon: String|Object,
        timeout: Number,
        confirm: {
            type: Boolean,
            default: false
        },
        html: {
            type: Boolean,
            default: false
        }
    })

    defineEmits([
        ...useDialogPluginComponent.emits
    ])

    onMounted(() => {
        if (props.timeout) {
            setTimeout(() => {
                onDialogCancel();
            }, props.timeout);
        }
    })

    const iconName = computed(() => typeof props.icon === 'string' ?  props.icon : props.icon.name);
    const iconColor = computed(() => typeof props.icon === 'string' ?  '' : props.icon.color);

    const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
</script>

<template>
    <q-dialog
        ref="dialogRef"
        @hide="onDialogHide"
        transition-show="slide-down"
        transition-hide="slide-down"
        :transition-duration="1000"
    >
        <q-card
            class="q-dialog-plugin q-py-md"
            style="width: 600px; max-width: 90vw"
        >
            <q-card-section class="flex flex-center q-py-lg" v-if="icon">
                <q-icon :name="iconName" size="5em" :color="iconColor" />
            </q-card-section>

            <q-card-section class="adm-fs-28 adm-fw-700 adm-lh-32 text-blue-grey-10 text-center">
                {{ title }}
            </q-card-section>

            <q-card-section style="font-size: 16px;" class="q-py-lg">
                <div
                    class="text-blue-grey-10 text-center"
                    v-if="html"
                    v-html="message"
                ></div>

                <div class="text-blue-grey-10 text-center" v-else>
                    {{ message }}
                </div>
            </q-card-section>

            <q-card-actions class="flex flex-center" v-if="confirm">
                <q-btn
                    color="green"
                    no-caps
                    @click="onDialogOK"
                    label="Sim"
                    outline
                    icon="thumb_up_alt"
                    style="width: 150px"
                />

                <q-btn
                    color="red"
                    no-caps
                    @click="onDialogCancel"
                    label="Não"
                    outline
                    icon="thumb_down_alt"
                    style="width: 150px"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
  </template>


