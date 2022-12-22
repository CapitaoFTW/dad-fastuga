import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useOrdersStore } from './orders'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useUserStore = defineStore('user', () => {

    const axios = inject('axios')
    const toast = inject("toast")
    const socket = inject("socket")
    const serverBaseUrl = inject('serverBaseUrl')

    const ordersStore = useOrdersStore()
    const user = ref(null)

    const userPhotoUrl = computed(() => {
        if (!user.value?.photo_url) {
            return avatarNoneUrl
        }

        return serverBaseUrl + '/storage/fotos/' + user.value.photo_url
    })

    const userId = computed(() => {
        return user.value?.id ?? -1
    })

    const customerId = computed(() => {
        return user.value?.customer ? user.value.customer.id : -1
    })

    async function loadUser() {
        try {
            const response = await axios.get('users/me')
            user.value = response.data.data

        } catch (error) {
            clearUser()
            ordersStore.clearOrders()
            throw error
        }
    }

    function clearUser() {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        user.value = null
    }

    async function register(credentials) {
        try {
            const response = await axios.post('register', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            return false
        }
        catch (error) {
            clearUser()
            return error
        }
    }

    async function login(credentials) {
        try {
            const response = await axios.post('login', credentials)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            socket.emit('loggedIn', user.value)
            return false

        } catch (error) {
            clearUser()
            ordersStore.clearOrders()
            return error
        }
    }

    async function logout() {
        try {
            await axios.post('logout')
            socket.emit('loggedOut', user.value)
            clearUser()
            ordersStore.clearOrders()
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
            socket.emit('loggedIn', user.value)
            await ordersStore.loadOrders()
            return true
        }

        clearUser()
        ordersStore.clearOrders()
        return false
    }

    socket.on('newUser', (data) => {
        toast.info(`New User '${data.user.name}' has been created by ${data.manager}!`)
    })

    socket.on('updateUser', (data) => {
        if (user.value?.id == data.user.id) {
            user.value = data.user
            toast.info('Your profile has been changed!')

        } else {
            toast.info(`${data.user.name}'s profile has been changed by ${data.manager}!`)
        }
    })

    socket.on('deleteUser', (data) => {
        if (user.value?.id == data.user.id) {
            user.value = data.user
            toast.error('Your account has been deleted!')
            logout()

        } else {
            toast.warning(`${data.user.name}'s account has been deleted by ${data.manager}!`)
        }
    })

    socket.on('blockUser', (data) => {
        if (user.value?.id == data.user.id) {
            user.value = data.user
            toast.error('You have been blocked!')
            logout()

        } else {
            if (data.user.blocked == 0) {
                toast.warning(`${data.user.name}'s account has been unblocked by ${data.manager}!`)

            } else {
                toast.warning(`${data.user.name}'s account has been blocked by ${data.manager}!`)
            }
        }
    })

    /*socket.on('newHotDishes', (number) => {
        //if (data.numberHotDishes == 1)
            toast.info(`One Hot Dish was ordered (Ticket #${number.id})`)

        else
            toast.info(`${data.numberHotDishes} new Hot Dishes were ordered (Ticket #${data.ticket})`)
    })*/

    return { user, userId, customerId, userPhotoUrl, register, login, changePassword, logout, restoreToken }
})
