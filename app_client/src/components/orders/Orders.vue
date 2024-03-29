<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user'
import { useOrdersStore } from "../../stores/orders.js"
import OrderTable from "./OrderTable.vue"

const router = useRouter()
const axios = inject("axios")
const toast = inject("toast")
const socket = inject("socket")

const userStore = useUserStore()
const ordersStore = useOrdersStore()
const orderToCancel = ref(null)
const filterByStatus = ref(!userStore.user || userStore.user?.type == 'C' ? 'R' : userStore.user?.type != 'EM' ? 'P' : '')
const cancelConfirmationDialog = ref(null)

const loadOrders = (page) => {
  ordersStore.loadOrders(page)
    .catch((error) => {
      console.log(error)
    })
}

const viewOrder = (order) => {
  router.push({ name: 'Order', params: { id: order.id } })
}

const pickupOrder = (order) => {
  ordersStore.pickupOrder(order)
    .then((response) => {
      loadOrders()
      toast.success('Order #' + order.ticket_number + ' was successfully picked up')
    })

    .catch((error) => {
      toast.error("It was not possible to pickup Order #" + order.ticket_number + "!")
    })
}

const cancelOrderConfirmed = () => {
  let payment = {
    type: orderToCancel.value.payment_type.toLowerCase(),
    reference: orderToCancel.value.payment_reference,
    value: Number(orderToCancel.value.total_paid)
  }

  ordersStore.refundOrder(payment)
    .then((payment) => {
      toast.success('Order was refunded successfully.')

      ordersStore.cancelOrder(orderToCancel.value)
        .then((cancelledOrder) => {
          loadOrders()
          toast.info("Order " + orderToCancelDescription.value + " was cancelled")
        })

        .catch(() => {
          toast.error("It was not possible to cancel Order " + orderToCancelDescription.value + "!")
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

const cancelOrder = (order) => {
  orderToCancel.value = order
  cancelConfirmationDialog.value.show()
}

const filteredOrders = computed(() => {
  return ordersStore.getOrdersByFilter(filterByStatus.value)
})

const totalOrders = computed(() => {
  return ordersStore.getOrdersByFilterTotal(filterByStatus.value)
})

const orderToCancelDescription = computed(() => {
  return orderToCancel.value ? `#${orderToCancel.value.ticket_number}` : ""
})

onMounted(() => {
  // Calling loadOrders refresh the list of orders from the API
  loadOrders()
})

</script>

<template>
  <confirmation-dialog ref="cancelConfirmationDialog" confirmationBtn="Cancel Order"
    :msg="`Do you really want to cancel the Order ${orderToCancelDescription}?`" @confirmed="cancelOrderConfirmed">
  </confirmation-dialog>

  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">{{ (!userStore.user || userStore.user?.type == 'C') ? 'Ready' : '' }} Orders </h3>
    </div>
    <div class="mx-2 total-filter">
      <h5 class="mt-4">Total: {{ totalOrders }}</h5>
    </div>
  </div>
  <hr>
  <form class="mb-3 d-flex justify-content-between flex-wrap" v-if="userStore.user && userStore.user?.type != 'C'">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectStatus" class="form-label">Filter by Status:</label>
      <select class="form-select" id="selectStatus" v-model="filterByStatus">
        <option v-if="userStore.user?.type == 'EM'" value="">Any</option>
        <option value="P">Pending</option>
        <option value="R">Ready</option>
        <option v-if="userStore.user?.type == 'EM'" value="C">Cancelled</option>
        <option v-if="userStore.user?.type == 'EM'" value="D">Delivered</option>
      </select>
    </div>
  </form>
  <order-table :orders="filteredOrders" :showStatus="filterByStatus == ''"
    :showDeliverer="filterByStatus == 'C' || filterByStatus == 'D' || filterByStatus == ''"
    :showBillInformation="userStore.user?.type == 'EM'" :showToCustomer="!userStore.user || userStore.user?.type == 'C'"
    :showViewButton="userStore.user && userStore.user?.type != 'C'" :showDeliverButton="userStore.user?.type == 'ED'"
    :showCancelButton="userStore.user?.type == 'EM'" @view="viewOrder" @pickup="pickupOrder" @cancel="cancelOrder">
  </order-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filter {
  margin-top: 0.35rem;
}
</style>
