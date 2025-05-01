import { formatarCelular, formatarCPF, formatarFloat, formatarInteger, validCustomFormat } from "../formatters"

// Tipo para campos de entrada comuns
type KeysValidator =
    | 'text'
    | 'integer'
    | 'float'
    | 'email'
    | 'placa'
    | 'cpf'
    | 'cep'
    | 'letter_only'
    | "letter_and_number_only"
    | 'phone'

export type InputType = KeysValidator | 'date'

interface IValidator {
    semiValid: (input: string) => boolean,
    valid: (input: string) => boolean,
    message: string,
    format?: (input: string, ...args: any) => string
}

const patterns: Record<KeysValidator, IValidator> = {
    integer: {
        valid: (input) => /^\d{1,3}(\.\d{3})*$/.test(input),
        semiValid: (input) => /^\d*$/.test(input),
        message: "O valor deve ser um número",
        format: formatarInteger
    },
    float: {
        valid: (input) => /^\d{1,3}(\.\d{3})*(\,\d+)?$/.test(input),
        semiValid: (input) => /^(\d+\,)?\d*?$/.test(input),
        message: "O valor deve ser um número",
        format: formatarFloat
    },
    text: {
        valid: (input) => true,
        semiValid: (input) => true,
        message: ""
    },
    email: {
        valid: (input) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input),
        semiValid: (input) => /^[^\s@]*@?[^\s@]*\.?[^\s@]*$/.test(input),
        message: "Por favor, insira um e-mail válido"
    },
    placa: {
        valid: (input) => /^[A-Z]{3}\d[A-Z0-9]\d{2}$/.test(input),
        semiValid: (input) => {
            let format = "AAANANN"
            return validCustomFormat(format, input)
        },
        message: "O formato do valor deve ser AAA0A00"
    },
    cpf: {
        valid: (input) => /^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(input),
        semiValid: (input) => {
            const format = "NNN.NNN.NNN-NN"
            return validCustomFormat(format, input)
        },
        format: formatarCPF,
        message: "O formato deve ser 000.000.000-00"
    },
    cep: {
        valid: (input) => /^\d{5}-?\d{3}$/.test(input),
        semiValid: (input) => {
            const format = "NNNNN-NNN"
            return validCustomFormat(format, input)
        },
        message: "O formato deve ser 00000-000"
    },
    letter_only: {
        valid: (input) => /^[A-Za-zÀ-ÿ\s\.]+$/.test(input),
        semiValid: (input) => /^[A-Za-zÀ-ÿ\s\.]*$/.test(input),
        message: "Devem ser apenas letras e espacos"
    },
    letter_and_number_only: {
        valid: (input) => /^[A-Za-zÀ-ÿ\s]+$/.test(input),
        semiValid: (input) => /^[A-Za-zÀ-ÿ\s\.]*$/.test(input),
        message: "Devem ser apenas letras e espacos"
    },

    phone: {
        valid: (input) => /^\(\d{2}\)\s\d{5}-\d{4}$/.test(input),
        semiValid: (input) => {
            const format = "(NN) NNNNN-NNNN"
            return validCustomFormat(format, input)
        },
        format: formatarCelular,
        message: "O formato deve ser: (00) 00000-0000"
    }
};

export default patterns;