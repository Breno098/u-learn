<script setup>
    import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/inertia-vue3';
    import { useQuasar } from 'quasar'
    import { Inertia } from '@inertiajs/inertia';
    import { ref, computed } from 'vue'
    import { useDropzone } from "vue3-dropzone";
    import DialogConfirm from '@/Components/DialogConfirm.vue';

    const $q = useQuasar()

    const props = defineProps({
        product: Object,
        errors: Object,
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

    const dropZoneImage = useDropzone({
        onDrop: (acceptFiles, rejectReasons) => {
            form.image = acceptFiles[0];

            if (rejectReasons.length > 0) {
                $q.notify({
                    message: 'Insira apenas uma imagem',
                    position: 'center',
                })
            }
        },
        accept: ['image/*'],
        maxFiles: 1
    });

    const imageSrc = computed(() => {
        if (! form.image)
            return '';

        return (typeof form.image === 'object') ? URL.createObjectURL(form.image) : form.image;
    });

    const removeImage = () => form.image = null

    const submit = () => {
        form
            .transform((data) => ({...data, _method: 'put' }))
            .post(route("admin.product.update", form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Produto atualizado com sucesso',
                        position: 'top',
                    })
                },
            })
    };

    function destroy() {
        $q.dialog({
            component: DialogConfirm,
            componentProps: {
                title: 'Excluir produto',
                message: 'Tem certeza que deseja excluir esse produto?',
            },
        }).onOk(() => {
            Inertia.delete(route('admin.product.destroy', form.id), {
                onSuccess: () => {
                    $q.notify({
                        type: 'positive',
                        message: 'Produto excluído com sucesso',
                        position: 'top',
                    })
                }
            })
        });
    }

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
        set: (newValue) => {
            form.credit_card_accept = newValue.includes('Cartão de crédito');
            form.pix_accept = newValue.includes('PIX');
            form.boleto_accept = newValue.includes('Boleto');
        },
    })

    const installments12times = computed(() => Array.from(Array(12).keys()).map(i => ({label: `Até ${i + 1}x`, value: i + 1})))
    const installments24times = computed(() => Array.from(Array(24).keys()).map(i => ({label: `Até ${i + 1}x`, value: i + 1})))

    const goBack = () => useForm().get(route('admin.product.index'))
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Produto | Editar" />

        <div class="row q-mb-lg">
            <div class="column col-12 col-md-6 justify-center">
                <div class="adm-fs-28 adm-fw-700 adm-lh-32 text-grey-8"> Editar produto </div>

                <q-breadcrumbs
                    class="text-grey q-mt-sm adm-fs-13 adm-fw-400 adm-lh-16"
                    gutter="xs"
                >
                    <q-breadcrumbs-el label="Home" class="text-grey"/>
                    <q-breadcrumbs-el label="Produtos" class="text-grey"/>
                    <q-breadcrumbs-el label="Editar produto" class="text-primary" />
                </q-breadcrumbs>
            </div>

            <div class="col-12 col-md-6 flex justify-end items-center">
                <q-btn
                    color="negative"
                    class="q-mr-md"
                    rounded
                    no-caps
                    outline
                    @click="destroy"
                >
                    <q-icon name="close" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Excluir produto
                    </div>
                </q-btn>

                <q-btn
                    color="primary"
                    rounded
                    no-caps
                    @click="submit"
                    :disabled="form.processing"
                >
                    <q-icon name="check" size="xs"/>

                    <div class="q-ml-sm adm-fw-500 adm-fs-14 adm-lh-20">
                        Salvar
                    </div>
                </q-btn>
            </div>
        </div>

        <div class="bg-white q-py-lg q-px-lg adm-br-16">
            <div class="row q-col-gutter-lg">
                <div class="col-12 items-center q-mt-xs">
                    <div class="q-ml-sm text-grey-8 adm-fw-700 adm-lh-32 adm-fs-23">
                        Informações
                    </div>
                </div>

                <div class="col-12 col-md-7">
                    <q-input
                        outlined
                        v-model="form.name"
                        label="Nome do produto"
                        :bottom-slots="Boolean(errors.name)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.name }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-5">
                    <q-select
                        :options="paymentMethods"
                        outlined
                        v-model="formPaymentMethods"
                        label="Formas de pagamento"
                        multiple
                    />
                </div>

                <div class="col-12 col-md-12">
                    <q-input
                        outlined
                        v-model="form.description"
                        label="Descrição"
                        :bottom-slots="Boolean(errors.description)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.description }} </div>
                        </template>
                    </q-input>
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
                        :bottom-slots="Boolean(errors.credit_card_value)"
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.credit_card_value }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6" v-if="form.credit_card_accept">
                    <q-select
                        :options="installments24times"
                        outlined
                        v-model="form.credit_card_installments"
                        label="Quantidade de parcelas para cartão"
                        :bottom-slots="Boolean(errors.credit_card_installments)"
                        emit-value
                        map-options
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.credit_card_installments }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-6" v-if="form.boleto_accept">
                    <q-input
                        outlined
                        v-model="form.boleto_value"
                        label="Valor para pagamento em boleto"
                        :bottom-slots="Boolean(errors.boleto_value)"
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.boleto_value }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6" v-if="form.boleto_accept">
                    <q-select
                        :options="installments12times"
                        outlined
                        v-model="form.boleto_installments"
                        label="Quantidade de parcelas para boleto"
                        :bottom-slots="Boolean(errors.boleto_installments)"
                        emit-value
                        map-options
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.boleto_installments }} </div>
                        </template>
                    </q-select>
                </div>

                <div class="col-12 col-md-12" v-if="form.pix_accept">
                    <q-input
                        outlined
                        v-model="form.pix_value"
                        label="Valor para pagamento em pix"
                        :bottom-slots="Boolean(errors.pix_value)"
                        mask="#.##"
                        fill-mask="0"
                        reverse-fill-mask
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.pix_value }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.checkout_code"
                        label="Código do checkout"
                        :bottom-slots="Boolean(errors.checkout_code)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.checkout_code }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 col-md-6">
                    <q-input
                        outlined
                        v-model="form.checkout_link"
                        label="Link"
                        :bottom-slots="Boolean(errors.checkout_link)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.checkout_link }} </div>
                        </template>
                    </q-input>
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
                            <q-icon name="o_photo" size="md" class="q-mr-md"/>

                            <q-btn
                                color="white"
                                class="absolute inset-shadow-down"
                                style="top: 8px; right: 8px"
                                flat
                                icon="close"
                                size="md"
                                @click="removeImage"
                            />

                            <div class="flex cursor-pointer" v-bind="dropZoneImage.getRootProps()">
                                <input v-bind="dropZoneImage.getInputProps()"/>
                                Alterar imagem
                            </div>
                        </div>
                    </q-img>
                </div>

                <div class="col-12" v-else>
                    <div
                        v-bind="dropZoneImage.getRootProps()"
                        class="column flex-center q-py-lg text-grey adm-border-dashed-blue adm-br-16"
                    >
                        <input v-bind="dropZoneImage.getInputProps()"/>

                        <q-icon name="o_photo" size="md"/>

                        <div class="q-mt-sm">
                            Clique aqui ou arraste sua imagem
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.video_url"
                        label="Video do produto"
                        :bottom-slots="Boolean(errors.video_url)"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.video_url }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12">
                    <q-input
                        outlined
                        v-model="form.terms_acceptance"
                        label="Termo de aceite"
                        :bottom-slots="Boolean(errors.terms_acceptance)"
                        type="textarea"
                    >
                        <template v-slot:hint>
                            <div class="text-red"> {{ errors.terms_acceptance }} </div>
                        </template>
                    </q-input>
                </div>

                <div class="col-12 flex justify-end items-center">
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
        </div>
    </AuthenticatedLayout>
</template>
