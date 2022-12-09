import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useOrderStore = defineStore('order', () => {

    const axios = inject('axios')
    const serverBaseUrl = inject('serverBaseUrl')

    const order = ref(null)

    const orderId = computed(() => {
        return order.value?.id ?? -1
    })

    async function loadOrder() {
        try {
            const response = await axios.get('orders/inprogress')
            order.value = response.data.data

        } catch (error) {
            clearUser()
            throw error
        }
    }

    function clearOrder() {
        sessionStorage.removeItem('order')
        order.value = null
    }

    async function register(credentials) {
        try {
            const response = await axios.post('register', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            //await loadInProgressOrders()
            return false
        }
        catch (error) {
            clearUser()
            //clearInProgressOrders()
            return error
        }
    }

    async function login(credentials) {
        try {
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            //await ordersStore.loadOrders()
            return false

        } catch (error) {
            clearUser()
            //ordersStore.clearOrders()
            return error
        }
    }

    async function logout() {
        try {
            await axios.post('logout')
            clearUser()
            //ordersStore.clearOrders()
            return true

        } catch (error) {
            return false
        }
    }

    async function changePassword(passwords) {
        try {
            await axios.patch('users/' + userId.value + '/password', passwords)
            return false
        }
        catch (error) {
            return error
        }
    }

    async function restoreToken() {
        let storedToken = sessionStorage.getItem('token')

        if (storedToken) {
            axios.defaults.headers.common.Authorization = "Bearer " + storedToken
            await loadUser()
            //await ordersStore.loadOrders()
            return true
        }

        clearUser()
        //ordersStore.clearOrders()

        return false
    }

    return { user, userId, userPhotoUrl, register, login, changePassword, logout, restoreToken }
})
