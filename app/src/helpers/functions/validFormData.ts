export const validEmptyFieldsForm = (form: any, requiredFields: string[]) => {
    let formErrors: Record<string, string> = {};

    for (const field of requiredFields) {
        const value = ((form as any)[field]) || '';
        if (value === '') formErrors[field] = 'Campo obrigatório';
    }
    return formErrors;
}