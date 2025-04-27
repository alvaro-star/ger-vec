import { formatNumber } from "chart.js/helpers"
import extractNumbers from "../functions/extractNumbers"

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
    | 'phone'

export type InputType = KeysValidator | 'date'

interface IValidator {
    semiValid: (input: string) => boolean,
    valid: (input: string) => boolean,
    message: string,
    format?: (input: string) => string
}

/*
 N > nmero
 l -> letra
 A -> leta maiuscula
 a -> letra minuscula
 */
const validFormat = (format: string, input: string) => {
    if (format.length < input.length) return false
    for (let i = 0; i < input.length; i++) {
        switch (format[i]) {
            case "A":
                if (!/[A-Z]/.test(input[i])) return false
                break
            case "N":
                if (!/\d/.test(input[i])) return false
                break
            case "l": if (!/[a-z]/.test(input[i])) return false
                break
            case "a": if (!/[a-z]/.test(input[i])) return false
            default:
                if (input[i] !== format[i]) return false
        }
    }
    return true
}


const patterns: Record<KeysValidator, IValidator> = {
    integer: {
        valid: (input) => /^\d+$/.test(input),
        semiValid: (input) => /^\d*$/.test(input),
        message: "Por favor, insira um número válido"
    },
    float: {
        valid: (input) => /^\d+(\.\d+)?$/.test(input),
        semiValid: (input) => /^(\d+\.)?\d*?$/.test(input),
        message: "Por favor, insira um numero float válido"
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
            return validFormat(format, input)
        },
        message: "O formato do valor deve ser AAA0A00"
    },
    cpf: {
        valid: (input) => /^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(input),
        semiValid: (input) => {
            const format = "NNN.NNN.NNN-NN"
            return validFormat(format, input)
        },
        message: "O formato deve ser 000.000.000-00"
    },
    cep: {
        valid: (input) => /^\d{5}-?\d{3}$/.test(input),
        semiValid: (input) => {
            const format = "NNNNN-NNN"
            return validFormat(format, input)
        },
        message: "O formato deve ser 00000-000"
    },
    letter_only: {
        valid: (input) => /^[A-Za-zÀ-ÿ\s]+$/.test(input),
        semiValid: (input) => /^[A-Za-zÀ-ÿ\s]*$/.test(input),
        message: "Devem ser apenas letras e espacos"
    },
    phone: {
        valid: (input) => /^\(\d{2}\)\s\d{5}-\d{4}$/.test(input),
        semiValid: (input) => {
            const format = "(NN) NNNNN-NNNN"
            return validFormat(format, input)
        },
        message: "O formato deve ser: (00) 00000-0000"
    }
};

patterns.cpf.format = (input: string) => {
    const numbers = extractNumbers(input)
    return formatarCPF(numbers)
}

patterns.phone.format = (input: string) => {
    const numbers = extractNumbers(input)
    return formatarCelular(numbers)
}

export const formatarFistLetter = (input: string) => {
    return input
        .replace(/\s+/g, ' ')
        .split(' ')
        .map(word =>
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        )
        .join(' ');
}

export function validInterval(value: string, min: number, max: number) {
    const numero = parseInt(value, 10);
    if (isNaN(numero)) return false;
    return numero >= min && numero <= max;
}

export function isInteger(ng: string) {
    return /^\d+$/.test(ng);
}

export function isFloat(ng: string) {
    return /^\d+(\.\d+)?$/.test(ng);
}

export function formatarCPF(numeros: string) {
    const apenasNumeros = numeros.replace(/\D/g, '').slice(0, 11);
    if (apenasNumeros.length !== 11) return '';
    return apenasNumeros.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

export function formatarCelular(numeros: string) {
    const apenasNumeros = numeros.replace(/\D/g, '').slice(0, 11);
    if (apenasNumeros.length !== 11) return '';
    return apenasNumeros.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
}




export default patterns;