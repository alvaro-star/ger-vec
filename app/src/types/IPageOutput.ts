export default interface IPageOutput<T> {
    nPage: number;
    size: number;
    nElementos: number;
    nPages: number;
    hasNextPage: boolean;
    content: T[];
}
