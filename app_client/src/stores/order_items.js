import { inject } from 'vue'
import { defineStore } from 'pinia'

export const useOrderItemsStore = defineStore('order_items', () => {

    const axios = inject('axios')

    async function prepareOrderItem(order_item) {
        const response = await axios.patch("order_items/" + order_item.id + "/preparing")
        return response.data.data
    }

    async function readyOrderItem(order_item) {
        const response = await axios.patch("order_items/" + order_item.id + "/ready")
        return response.data.data
    }

    return { prepareOrderItem, readyOrderItem }
})
