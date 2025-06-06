import type IMarca from "./IMarca"
import type IPessoa from "./IPessoa"

export default interface IVeiculo {
    id: number
    
    modelo: string
    placa: string
    renavam: string
    ano: number
    cor: string
    tipo_combustivel: string
    pessoa_id: number
    n_revisoes: number
    pessoa: IPessoa | null;
    marca: IMarca | null;
    created_at: string
    updated_at: string
}
