import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'

export const useUsersStore = defineStore('users', () => {

    const axios = inject('axios')
    const users = ref([])

    const totalUsers = computed(() => {
        return users.value.length
    })

    function getUsersByFilter(type) {
        return users.value.filter(user => (!type || type == user.type))
    }

    function getUsersByFilterTotal(type) {
        return getUsersByFilter(type).length
    }

    function clearUsers() {
        users.value = []
    }

    async function loadUsers() {
        try {
            const response = await axios.get('users')
            users.value = response.data.data

            return users.value

        } catch (error) {
            clearUsers()
            throw error
        }
    }

    async function insertUser(newUser) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the insertUser
        const response = await axios.post('users', newUser)
        users.value.push(response.data.data)
        return response.data.data
    }

    async function updateUser(updateUser) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the updateUser
        const response = await axios.put('users/' + updateUser.id, updateUser)

        let idx = users.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            users.value[idx] = response.data.data
        }

        return response.data.data
    }

    async function deleteUser(deleteUser) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteUser
        const response = await axios.delete('users/' + deleteUser.id)
        let idx = users.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            users.value.splice(idx, 1)
        }
        return response.data.data
    }

    async function blockedUser(user) {
        if (user.blocked == 0)
            user.blocked = 1
        
        else
            user.blocked = 0

        const response = await axios.patch("users/" + user.id + "/blocked", { blocked: user.blocked })
        
        return response.data.data
    }

    return { users, totalUsers, getUsersByFilter, getUsersByFilterTotal, loadUsers, clearUsers, insertUser, updateUser, deleteUser, blockedUser }
})
