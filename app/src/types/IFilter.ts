export interface IFilter {
    options: string[], type: 'radio' | 'checkbox', value: string[]
}

export interface ISort {
    options: string[], value: string
}

export type IFilters = Record<string, IFilter>