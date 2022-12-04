import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"

export const useProductsStore = defineStore('products', () => {

    const axios = inject('axios')
    const products = ref([])
    

    const totalProducts = computed(() => {
        return products.value.length
    })

    /*const myInProgressProducts = computed(() => {
        return products.value.filter(product => product.status == 'P' && product.costumer_id == userStore.userId)
    })

    const totalMyInProgressProducts = computed(() => {
        return myInProgressProducts.value.length
    })*/

    function getProductsByFilter(type) {
        return products.value.filter(product => (!type || type == product.type))
    }

    function getProductsByFilterTotal(type) {
        return getProductsByFilter(type).length
    }

    function clearProducts() {
        products.value = []
    }

    async function loadProducts() {
        try {
            const response = await axios.get('products')
            products.value = response.data.data

            return products.value
            
        } catch (error) {
            clearProducts()
            throw error
        }
    }

    async function insertProduct(newProduct) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the insertProduct
        const response = await axios.post('products', newProduct)
        products.value.push(response.data.data)

        return response.data.data
    }

    async function updateProduct(updateProduct) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the updateProduct
        const response = await axios.put('products/' + updateProduct.id, updateProduct)

        let idx = products.value.findIndex((t) => t.id === response.data.data.id)

        if (idx >= 0) {
            products.value[idx] = response.data.data
        }

        return response.data.data
    }

    async function deleteProduct(deleteProduct) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteProduct
        const response = await axios.delete('products/' + deleteProduct.id)

        let idx = products.value.findIndex((t) => t.id === response.data.data.id)

        if (idx >= 0) {
            products.value.splice(idx, 1)
        }

        return response.data.data
    }

    return { products, totalProducts, /*myInProgressProducts, totalMyInProgressProducts,*/getProductsByFilter, getProductsByFilterTotal, loadProducts, clearProducts, insertProduct, updateProduct, deleteProduct }
})