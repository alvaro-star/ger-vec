import extractNumbers from "./functions/extractNumbers"

/*
 N > nmero
 l -> letra
 A -> leta maiuscula
 a -> letra minuscula
 */
export const validCustomFormat = (format: string, input: string) => {
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


export const formatarInteger = (number: string) => {
    const [integer, decimal] = number.split(",")
    number = Number(extractNumbers(integer)).toString()
    if (number.length == 0) return ''

    let nExcecos = number.length % 3;

    if (nExcecos == number.length) return number
    let significativePart = number.slice(0, nExcecos)

    let unsignificativePart = number.slice(nExcecos, number.length + 1)
    let numberParts = unsignificativePart.match(/.{1,3}/g)

    if (!numberParts) return significativePart
    if (significativePart != '') numberParts.unshift(significativePart)
    return numberParts.join('.')
}

export const formatarFloat = (number: string, precicion: number) => {
    precicion = precicion || 2

    let [integer, decimal] = number.split(',')

    if (!decimal && (!integer || integer == '')) return ''
    integer = formatarInteger(integer)
    decimal = extractNumbers(decimal)
    decimal = String(decimal).padEnd(precicion, '0')
    if (decimal.length > precicion)
        decimal = decimal.slice(0, precicion)
    if (integer.length == 0)
        integer = '0'
    return integer + ',' + decimal
}


export const formatarFistLetter = (input: string) => {
    return input
        .replace(/\s+/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
}

export const formatarCPF = (numeros: string) => {
    const apenasNumeros = extractNumbers(numeros).slice(0, 11);
    const length = apenasNumeros.length;
    if (length >= 10)
        return apenasNumeros.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    else if (length >= 7)
        return apenasNumeros.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    else if (length >= 4)
        return apenasNumeros.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    return apenasNumeros;
}

export const formatarCelular = (numeros: string) => {
    const apenasNumeros = extractNumbers(numeros).slice(0, 11);
    const length = apenasNumeros.length;

    if (length === 11)
        return apenasNumeros.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    else if (length >= 7)
        return apenasNumeros.replace(/(\d{2})(\d{5})(\d{1,2})/, '($1) $2-$3');
    else if (length >= 3)
        return apenasNumeros.replace(/(\d{2})(\d{1,5})/, '($1) $2');

    return apenasNumeros;
}

export const formatarLocalDateTime = (localDateTime: string): string => {
    let [data, hora] = localDateTime.split('T');
    let horaBreaked = hora.split(":")
    return formatarLocalDate(data) + " - " + (horaBreaked[0] + "h:" + horaBreaked[1]) + "m";
};

// A funcao foi desenvolvida deivido a influencia do fuso horario da maquina e do servidor
export const createDateByString = (localDate: string) => {
    const [ano, mes, dia] = localDate.split('-').map(Number).map(Number)

    return new Date(ano, mes - 1, dia)
}

export const formatarLocalDate = (localDate: string): string => {
    const [ano, mes, dia] = localDate.split('-').map(Number)
    const data = new Date(ano, mes - 1, dia)

    if (isNaN(data.getTime())) return ''
    const diaStr = String(data.getDate()).padStart(2, '0')
    const mesStr = String(data.getMonth() + 1).padStart(2, '0')
    return `${diaStr}/${mesStr}/${data.getFullYear()}`
}

export const formatarDateToStringDate = (data: Date | undefined): string => {
    if (!data) return '';
    const year = data.getFullYear();
    const month = String(data.getMonth() + 1).padStart(2, '0');
    const day = String(data.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
