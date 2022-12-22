<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"
import { useOrdersStore } from "../../stores/orders.js"
import { useOrderItemsStore } from "../../stores/order_items.js"

import OrderDetail from "./OrderDetail.vue"

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')
const socket = inject('socket')

const userStore = useUserStore()
const ordersStore = useOrdersStore()
const orderItemsStore = useOrderItemsStore()
const filterByStatus = ref(!userStore.user || userStore.user?.type == 'C' || userStore.user?.type == 'ED' ? 'R' : '')
const cancelConfirmationDialog = ref(null)

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const newOrder = () => {
  return {
    id: null,
    ticket_number: null,
    customer_id: userStore.customerId,
    points_used_to_pay: null,
    payment_type: null,
    payment_reference: null,
    delivered_by: null,
    total_products: null,
    order_items: [],
  }
}

let originalValueStr = ''
const loadOrder = (id) => {
  originalValueStr = ''
  axios.get('orders/' + id)
    .then((response) => {
      order.value = response.data.data
      originalValueStr = dataAsString()
    })

    .catch((error) => {
      if (error.response.status == 403)
        router.push({ name: 'Orders' }) // como as Orders são um array seria mais pesado fazer esta verificação no index do router
    })
}

const dataAsString = () => {
  return JSON.stringify(order.value)
}

const order = ref(newOrder())
const users = ref([])

watch(
  () => props.id,

  (newValue) => {
    loadOrder(newValue)
  },

  { immediate: true }
)

const back = () => {
  router.push({ name: 'Orders' })
}

const prepareItem = (order_item) => {
  orderItemsStore.prepareOrderItem(order_item, order.value.ticket_number)
    .then((preparedOrderItem) => {
      order_item = preparedOrderItem
      loadOrder(props.id)
      toast.success('Order Item #' + order.value.ticket_number + '-' + order_item.order_local_number + ' is now being prepared')
    })

    .catch((error) => {
      toast.error("It was not possible to prepare Order Item #" + order.value.ticket_number + '-' + order_item.order_local_number + "!")
    })
}

const readyItem = (order_item) => {
  orderItemsStore.readyOrderItem(order_item, order.value.ticket_number)
    .then((readyOrderItem) => {
      order_item = readyOrderItem
      loadOrder(props.id)
      toast.success('Order Item #' + order.value.ticket_number + '-' + order_item.order_local_number + ' is now ready')
    })

    .catch((error) => {
      toast.error("It was not possible to ready Order Item #" + order_item.order_local_number + "!")
    })
}

const readyOrder = (order) => {
  ordersStore.readyOrder(order)
    .then((response) => {
      loadOrder(props.id)
      toast.success('Order #' + order.ticket_number + ' is now ready to be delivered')
      router.back()
    })

    .catch((error) => {
      toast.error("It was not possible to ready Order #" + order.ticket_number + "!")
    })
}

const cancelOrderConfirmed = () => {
  let payment = {
    type: order.value.payment_type.toLowerCase(),
    reference: order.value.payment_reference,
    value: Number(order.value.total_paid)
  }

  ordersStore.refundOrder(payment)
    .then((payment) => {
      toast.success('Order was refunded successfully.')

      ordersStore.cancelOrder(order.value)
        .then((cancelledOrder) => {
          toast.info("Order #" + order.value.ticket_number + " was successfully cancelled")
          router.back()
        })

        .catch(() => {
          toast.error("It was not possible to cancel Order " + order.value.ticket_number + "!")
        })
    })

    .catch((error) => {
      if (error.response.status == 422) {
        toast.error('Order was not refunded because of: ' + error.response.data.message.toUpperCase())

      } else {
        toast.error('Order was not refunded due to unknown server error!')
      }
    })
}

const cancelOrder = () => {
  cancelConfirmationDialog.value.show()
}

const filteredOrderItems = computed(() => {
  return order.value.order_items.filter(order_item => (!filterByStatus.value || filterByStatus.value == order_item.status))
})

const areAllOrderItemsReady = computed(() => {
  return order.value.order_items.every(order_item => order_item.status == 'R')
})

socket.on('newHotDishes', () => {
  loadOrder(props.id)
})

socket.on('preparingOrderItem', () => {
  loadOrder(props.id)
})

socket.on('readyOrderItem', () => {
  loadOrder(props.id)
})

socket.on('readyOrder', () => {
  loadOrder(props.id)
})

socket.on('deliveredOrder', () => {
  loadOrder(props.id)
})

socket.on('cancelledOrder', () => {
  loadOrder(props.id)
})

onMounted(() => {
  users.value = []
  if (userStore.user?.type == 'EM') {
    axios.get('users')
      .then((response) => {
        users.value = response.data.data
      })

      .catch((error) => {
        console.log(error)
      })
  }
})
</script>

<template>
  <confirmation-dialog ref="cancelConfirmationDialog" confirmationBtn="Cancel Order"
    :msg="`Do you really want to cancel this order?`" @confirmed="cancelOrderConfirmed">
  </confirmation-dialog>

  <div class="d-flex justify-content-start">
    <div class="mx-2">
      <h3 class="mt-4">Order #{{ order.ticket_number }}</h3>
    </div>
  </div>
  <hr>
  <div v-if="userStore.user?.type != 'EM' && userStore.user?.type != 'ED' && !areAllOrderItemsReady"
    class="mb-3 mt-2 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectStatus" class="form-label">Filter by Status:</label>
      <select class="form-select" id="selectStatus" v-model="filterByStatus">
        <option value="">Any</option>
        <option value="W">Waiting</option>
        <option value="P">Preparing</option>
        <option value="R">Ready</option>
      </select>
    </div>
    <!--<div class="mx-2 mt-2 flex-grow-1 filter-div" v-if="">
      <label for="selectCustomer" class="form-label">Filter by customer:</label>
      <select class="form-select" id="selectOwner" v-model="filterByCustomerId">
        <option :value="null"></option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
    </div>-->
  </div>
  <OrderDetail :order="order" :order_items="filteredOrderItems" :users="users"
    :showChef="userStore.user && userStore.user?.type != 'C'" :showChefButtons="userStore.user?.type == 'EC'"
    :showReadyButton="userStore.user?.type == 'ED' && order.status == 'P' && areAllOrderItemsReady"
    :showCancelButton="userStore.user?.type == 'EM' && order.status != 'C'"
    :showDeliverer="userStore.user?.type != 'ED' && userStore.user?.type != 'EC' && (order.status == 'D' || order.status == 'C')"
    @back="back" @prepareItem="prepareItem" @readyItem="readyItem" @ready="readyOrder" @cancel="cancelOrder">
  </OrderDetail>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filter {
  margin-top: 0.35rem;
}
</style>
