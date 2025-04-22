const formatarData = (data: string): string => {
    const date = new Date(data);
    return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

export const formatarClassicData = (dataStr: string): string => {
    const data = new Date(dataStr)
    const dia = String(data.getDate()).padStart(2, '0')
    const mes = String(data.getMonth() + 1).padStart(2, '0') // meses começam em 0
    const ano = data.getFullYear()
    return `${dia}/${mes}/${ano}`
}

export default formatarData;