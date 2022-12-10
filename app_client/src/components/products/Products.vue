<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useProductsStore } from "../../stores/products.js"
import { useUserStore } from "../../stores/user.js"

import ProductTable from "./ProductTable.vue"

const router = useRouter()
const toast = inject("toast")

const userStore = useUserStore()
const productsStore = useProductsStore()
const productToDelete = ref(null)
const filterByType = ref('')
const deleteConfirmationDialog = ref(null)

const loadProducts = () => {
  productsStore.loadProducts()
    .catch((error) => {
      console.log(error)
    })
}

const addProduct = () => {
  router.push({ name: 'NewProduct' })
}

const addProductToOrder = (product) => {
  let order = sessionStorage.getItem('order') ?? {}
  let qtdTotal = sessionStorage.getItem('qtdTotal') ?? 0
  
  let qtd = (order[product.id]['qtd'] ?? 0) + 1
  ++qtdTotal
  sessionStorage.setItem('qtdTotal', JSON.stringify(qtdTotal))
  
  order[product.id] = {
    'id' : product.id,
    'qtd' : qtd,
    'name' : product.name,
    'preco' : product.preco,
    'subtotal' : product.preco * qtd,
  }

  sessionStorage.setItem('order', JSON.stringify(order))
}

const editProduct = (product) => {
  router.push({ name: 'Product', params: { id: product.id } })
}

const deleteProductConfirmed = () => {
  productsStore.deleteProduct(productToDelete.value)
    .then((deletedProduct) => {
      toast.info("Product " + productToDeleteDescription.value + " was deleted")
    })

    .catch(() => {
      toast.error("It was not possible to delete Product " + productToDeleteDescription.value + "!")
    })
}

const clickToDeleteProduct = (product) => {
  productToDelete.value = product
  deleteConfirmationDialog.value.show()
}

const filteredProducts = computed(() => {
  return productsStore.getProductsByFilter(filterByType.value)
})

const totalProducts = computed(() => {
  return productsStore.getProductsByFilterTotal(filterByType.value)
})

const productToDeleteDescription = computed(() => {
  return productToDelete.value ? `#${productToDelete.value.id} (${productToDelete.value.name})` : ""
})

onMounted(() => {
  // Calling loadProducts refresh the list of products from the API
  loadProducts()
})

</script>

<template>
  <confirmation-dialog ref="deleteConfirmationDialog" confirmationBtn="Delete product"
    :msg="`Do you really want to delete the product ${productToDeleteDescription}?`"
    @confirmed="deleteProductConfirmed">
  </confirmation-dialog>

  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Menu</h3>
    </div>
    <div class="mx-2 total-filter">
      <h5 class="mt-4">Total: {{ totalProducts }}</h5>
    </div>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-start flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectType" class="form-label">Filter by Type:</label>
      <select class="form-select" id="selectType" v-model="filterByType">
        <option value="">Any</option>
        <option value="hot dish">Hot Dishes</option>
        <option value="cold dish">Cold Dishes</option>
        <option value="drink">Drinks</option>
        <option value="dessert">Desserts</option>
      </select>
    </div>
    <div class="mx-2 mt-2">
      <button v-if="userStore.user?.type == 'EM'" type="button" class="btn btn-success px-4 btn-addproduct"
        @click="addProduct"><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Product</button>
    </div>
  </div>
  <product-table :products="filteredProducts" :showId="false" @add="addProductToOrder" @edit="editProduct"
    @delete="clickToDeleteProduct"></product-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.btn-addproduct {
  margin-top: 1.85rem;
}

.total-filter {
  margin-top: 0.35rem;
}
</style>