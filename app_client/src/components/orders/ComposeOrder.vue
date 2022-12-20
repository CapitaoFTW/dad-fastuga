<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"
import { useOrderItemsStore } from '../../stores/order_items'
import { useOrdersStore } from '../../stores/orders'

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')

const userStore = useUserStore()
const orderItemsStore = useOrderItemsStore()
const ordersStore = useOrdersStore()

const order_items = ref([])
const totalProducts = ref(0)

const totalPrice = computed(() => {
  let total = 0

  for (const [key] of Object.entries(orderItemsStore.order_items)) {
    total += Number(orderItemsStore.order_items[key].product_subtotal)
  }

  return Number(total.toFixed(2))
})

const more = (row) => {
  orderItemsStore.updateOrderItems(row, 1)

  order_items.value = orderItemsStore.order_items
  totalProducts.value = orderItemsStore.totalProducts
}

const less = (row) => {
  orderItemsStore.updateOrderItems(row, -1)

  order_items.value = orderItemsStore.order_items
  totalProducts.value = orderItemsStore.totalProducts

  if (totalProducts.value <= 0) {
    router.push({ name: 'Products' })
  }
}

const destroy = (row) => {
  orderItemsStore.updateOrderItems(row, 0)

  order_items.value = orderItemsStore.order_items
  totalProducts.value = orderItemsStore.totalProducts

  if (totalProducts.value <= 0) {
    router.push({ name: 'Products' })
  }
}

const newOrder = () => {
  return {
    customer_id: userStore.user ? userStore.customerId : null,
    customer_points: userStore.user ? userStore.user.customer.points : null,
    total_price: totalPrice.value,
    points_used_to_pay: 0,
    total_paid_with_points: 0 /*points_used_to_pay / 2*/,
    total_paid: totalPrice.value /*- total_paid_with_points*/,
    points_gained: userStore.user && totalPrice.value >= 10 /*&& points_used_to_pay == 0*/ ? Math.floor(totalPrice.value / 10) : 0,
    payment_type: userStore.user ? userStore.user.customer.default_payment_type : '',
    payment_reference: userStore.user ? userStore.user.customer.default_payment_reference : '',
    order_items: orderItemsStore.order_items,
  }
}

const order = ref(newOrder())

const save = () => {
  errors.value = null

  if (order.value.points_used_to_pay != 0) {
    order.value.total_paid_with_points = order.value.points_used_to_pay / 2
    order.value.total_paid -= order.value.total_paid_with_points
    order.value.points_gained = 0
  }

  /*let payment = {
    type: userStore.user.customer.default_payment_type.toLowerCase(),
    reference: userStore.user.customer.default_payment_reference,
    value: Number(totalPrice.value)
  }
 
  orderStore.payOrder(payment)
    .then((payment) => {
      toast.success('Order was paid successfully.')*/

  ordersStore.insertOrder(order.value)
    .then((insertedOrder) => {
      toast.success('Order #' + insertedOrder.ticket_number + ' was created successfully.')
      orderItemsStore.clearOrderItems()

      router.push({ name: 'Products' })
    })

    .catch((error) => {
      if (error.response.status == 422) {
        toast.error('Order was not created due to validation errors!')
        errors.value = error.response.data.errors

        console.log(error)

      } else {
        console.log(error)
        toast.error('Order was not created due to unknown server error!')
      }
    })
  /*})
 
  .catch((error) => {
    if (error.response.status == 422) {
      toast.error('Order was not paid due to validation errors!')
      errors.value = error.response.data.errors
 
    } else {
      console.log(error)
      toast.error('Order was not created due to unknown server error!')
    }
  })*/
}

const errors = ref(null)

const cancel = () => {
  orderItemsStore.clearOrderItems()
  order_items.value = orderItemsStore.order_items
  totalProducts.value = orderItemsStore.totalProducts
  router.push({ name: 'Products' })
}

onMounted(() => {
  order_items.value = orderItemsStore.order_items
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
      <tr v-for="order_item in order_items">
        <td class="align-middle">{{ order_item['product_quantity'] }}</td>
        <td class="align-middle">{{ order_item['product_name'] }}</td>
        <td class="align-middle">{{ order_item['product_type'] == 'hot dish' ? 'Hot Dish' : (order_item['product_type']
            == 'cold dish'
            ? 'Cold Dish' : (order_item['product_type'] == 'drink' ? 'Drink' : 'Dessert'))
        }}</td>
        <td class="align-middle">{{ order_item['product_price'] }} €</td>
        <td class="align-middle">{{ order_item['product_subtotal'] }} €</td>
        <td class="text-end align-middle">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary" @click="more(order_item)"><i
                class="bi bi-xs bi-cart-plus-fill"></i></button>
            <button class="btn btn-xs btn-success" @click="less(order_item)"><i
                class="bi bi-xs bi-cart-dash-fill"></i></button>
            <button class="btn btn-xs btn-danger" @click="destroy(order_item)"><i
                class="bi bi-xs bi-trash3-fill"></i></button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="mx-2 mb-3 total-filter">
    <h6 class="mt-4">Total Price: {{ totalPrice }} €</h6>
  </div>
  <form class="row pt-3 pb-2 mb-3 needs-validation justify-content-center" novalidate @submit.prevent="save">
    <div class="w-75">
      <h4 class="mb-3 text-center">Payment</h4>
      <div class="mb-3" v-if="userStore.user?.type == 'C'">
        <label for="inputPoints" class="form-label">Use Points to Pay (Remaining: <b>{{ order.customer_points
        }}</b>)</label>
        <input type="number" min="0" step="10" class="form-control" id="inputPoints" placeholder="Points"
          v-model="order.points_used_to_pay" />
        <field-error-message :errors="errors" fieldName="points_used_to_pay"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPaymentType" class="form-label">Payment Type</label>
        <select class="form-select" id="inputPaymentType" v-model="order.payment_type" required>
          <option :value="null" disabled>Choose an option</option>
          <option value="VISA">VISA</option>
          <option value="MBWAY">MBWAY</option>
          <option value="PAYPAL">PAYPAL</option>
        </select>
        <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPaymentReference" class="form-label">Payment Reference</label>
        <input type="text" class="form-control" id="inputPaymentReference" placeholder="Payment Reference"
          v-model="order.payment_reference" />
        <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
      </div>
    </div>
    <div class="mt-5 mb-5 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="save">Pay Order</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel Order</button>
    </div>
  </form>
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
