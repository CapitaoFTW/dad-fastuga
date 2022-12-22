import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"

export const useOrdersStore = defineStore('orders', () => {

    const userStore = useUserStore()
    const axios = inject('axios')
    const socket = inject("socket")

    const orders = ref([])

    const totalOrders = computed(() => {
        return orders.value.length
    })

    const myInProgressOrders = computed(() => {
        return orders.value.filter(order => ((order.status == 'P' || order.status == 'R') && order.customer_userId == userStore.userId))
    })

    const totalMyInProgressOrders = computed(() => {
        return myInProgressOrders.value.length
    })

    function getOrdersByFilter(status) {
        return orders.value.filter(order => (!status || status == order.status))
    }

    function getOrdersByFilterTotal(status) {
        return getOrdersByFilter(status).length
    }

    function clearOrders() {
        orders.value = []
    }

    async function loadOrders() {
        try {
            const response = await axios.get('orders')
            orders.value = response.data.data

            return orders.value

        } catch (error) {
            clearOrders()
            throw error
        }
    }

    async function insertOrder(newOrder) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the insertOrder
        const response = await axios.post('orders', newOrder)
        orders.value.push(response.data.data)

        return response.data.data
    }

    async function updateOrder(updateOrder) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the updateOrder
        const response = await axios.put('orders/' + updateOrder.id, updateOrder)
        let idx = orders.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            orders.value[idx] = response.data.data
        }
        return response.data.data
    }

    async function readyOrder(order) {
        const response = await axios.patch("orders/" + order.id + "/ready")
        socket.emit('readyOrder', response.data.data)
        return response.data.data
    }

    async function deliverOrder(order) {
        const response = await axios.patch("orders/" + order.id + "/delivered")
        socket.emit('deliveredOrder', response.data.data)
        return response.data.data
    }

    async function cancelOrder(order) {

        const response = await axios.patch("orders/" + order.id + "/cancelled")
        socket.emit('cancelledOrder', response.data.data, userStore.user.name)
        return response.data.data
    }

    async function payOrder(payment) {
        const response = await axios.post('https://dad-202223-payments-api.vercel.app/api/payments ', payment)
        return response.data.data
    }

    async function refundOrder(payment) {
        const response = await axios.post('https://dad-202223-payments-api.vercel.app/api/refunds ', payment)
        return response.data.data
    }

    return { orders, totalOrders, myInProgressOrders, totalMyInProgressOrders, getOrdersByFilter, getOrdersByFilterTotal, loadOrders, clearOrders, insertOrder, updateOrder, readyOrder, deliverOrder, cancelOrder, payOrder, refundOrder }
})
