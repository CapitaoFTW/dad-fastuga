<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"
import { useOrdersStore } from "../../stores/orders.js"

import OrderDetail from "./OrderDetail.vue"

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')
const userStore = useUserStore()
const ordersStore = useOrdersStore()


const newOrder = () => {
  return {
    id: null,
    ticket_number: null,
    customer_id: userStore.userId,
    status: 'P',
    date: null,
    total_price: null,
    total_paid: null,
    total_paid_with_points: null,
    points_gained: null,
    points_used_to_pay: null,
    payment_type: null,
    payment_reference: null,
    delivered_by: null,
    total_products: null,
  }
}

let originalValueStr = ''
const loadOrder = (id) => {
  originalValueStr = ''
  errors.value = null

  if (!id || (id < 0)) {
    order.value = newOrder()
    originalValueStr = dataAsString()

  } else {
    axios.get('orders/' + id)
      .then((response) => {
        order.value = response.data.data
        originalValueStr = dataAsString()
      })

      .catch((error) => {
        console.log(error)
      })
  }
}

/* Change this function */
const save = () => {
  errors.value = null

  if (operation.value == 'insert') {
    ordersStore.insertOrder(order.value)

      .then((insertedOrder) => {
        order.value = insertedOrder
        originalValueStr = dataAsString()

        toast.success('Order #' + order.value.ticket_number + ' was created successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('Order was not created due to validation errors!')
          errors.value = error.response.data.errors
        } else {
          toast.error('Order was not created due to unknown server error!')
        }
      })

  } else {
    ordersStore.updateOrder(order.value)
      .then((updatedOrder) => {
        order.value = updatedOrder
        originalValueStr = dataAsString()

        toast.success('Order #' + order.value.ticket_number + ' was updated successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('Order #' + order.value.ticket_number + ' was not updated due to validation errors!')
          errors.value = error.response.data.errors

        } else {
          toast.error('Order #' + order.value.ticket_number + ' was not updated due to unknown server error!')
        }
      })
  }
}

const cancel = () => {
  originalValueStr = dataAsString()
  router.back()
}

const dataAsString = () => {
  return JSON.stringify(order.value)
}

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = dataAsString()

  if (originalValueStr != newValueStr) {
    nextCallBack = next
    confirmationLeaveDialog.value.show()

  } else {
    next()
  }
})

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const order = ref(newOrder())
const users = ref([])
const errors = ref(null)
const confirmationLeaveDialog = ref(null)

const operation = computed(() => {
  return (!props.id || props.id < 0) ? 'insert' : 'update'
})

watch(
  () => props.id,

  (newValue) => {
    loadOrder(newValue)
  },

  { immediate: true, }
)

onMounted(() => {
  users.value = []
  axios.get('users')
    .then((response) => {
      users.value = response.data.data
    })

    .catch((error) => {
      console.log(error)
    })
})
</script>

<template>
  <confirmation-dialog ref="confirmationLeaveDialog" confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!" @confirmed="leaveConfirmed">
  </confirmation-dialog>

  <OrderDetail :operationType="operation" :order="order" :users="users" :errors="errors" @save="save" @cancel="cancel">
  </OrderDetail>
</template>
