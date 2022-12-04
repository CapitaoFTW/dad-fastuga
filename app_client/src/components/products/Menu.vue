<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useProductsStore } from "../../stores/products.js"
import { useUserStore } from "../../stores/user.js"

import MenuTable from "./MenuTable.vue"

const router = useRouter()
const axios = inject("axios")
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
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ totalProducts }}</h5>
    </div>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-start flex-wrap">
    <div class="mx-2">
      <button v-if="userStore.user?.type == 'EM'" type="button" class="btn btn-success px-4 btn-addproduct" @click="addProduct"><i
          class="bi bi-xs bi-file-earmark-plus"></i>&nbsp; Add Product</button>
    </div>
  </div>
  <menu-table :products="filteredProducts" :showId="false" @edit="editProduct"
    @delete="clickToDeleteProduct"></menu-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 0.35rem;
}

</style>