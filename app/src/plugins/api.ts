import CookieAccessToken from "@/cookies/CookieAccessToken"
import CookieRefreshToken from "@/cookies/CookieRefreshToken"
import CookieUserLogued from "@/cookies/CookieUsuarioLogued"
import axios from "axios"


const APP_API_URL = import.meta.env.VITE_API_URL

const api = axios.create({
    baseURL: APP_API_URL,
    headers: {
        'Content-Type': 'application/json',
    },
})

api.interceptors.request.use(
    (config) => {
        const token = CookieAccessToken.get()
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`
        }
        return config
    },
    (error) => Promise.reject(error)
)

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            CookieAccessToken.remove()
            CookieRefreshToken.remove()
            CookieUserLogued.remove()
            window.location.replace("/login")
        }
        return Promise.reject(error)
    }
)

export default api
