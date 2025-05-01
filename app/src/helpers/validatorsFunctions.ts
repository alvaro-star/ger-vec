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

export function isDateInFuture(dateString: string) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const inputDate = new Date(dateString);
    inputDate.setHours(0, 0, 0, 0);

    return inputDate > today;
}
