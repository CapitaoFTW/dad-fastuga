import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'

export const useOrderStore = defineStore('order', () => {

    const axios = inject('axios')
    const toast = inject('toast')

    const order = ref(null)
    const totalProducts = ref(null)

    function composeOrder(product) {
        order.value = JSON.parse(sessionStorage.getItem('order')) ?? {}
        totalProducts.value = sessionStorage.getItem('totalProducts') ?? 0

        let qtd = (order.value[product.id] == null ? 0 : order.value[product.id]['qtd']) + 1
        totalProducts.value++
        sessionStorage.setItem('totalProducts', totalProducts.value)

        order.value[product.id] = {
            'id': product.id,
            'qtd': qtd,
            'name': product.name,
            'price': product.price,
            'subtotal': (product.price * qtd).toFixed(2),
        }

        sessionStorage.setItem('order', JSON.stringify(order.value))
    }

    function updateComposingOrder(product, value) {
        order.value = JSON.parse(sessionStorage.getItem('order')) ?? {}
        totalProducts.value = sessionStorage.getItem('totalProducts') ?? 0
        let qtd = order.value[product.id]['qtd'] + value

        if (value == 1) {
            toast.success("Added 1 item from the product '" + order.value[product.id]['name'] + "'")
            totalProducts.value++;

        } else {

            if (qtd <= 0) {
                toast.info("Product '" + order.value[product.id]['name'] + "' was removed from the order")
                delete order.value[product.id]

            } else {
                toast.warning("Removed 1 item from the product '" + order.value[product.id]['name'] + "'")
                totalProducts.value--;
            }
        }

        if (qtd > 0) {
            order.value[product.id] = {
                'id': product.id,
                'qtd': qtd,
                'name': product.name,
                'price': product.price,
                'subtotal': (product.price * qtd).toFixed(2),
            }
        }

        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order', JSON.stringify(order.value))
    }

    function deleteProductFromComposingOrder(product) {
        order.value = JSON.parse(sessionStorage.getItem('order')) ?? {}
        totalProducts.value = sessionStorage.getItem('totalProducts') ?? 0
        totalProducts.value -= order.value[product.id]['qtd']
        toast.info("Product " + order.value[product.id]['name'] + " was removed from the order")
        delete order.value[product.id]
        sessionStorage.setItem('totalProducts', totalProducts.value)
        sessionStorage.setItem('order', JSON.stringify(order.value))
    }

    function clearOrder() {
        sessionStorage.removeItem('order')
        sessionStorage.removeItem('totalProducts')
        order.value = null
        totalProducts.value = null
    }

    return { order, totalProducts, clearOrder, composeOrder, updateComposingOrder, deleteProductFromComposingOrder }
})
