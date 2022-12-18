<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"
import { useOrderStore } from '../../stores/order'
import { useOrdersStore } from '../../stores/orders'

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')

const userStore = useUserStore()
const orderStore = useOrderStore()
const ordersStore = useOrdersStore()

const order = ref([])
const totalProducts = ref(0)

const totalPrice = computed(() => {
  let total = 0

  for (const [key] of Object.entries(order.value)) {
    total += Number(order.value[key].subtotal)
  }

  return total.toFixed(2)
})

const more = (row) => {
  orderStore.updateComposingOrder(row, 1)

  order.value = orderStore.composingOrder
  totalProducts.value = orderStore.totalProducts
}

const less = (row) => {
  orderStore.updateComposingOrder(row, -1)

  order.value = orderStore.composingOrder
  totalProducts.value = orderStore.totalProducts

  if (totalProducts.value <= 0) {
    router.push({ name: 'Products' })
  }
}

const destroy = (row) => {
  orderStore.updateComposingOrder(row, 0)

  order.value = orderStore.composingOrder
  totalProducts.value = orderStore.totalProducts

  if (totalProducts.value <= 0) {
    router.push({ name: 'Products' })
  }
}

const save = () => {
  errors.value = null
  let order_items = {}
  
  for(let i = 0; i < order.value.length; i++) {
    order_items[i] = {
      'name': order.value[i].name,
      'price': order.value[i].price,
      'product_id': order.value[i].product_id,
      'quantity': order.value[i].quantity,
      'type': order.value[i].type,
    }
  }

  ordersStore.insertOrder({
    'customer_id': userStore.user ? userStore.customerId : null,
    'total_price': totalPrice.value,
    'total_paid': totalPrice.value,
    'total_paid_with_points': 0,
    'points_gained': userStore.user && totalPrice.value >= 10 ? Math.floor(totalPrice.value / 10) : 0,
    'points_used_to_pay': 0,
    'payment_type': userStore.user ? userStore.user.customer.default_payment_type : 'MBWAY',
    'payment_reference': userStore.user ? userStore.user.customer.default_payment_reference : '910599057',
    'order_items': order.value,
  })

    .then((insertedOrder) => {
      toast.success('Order #' + insertedOrder.ticket_number + ' was created successfully.')
      orderStore.clearOrder()

      router.push({ name: 'Products' })
    })

    .catch((error) => {
      if (error.response.status == 422) {
        toast.error('Order was not created due to validation errors!')
        errors.value = error.response.data.errors
  
      } else {
      console.log(error)
      toast.error('Order was not created due to unknown server error!')
      }
    })
}

const errors = ref(null)

const cancel = () => {
  orderStore.clearOrder()
  order.value = orderStore.order
  totalProducts.value = orderStore.totalProducts
  router.push({ name: 'Products' })
}

onMounted(() => {
  order.value = orderStore.composingOrder
  totalProducts.value = sessionStorage.getItem('totalProducts')
})

</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Order</h3>
    </div>
    <div class="mx-2 total-filter">
      <h5 class="mt-4">Total Products: {{ totalProducts }}</h5>
    </div>
  </div>
  <hr>
  <table class="table">
    <thead>
      <tr>
        <th class="align-middle">Quantity</th>
        <th class="align-middle">Product</th>
        <th class="align-middle">Type</th>
        <th class="align-middle">Price</th>
        <th class="align-middle">Subtotal</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in order">
        <td class="align-middle">{{ row['quantity'] }}</td>
        <td class="align-middle">{{ row['name'] }}</td>
        <td class="align-middle">{{ row['type'] == 'hot dish' ? 'Hot Dish' : (row['type'] == 'cold dish' ? 'Cold Dish' :
            (row['type'] == 'drink' ? 'Drink' : 'Dessert'))
        }}</td>
        <td class="align-middle">{{ row['price'] }} €</td>
        <td class="align-middle">{{ row['subtotal'] }} €</td>
        <td class="text-end align-middle">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary" @click="more(row)"><i
                class="bi bi-xs bi-cart-plus-fill"></i></button>
            <button class="btn btn-xs btn-success" @click="less(row)"><i
                class="bi bi-xs bi-cart-dash-fill"></i></button>
            <button class="btn btn-xs btn-danger" @click="destroy(row)"><i class="bi bi-xs bi-trash3-fill"></i></button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="mx-2 total-filter">
    <h6 class="mt-4">Total Price: {{ totalPrice }} €</h6>
  </div>
  <div class="mt-5 mb-5 d-flex justify-content-center">
    <button v-if="userStore.user?.type == 'C' || !userStore.user" type="button" class="btn btn-primary px-5"
      @click="save">Pay
      Order</button>
    <button type="button" class="btn btn-light px-5" @click="cancel">Cancel Order</button>
  </div>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.total-filter {
  margin-top: 0.35rem;
}
</style>
