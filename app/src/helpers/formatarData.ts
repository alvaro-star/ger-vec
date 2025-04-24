const formatarData = (data: string): string => {
    const [ano, mes, dia] = data.split('T')[0].split('-').map(Number);
    return `${String(dia).padStart(2, '0')}/${String(mes).padStart(2, '0')}/${ano}`;
};


export const formatarClassicData = (dataStr: string): string => {
    const [ano, mes, dia] = dataStr.split('-').map(Number)

    const data = new Date(ano, mes - 1, dia)

    if (isNaN(data.getTime())) return ''

    const diaStr = String(data.getDate()).padStart(2, '0')
    const mesStr = String(data.getMonth() + 1).padStart(2, '0')
    return `${diaStr}/${mesStr}/${data.getFullYear()}`
}


export const formatoDataValido = (data: string): boolean => {
    const regex = /^([0-2]\d|3[01])\/(0\d|1[0-2])\/\d{4}$/
    return regex.test(data)
}

export const converterDataParaISO = (data: string): string => {
    const [dia, mes, ano] = data.split('/')
    return `${ano}-${mes}-${dia}`
}

export default formatarData;