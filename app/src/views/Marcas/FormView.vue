<script lang="ts" setup>
import HeaderModule from '@/components/data-table/HeaderModule.vue'
import BackButton from '@/components/form-components/buttons/BackButton.vue'
import FormTemplate from '@/components/form-components/form/FormTemplate.vue'
import api from '@/plugins/api'
import { useAlertStore } from '@/stores/alertState'
import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import CloseModal from '@/components/CloseModal.vue'
import type IMarca from '@/types/IMarca'
import { validEmptyFieldsForm } from '@/helpers/functions/validFormData'
import SelectInput from '@/components/form-components/SelectInput.vue'
import { countries } from '@/data/options_selects'
import TextInput from '@/components/form-components/TextInput.vue'
import patterns, { validInterval } from '@/helpers/regexp/patterns'

const alertStore = useAlertStore()
const router = useRouter()
const route = useRoute()

const getIdByRoute = () => route.query.id || route.params.id

const form = reactive({
    nome: '',
    ano_fundacao: '',
    pais: ''
})

const errors = ref<Record<string, string>>({})
const loading = ref(false)

const fetchMarca = async () => {
    const id = getIdByRoute()
    if (!id) return
    try {
        const response = await api.get<IMarca>(`/marcas/${id}`)
        form.nome = response.data.nome
        form.ano_fundacao = response.data.ano_fundacao.toString()
        form.pais = response.data.pais
    } catch (error) {
        console.error((error as Error).message)
    }
}

function voltar() {
    router.back()
}

function validateForm(): boolean {
    const requiredFields = ['nome', 'ano_fundacao', 'pais']
    const formErrors: Record<string, string> = validEmptyFieldsForm(form, requiredFields)


    if (!patterns.integer.valid(form.ano_fundacao))
        formErrors['ano_fundacao'] = 'Ano de fundação deve ser um número'
    else if (!validInterval(form.ano_fundacao, 1880, 2025))
        formErrors['ano_fundacao'] = 'O ano deve estar no intervalo de 1880 a 2025'

    if (!/^[\p{Lu}\s]+$/u.test(form.nome))
        formErrors.nome = "Digite letras validas"
    errors.value = formErrors
    return Object.keys(formErrors).length === 0
}

const createMarca = async (formData: any) => {
    try {
        const response = await api.post('/marcas', formData)
        const marcaId = response.data.id

        alertStore.setMessage('Marca cadastrada com sucesso!', null)
        router.push({
            name: 'marcas.show', params: { id: marcaId }, replace: true
        })

    } catch (erro: any) {
        throw erro
    }
}

const updateMarca = async (id: string, formData: any) => {
    try {
        await api.put(`/marcas/${id}`, formData)
        alertStore.setMessage('Marca editada com sucesso', null)
        router.back()
    } catch (erro: any) {
        throw erro
    }
}

const submitForm = async () => {
    if (!validateForm()) return
    loading.value = true
    try {
        const id = getIdByRoute() as string
        const formData = {
            ...form,
            nome: form.nome.toUpperCase(),
            pais: form.pais.toUpperCase()
        }
        if (id) {
            await updateMarca(id, formData)
        } else {
            await createMarca(formData)
        }
    } catch (erro: any) {
        if (erro.status === 422 && erro.response?.data?.errors) {
            Object.assign(errors.value, erro.response.data.errors)
        }
    } finally {
        loading.value = false
    }
}

const cancelarProcesso = () => router.back()

const deleteModal = ref<InstanceType<typeof CloseModal> | null>(null)
const openDeleteModal = () => deleteModal.value?.openModal()

const confirmDelete = async () => {
    try {
        const id = getIdByRoute()
        if (!id) return

        await api.delete(`/marcas/${id}`)
        alertStore.setMessage('Marca deletada com sucesso', null)
        router.push({
            name: 'marcas.index',
            replace: true
        })

    } catch (exception: any) {
        if (exception.response?.status === 400 && exception.response?.data?.message) {
            const message = exception.response?.data?.message
            alertStore.setMessage(message, 'danger')
        } else {
            alertStore.setMessage('Não foi possível excluir a marca.', 'danger')
        }
    }
}

onMounted(() => {
    if (!getIdByRoute()) return
    fetchMarca()
})
</script>

<template class="mt-4">
    <HeaderModule class="mb-4">
        <template #title>
            <h1 class="text-3xl font-bold">{{ !getIdByRoute() ? 'Cadastrar' : 'Editar' }} Marca</h1>
        </template>
        <template #actions>
            <BackButton label="Voltar" @click="voltar" />
        </template>
    </HeaderModule>
    <main class="min-h-[calc(100vh-56px)] pb-10">
        <Suspense>
            <template #default>
                <FormTemplate :open-delete-modal="openDeleteModal" :create="!getIdByRoute()" header="Dados da Marca"
                    @submit.prevent="submitForm" class="container" :cancelarProcesso="cancelarProcesso">
                    <TextInput class="px-3" label="Nome da Marca" v-model="form.nome" :message="errors.nome" uppercase
                        placeholder="Digite o nome da marca" :required="true" :show-max-size="true" :max-size="20" />

                    <TextInput class="px-3" label="Ano de Fundação" v-model="form.ano_fundacao"
                        :message="errors.ano_fundacao" placeholder="Digite o ano de fundação (ex: 1990)" type="integer"
                        :required="true" :max-value="2025" />

                    <SelectInput class="px-3" label="País de Origem" v-model="form.pais" :options="countries"
                        :message="errors.pais" placeholder="Selecione o país de origem" :required="true"
                        :show-max-size="true" />
                </FormTemplate>
            </template>

            <template #fallback>
                <div>Carregando...</div>
            </template>
        </Suspense>

        <CloseModal ref="deleteModal" :confirm-delete="confirmDelete">
            Deseja realmente excluir esta marca? Esta ação é irreversível.
        </CloseModal>
    </main>
</template>
