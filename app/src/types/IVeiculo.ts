import type IPessoa from "./IPessoa"

export default interface IVeiculo {
    id: number
    marca: string
    modelo: string
    placa: string
    renavam: string
    ano: number
    cor: string
    tipo_combustivel: string
    pessoa_id: number
    pessoa: IPessoa | null;
    created_at: string
    updated_at: string
}
