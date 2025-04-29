const selectTop5 = (items: any[], label: string, value: string) => {
    if (items.length <= 5) return items
    const principais = items.slice(0, 5)
    const outros = items.slice(5)
    const totalOutros = outros.reduce((soma, item) => soma + item[value], 0)
    const response = [...principais];
    const groupOutros = {} as any
    groupOutros[label] = 'Outros'
    groupOutros[value] = totalOutros
    response.push(groupOutros)
    return response
}

export default selectTop5