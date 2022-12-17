<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"
import { useOrderStore } from '../../stores/order'

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')
const orderStore = useOrderStore()

const order = ref(JSON.parse(sessionStorage.getItem('order')))
const totalProducts = ref(sessionStorage.getItem('totalProducts'))
const errors = ref(null)

const more = (product) => {
  orderStore.updateComposingOrder(product, 1)
  order.value = orderStore.order
  totalProducts.value = orderStore.totalProducts
}

const less = (product) => {
  orderStore.updateComposingOrder(product, -1)
  order.value = orderStore.order
  totalProducts.value = orderStore.totalProducts
}

const destroy = (product) => {
  orderStore.deleteProductFromComposingOrder(product)
  order.value = orderStore.order
  totalProducts.value = orderStore.totalProducts
}
</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Order</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total Products: {{ totalProducts }}</h5>
    </div>
  </div>
  <hr>
  <table class="table">
    <thead>
      <tr>
        <th class="align-middle">Quantity</th>
        <th class="align-middle">Product</th>
        <th class="align-middle">Price</th>
        <th class="align-middle">Subtotal</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="product in order">
        <td class="align-middle">{{ product['qtd'] }}</td>
        <td class="align-middle">{{ product['name'] }}</td>
        <td class="align-middle">{{ product['price'] }} €</td>
        <td class="align-middle">{{ product['subtotal'] }} €</td>
        <td class="text-end align-middle">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary" @click="more(product)"><i
                class="bi bi-xs bi-cart-plus-fill"></i></button>
            <button class="btn btn-xs btn-secondary" @click="less(product)"><i
                class="bi bi-xs bi-cart-dash-fill"></i></button>
            <button class="btn btn-xs btn-danger" @click="destroy(product)"><i
                class="bi bi-xs bi-trash3-fill"></i></button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="mt-5 mb-5 d-flex justify-content-center">
    <button type="button" class="btn btn-primary px-5" @click="save">Pay Order</button>
    <button type="button" class="btn btn-light px-5" @click="cancel">Cancel Order</button>
  </div>
</template>
<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>
