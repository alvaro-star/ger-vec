import { defineStore } from 'pinia'

export const useAlertStore = defineStore('message', {
    state: () => ({
        mensagem: '',
        tipo: 'danger' as 'danger' | 'alert' | 'success'
    }),
    actions: {
        setMessage(mensagem: string, tipo: 'danger' | 'alert' | 'success' | null) {
            this.mensagem = mensagem
            this.tipo = tipo ?? 'alert';
        },

        clearMessage() {
            this.mensagem = ''
            this.tipo = 'danger'
        }
    }
})
