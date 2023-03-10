<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue'

    const props = defineProps({
        product: Object,
    });

    const form = useForm({
        id: props.product.id,
        name: props.product.name,
        description: props.product.description,
        url_sale: props.product.url_sale,
        terms_acceptance: props.product.terms_acceptance,
        video_url: props.product.video_url,
        credit_card_accept: props.product.credit_card_accept,
        credit_card_value: props.product.credit_card_value.toFixed(2),
        credit_card_installments: props.product.credit_card_installments,
        boleto_accept: props.product.boleto_accept,
        boleto_value: props.product.boleto_value.toFixed(2),
        boleto_installments: props.product.boleto_installments,
        pix_accept: props.product.pix_accept,
        pix_value: props.product.pix_value.toFixed(2),
        checkout_code: props.product.checkout_code,
        checkout_link: props.product.checkout_link,
        image: props.product.image,
    });

    const imageSrc = ref(props.product.image);

    const paymentMethods = ref(['Cartão de crédito', 'Boleto', 'PIX']);

    const formPaymentMethods = computed({
        get: () => {
            let returnData = [];

            if (form.credit_card_accept) {
                returnData.push('Cartão de crédito');
            }

            if (form.boleto_accept) {
                returnData.push('Boleto');
            }

            if (form.pix_accept) {
                returnData.push('PIX');
            }

            return returnData;
        },
    })

    const installments12times = computed(() => Array.from(Array(12).keys()).map(i => ({label: `Até ${i + 1}x`, value: i + 1})))
    const installments24times = computed(() => Array.from(Array(24).keys()).map(i => ({label: `Até ${i + 1}x`, value: i + 1})))

    const goBack = () => useForm().get(route('admin.product.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Produto | Visualizar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Visualizar produto </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Produtos" class="text-grey"/>
                    <q-breadcrumbs-el label="Visualizar produto" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="primary"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="goBack"
                >
                    <q-icon name="chevron_left" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Voltar
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-28">
                        Informações
                    </div>
                </div>

                <div class="col-12 col-md-7">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do produto"
                        disable
                    />
                </div>

                <div class="col-12 col-md-5">
                    <q-select
                        :options="paymentMethods"
                        outlined
                        v-model="formPaymentMethods"
                        label="Formas de pagamento"
                        disable
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição"
                        disable
                    />
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-28">
                        Valores
                    </div>
                </div>

                <div class="col-12 col-md-6" v-if="form.credit_card_accept">
                    <q-input
                        outlined
                        v-model="form.credit_card_value"
                        label="Valor para pagamento em cartão"
                        disable
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    />
                </div>

                <div class="col-12 col-md-6" v-if="form.credit_card_accept">
                    <q-select
                        :options="installments24times"
                        outlined
                        v-model="form.credit_card_installments"
                        label="Quantidade de parcelas para cartão"
                        disable
                        emit-value
                        map-options
                    />
                </div>

                <div class="col-12 col-md-6" v-if="form.boleto_accept">
                    <q-input
                        outlined
                        v-model="form.boleto_value"
                        label="Valor para pagamento em boleto"
                        disable
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    />
                </div>

                <div class="col-12 col-md-6" v-if="form.boleto_accept">
                    <q-select
                        :options="installments12times"
                        outlined
                        v-model="form.boleto_installments"
                        label="Quantidade de parcelas para boleto"
                        disable
                        emit-value
                        map-options
                    />
                </div>

                <div class="col-12 col-md-12" v-if="form.pix_accept">
                    <q-input
                        outlined
                        v-model="form.pix_value"
                        label="Valor para pagamento em pix"
                        disable
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.checkout_code"
                        label="Código do checkout"
                        disable
                    />
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.checkout_link"
                        label="Link"
                        disable
                    />
                </div>

                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-28">
                        Imagem da vitrine
                    </div>
                </div>

                <div class="col-12 col-md-12" v-if="imageSrc">
                    <q-img
                        :src="imageSrc"
                        style="height: 400px"
                        class="adm-br-16"
                    >
                        <div class="absolute-bottom text-subtitle2 row items-center">
                            <q-icon name="attach_file" size="md" class="rotate-225 q-mr-md"/>
                        </div>
                    </q-img>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.video_url"
                        label="Video do produto"
                        disable
                    />
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.terms_acceptance"
                        label="Termo de aceite"
                        disable
                        type="textarea"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
