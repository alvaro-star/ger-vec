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

export function isDate(ng: string) {
    return /^\d{4}-\d{2}-\d{2}$/.test(ng);
}


export function calculateIdadeByDate(nascimento: Date) {
    const now = new Date()
    now.setHours(0, 0, 0, 0)
    let diffAnos = now.getFullYear() - nascimento.getFullYear()

    if (nascimento.getMonth() + 1 > now.getMonth() + 1)
        diffAnos--;
    else if (now.getMonth() + 1 == now.getMonth() + 1 && nascimento.getDate() > now.getDate())
        diffAnos--;
    return diffAnos
}

export function calculateIdade(data: string) {
    const [ano, mes, dia] = data.split("-").map(Number);
    const now = new Date()
    now.setHours(0, 0, 0, 0)
    let diffAnos = now.getFullYear() - ano

    if (mes > now.getMonth() + 1)
        diffAnos--;
    else if (now.getMonth() + 1 == mes && dia > now.getDate())
        diffAnos--;
    return diffAnos
}

export function isDateInFuture(inputDate: Date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    inputDate.setHours(0, 0, 0, 0);

    return inputDate > today;
}
