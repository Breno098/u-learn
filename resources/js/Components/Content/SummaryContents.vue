<script setup>
  const props = defineProps({
    contentReleasedThisMonth: Object,
    contentsMostViewed: Object,
    contentsExpiresIn60Days: Object
  })
</script>

<template>
    <div class="row q-pb-lg q-col-gutter-lg">
        <!-- Lançamentos do mẽs -->
        <div class="col-12 col-md-4">
            <q-card flat class="adm-br-16 q-pa-md">
                <q-card-section class="adm-fw-700 adm-fs-16 text-grey-8 adm-lh-24 q-pt-sm">
                    Lançamentos do mês
                </q-card-section>

                <q-markup-table class="text-grey-8" dense>
                    <tbody>
                        <tr
                            v-for="content, index in contentReleasedThisMonth"
                            :key="`contents-relesead-this-month-${index}`"
                            :class="{'adm-fw-700 adm-fs-16': index === 0}"
                        >
                            <td class="text-left">
                                <q-img
                                    src="/img/admin/logo_ligia_academy.png"
                                    style="height: 56px; width: 56px;"
                                />
                            </td>
                            <td class="text-left">
                                {{ content.name }}
                            </td>
                            <td class="text-left">
                                {{ content.launch_start_at }}
                            </td>
                        </tr>
                    </tbody>
                </q-markup-table>
            </q-card>
        </div>


        <!-- Mais vistos -->
        <div class="col-12 col-md-4">
            <q-card flat class="adm-br-16 q-pa-md">
                <q-card-section class="adm-fw-700 adm-fs-16 text-grey-8 adm-lh-24 q-pt-sm">
                    Conteúdos mais vistos
                </q-card-section>

                <q-markup-table class="text-grey-8" dense>
                    <tbody>
                        <tr
                            v-for="content, index in contentsMostViewed"
                            :key="`most-viewed-${index}`"
                            :class="{'adm-fw-700 adm-fs-16': index === 0}"
                        >
                            <td class="text-left">
                                <q-img
                                    src="/img/admin/logo_ligia_academy.png"
                                    style="height: 56px; width: 56px;"
                                />
                            </td>
                            <td class="text-left">
                                {{ content.name }}
                            </td>
                            <td class="text-left">
                                {{ content.access_count }} views
                            </td>
                        </tr>
                    </tbody>
                </q-markup-table>
            </q-card>
        </div>

        <!-- Expira em  60 dias -->
        <div class="col-12 col-md-4">
            <q-card flat class="adm-br-16 q-pa-md">
                <q-card-section class="adm-fw-700 adm-fs-16 text-grey-8 adm-lh-24 q-pt-sm">
                    Expira em até 60 dias
                </q-card-section>

                <q-markup-table class="text-grey-8" dense>
                    <tbody>
                        <tr
                            v-for="content, index in contentsExpiresIn60Days"
                            :key="`contents-expires-in-60-days-${index}`"
                            :class="{'adm-fw-700 adm-fs-16': index === 0}"
                        >
                            <td class="text-left">
                                <q-img
                                    src="/img/admin/logo_ligia_academy.png"
                                    style="height: 56px; width: 56px;"
                                />
                            </td>
                            <td class="text-left">
                                {{ content.name }}
                            </td>
                            <td class="text-left">
                                {{ content.expires_in }} dias
                            </td>
                        </tr>
                    </tbody>
                </q-markup-table>
            </q-card>
        </div>
    </div>
</template>
