import CookieManager from "./manager/CookieManager";

const refreshToken = import.meta.env.VITE_REFRESH_TOKEN;
const CookieRefreshToken = new CookieManager<string>(refreshToken);
export default CookieRefreshToken;