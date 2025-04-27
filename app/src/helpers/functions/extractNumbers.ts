const extractNumbers = (texto: string) => {
    return texto.replace(/\D/g, '');
}

export default extractNumbers;