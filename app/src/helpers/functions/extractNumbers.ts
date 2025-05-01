const extractNumbers = (str: string) => {
    if (str) return str.replace(/\D/g, '')
    return ''
}
export default extractNumbers;