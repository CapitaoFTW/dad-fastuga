import { ref, inject } from 'vue'
import { defineStore } from 'pinia'

export const useOrderItemsStore = defineStore('order_items', () => {

    const axios = inject('axios')
    const toast = inject('toast')

    const order_items = ref(JSON.parse(sessionStorage.getItem('order_items')) ?? [])
    const totalProducts = ref(sessionStorage.getItem('totalProducts') ?? 0)

    function composeOrder(product) {
        let orderItems = order_items.value
        let isSameProduct = false
        let quantity = 1

        for (let i = 0; i < orderItems.length; i++) {
            if (orderItems[i].product_id == product.id) {
                orderItems[i].product_quantity++
                orderItems[i].product_subtotal = (product.price * orderItems[i].product_quantity).toFixed(2)
                isSameProduct = true
            }
        }

        totalProducts.value++

        if (!isSameProduct) {
            let order_item = {
                'product_id': product.id,
                'product_quantity': quantity,
                'product_name': product.name,
                'product_price': product.price,
                'product_type': product.type,
                'product_subtotal': (product.price * quantity).toFixed(2),
            }

            orderItems.push(order_item)
        }

        order_items.value = orderItems

        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order_items', JSON.stringify(order_items.value))
    }

    function updateOrderItems(order_item, value) {
        let orderItems = order_items.value

        for (let i = 0; i < orderItems.length; i++) {
            if (orderItems[i].product_id == order_item.product_id) {

                if (orderItems[i].product_quantity > 0) {
                    orderItems[i].product_quantity += value
                    orderItems[i].product_subtotal = (order_item.product_price * orderItems[i].product_quantity).toFixed(2)
                }

                if (value == 1) {
                    toast.success("Added 1 item from the product '" + orderItems[i].product_name + "'")
                    totalProducts.value++;

                } else {

                    if (orderItems[i].product_quantity <= 0 || value == 0) {
                        toast.info("Product '" + orderItems[i].product_name + "' was removed from the order_items")

                        if (value == 0) {
                            totalProducts.value -= orderItems[i].product_quantity
                            totalProducts.value++ // using this to spare an else
                        }

                        orderItems.splice(i, 1)

                    } else {

                        toast.warning("Removed 1 item from the product '" + orderItems[i].product_name + "'")
                    }

                    totalProducts.value--;
                }
            }
        }

        order_items.value = orderItems

        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order_items', JSON.stringify(order_items.value))
    }

    function clearOrderItems() {
        sessionStorage.removeItem('order_items')
        sessionStorage.removeItem('totalProducts')
        order_items.value = []
        totalProducts.value = 0
    }

    async function prepareOrderItem(order_item) {
        const response = await axios.patch("order_items/" + order_item.id + "/preparing")
        return response.data.data
    }

    async function readyOrderItem(order_item) {
        const response = await axios.patch("order_items/" + order_item.id + "/ready")
        return response.data.data
    }

    return { order_items, totalProducts, composeOrder, updateOrderItems, clearOrderItems, prepareOrderItem, readyOrderItem }
})
