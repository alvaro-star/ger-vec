import { formatarCelular, formatarCPF, formatarFloat, formatarInteger, validCustomFormat } from "../formatters"
import extractNumbers from "../functions/extractNumbers";

// Tipo para campos de entrada comuns
type KeysValidator =
    | 'text'
    | 'integer'
    | 'float'
    | 'renavam'
    | 'email'
    | 'placa'
    | 'cpf'
    | 'cep'
    | 'letter_only'
    | "letter_and_number_only"
    | 'phone'
    | 'date'

export type InputType = KeysValidator

interface IValidator {
    semiValid: (input: string) => boolean,
    valid: (input: string) => boolean,
    message: string,
    format?: (input: string, ...args: any) => string
}

const patterns: Record<KeysValidator, IValidator> = {
    date: {
        valid: (input) => /^\d{1,3}\\(\d{3})*$/.test(input),
        semiValid: (input) => {
            let format = "NN/NN/NNNN"
            return validCustomFormat(format, input)
        },
        message: 'Digite uma data valida'
    },
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
        valid: (input) => {
            if (!/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(input))
                return false;

            const numbersCPF = extractNumbers(input)

            if (numbersCPF.length != 11) return false

            let firstSum = 0;
            let seconSum = 0;
            for (let i = 0, j = 10; i < 9; i++, j--)
                firstSum += parseInt(numbersCPF[i]) * j;
            let firstDigit = firstSum % 11
            firstDigit = (firstDigit >= 2) ? 11 - firstDigit : 0

            for (let i = 0, j = 11; i < 10; i++, j--)
                seconSum += parseInt(numbersCPF[i]) * j;
            let seconDigit = seconSum % 11
            seconDigit = (seconDigit >= 2) ? 11 - seconDigit : 0

            return (firstDigit.toString() == numbersCPF.charAt(9) && seconDigit.toString() == numbersCPF.charAt(10))
        },
        semiValid: (input) => {
            const format = "NNN.NNN.NNN-NN"
            return validCustomFormat(format, input)
        },
        format: formatarCPF,
        message: "O CPF informado não é válido"
    },
    renavam: {
        valid: (input) => {
            const numbers = extractNumbers(input)
            let soma = 0
            if (numbers.length != 11) return false;

            const pesos = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
            for (let i = 0; i < 10; i++)
                soma += pesos[i] * parseInt(numbers.charAt(i));
            let digit = soma % 11
            digit = ((digit >= 2) ? 11 - digit : 0)
            return parseInt(numbers.charAt(10)) === digit
        },
        semiValid: (input) => {
            const numbers = extractNumbers(input)
            return numbers.length <= 11 && numbers.length != input.length
        },
        message: "A renavam informado não é válida"
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