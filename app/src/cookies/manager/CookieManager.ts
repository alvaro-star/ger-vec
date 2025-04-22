class CookieManager<T> {
    private cookieName: string;
    constructor(cookieName: string) {
        this.cookieName = cookieName;
    }

    set(value: T) {
        sessionStorage.setItem(this.cookieName, JSON.stringify(value));
    }

    remove() {
        sessionStorage.removeItem(this.cookieName);
    }
    get() {
        const value = sessionStorage.getItem(this.cookieName) || "\"\"";
        return JSON.parse(value) as T;
    }
}


export default CookieManager;