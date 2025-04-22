import CookieManager from "./manager/CookieManager";

const cookieUserLogued = import.meta.env.VITE_USER_LOGUED;
const CookieUserLogued = new CookieManager(cookieUserLogued);
export default CookieUserLogued