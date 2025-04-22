import type IVeiculo from "./IVeiculo"

export default interface IRevisao {
    id: number
    data: string
    quilometragem: number
    tipo: string
    descricao: string
    observacoes: string
    valor_total: number
    garantia_meses: number
    veiculo_id: number
    veiculo?: IVeiculo | null
    created_at: string
    updated_at: string
}
