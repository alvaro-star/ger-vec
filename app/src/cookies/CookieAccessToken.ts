import CookieManager from "./manager/CookieManager";

const accesssToken = import.meta.env.VITE_ACCESS_TOKEN;
console.log(accesssToken);

const CookieAccessToken = new CookieManager<string>(accesssToken);
export default CookieAccessToken;