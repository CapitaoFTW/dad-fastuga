import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'

export const useOrderStore = defineStore('order', () => {

    const axios = inject('axios')
    const toast = inject('toast')

    const composingOrder = ref(JSON.parse(sessionStorage.getItem('order')) ?? [])
    const totalProducts = ref(sessionStorage.getItem('totalProducts') ?? 0)

    function composeOrder(product) {
        let order = composingOrder.value

        let isSameProduct = false
        let quantity = 1

        for (let i = 0; i < order.length; i++) {
            if (order[i].product_id == product.id) {
                order[i].quantity++
                order[i].subtotal = (product.price * order[i].quantity).toFixed(2)
                isSameProduct = true
            }
        }

        totalProducts.value++

        if (!isSameProduct) {
            let row = {
                'product_id': product.id,
                'quantity': quantity,
                'name': product.name,
                'price': product.price,
                'type': product.type,
                'subtotal': (product.price * quantity).toFixed(2),
            }

            order.push(row)
        }

        composingOrder.value = order

        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order', JSON.stringify(composingOrder.value))
    }

    function updateComposingOrder(row, value) {
        let order = composingOrder.value

        for (let i = 0; i < order.length; i++) {
            if (order[i].product_id == row.product_id) {

                if (order[i].quantity > 0) {
                    order[i].quantity += value
                    order[i].subtotal = (row.price * order[i].quantity).toFixed(2)
                }

                if (value == 1) {
                    toast.success("Added 1 item from the product '" + order[i].name + "'")
                    totalProducts.value++;

                } else {

                    if (order[i].quantity <= 0 || value == 0) {
                        toast.info("Product '" + order[i].name + "' was removed from the order")

                        if (value == 0) {
                            totalProducts.value -= order[i].quantity
                            totalProducts.value++ // using this to spare an else
                        }

                        order.splice(i, 1)

                    } else {

                        toast.warning("Removed 1 item from the product '" + order[i].name + "'")
                    }

                    totalProducts.value--;
                }
            }
        }

        composingOrder.value = order

        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order', JSON.stringify(composingOrder.value))
    }

    function clearOrder() {
        sessionStorage.removeItem('order')
        sessionStorage.removeItem('totalProducts')
        composingOrder.value = []
        totalProducts.value = 0
    }

    async function payOrder(payment) {
        const response = await axios.post('https://dad-202223-payments-api.vercel.app/api/payments ', payment)
        return response.data.data
    }

    return { composingOrder, totalProducts, clearOrder, composeOrder, updateComposingOrder, payOrder }
})
