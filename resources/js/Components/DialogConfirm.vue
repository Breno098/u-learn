<script setup>
  import { useDialogPluginComponent } from 'quasar'

  const props = defineProps({
    title: String,
    message: String,
    html: {
        type: Boolean,
        default: false
    }
  })

  defineEmits([
    ...useDialogPluginComponent.emits
  ])

  const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
</script>

<template>
    <q-dialog ref="dialogRef" @hide="onDialogHide">
        <q-card
            class="q-dialog-plugin"
            style="width: 550px; padding: 16px 48px; border-radius: 16px;"
        >
            <q-card-section class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8 text-center">
                {{ title }}
            </q-card-section>

            <q-card-section style="font-size: 16px;">
                <div
                    class="text-grey-8 text-center"
                    v-if="html"
                    v-html="message"
                ></div>

                <div class="text-grey-8 text-center" v-else>
                    {{ message }}
                </div>
            </q-card-section>

            <q-card-actions align="center">
                <q-btn
                    color="green-13"
                    rounded
                    no-caps
                    @click="onDialogOK"
                    unelevated
                >
                    <q-icon name="check" size="xs" class="q-ml-sm"/>

                    <div class="text-caption q-mx-sm">
                        Sim
                    </div>
                </q-btn>

                <q-btn
                    color="negative"
                    rounded
                    no-caps
                    @click="onDialogCancel"
                    unelevated
                >
                    <q-icon name="close" size="xs" class="q-ml-sm"/>

                    <div class="text-caption q-mx-sm">
                        Não
                    </div>
                </q-btn>
            </q-card-actions>
        </q-card>
    </q-dialog>
  </template>


