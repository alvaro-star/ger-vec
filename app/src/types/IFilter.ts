export interface IFilter {
    options: string[], type: 'radio' | 'checkbox', value: string[]
}
export type IFilters = Record<string, IFilter>
