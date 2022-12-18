<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user'
import { useOrdersStore } from "../../stores/orders.js"
import OrderTable from "./OrderTable.vue"

const router = useRouter()
const axios = inject("axios")
const toast = inject("toast")

const userStore = useUserStore()
const ordersStore = useOrdersStore()
const orderToDelete = ref(null)
const users = ref([])
const filterByCustomerId = ref(null)
const filterByStatus = ref(userStore.user?.type == 'ED' ? 'R' : userStore.user?.type == 'EC' ? 'P' : '')
const deleteConfirmationDialog = ref(null)

const loadOrders = () => {
  ordersStore.loadOrders()
    .catch((error) => {
      console.log(error)
    })
}

const loadUsers = () => {
  axios.get('users')
    .then((response) => {
      users.value = response.data.data
    })
    .catch((error) => {
      console.log(error)
    })
}

const editOrder = (order) => {
  router.push({ name: 'Order', params: { id: order.id } })
}

const readyOrder = (order) => {
  ordersStore.readyOrder(order)
    .then((response) => {
      toast.success('Order #' + order.ticket_number + ' is now ready to be delivered')
    })

    .catch((error) => {
      toast.error("It was not possible to ready Order #" + order.ticket_number + "!")
    })
}

const deliverOrder = (order) => {
  ordersStore.deliverOrder(order)
    .then((response) => {
      toast.success('Order #' + order.ticket_number + ' was delivered')
    })

    .catch((error) => {
      toast.error("It was not possible to deliver Order #" + order.ticket_number + "!")
    })
}

const deleteOrderConfirmed = () => {
  ordersStore.deleteOrder(orderToDelete.value)
    .then((deletedOrder) => {
      toast.info("Order " + orderToDeleteDescription.value + " was deleted")
    })

    .catch(() => {
      toast.error("It was not possible to delete Order " + orderToDeleteDescription.value + "!")
    })
}

const clickToDeleteOrder = (order) => {
  orderToDelete.value = order
  deleteConfirmationDialog.value.show()
}

const filteredOrders = computed(() => {
  return ordersStore.getOrdersByFilter(filterByStatus.value)
})

const totalOrders = computed(() => {
  return ordersStore.getOrdersByFilterTotal(filterByStatus.value)
})

const orderToDeleteDescription = computed(() => {
  return orderToDelete.value ? `#${orderToDelete.value.id} (${orderToDelete.value.name})` : ""
})

onMounted(() => {
  loadUsers()
  // Calling loadOrders refresh the list of orders from the API
  loadOrders()
})

</script>

<template>
  <confirmation-dialog ref="deleteConfirmationDialog" confirmationBtn="Delete order"
    :msg="`Do you really want to delete the order ${orderToDeleteDescription}?`" @confirmed="deleteOrderConfirmed">
  </confirmation-dialog>

  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Orders</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ totalOrders }}</h5>
    </div>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectStatus" class="form-label">Filter by Status:</label>
      <select class="form-select" id="selectStatus" v-model="filterByStatus">
        <option value="">Any</option>
        <option value="P">Pending</option>
        <option value="R">Ready</option>
        <option value="C">Cancelled</option>
        <option value="D">Delivered</option>
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
  <order-table :orders="filteredOrders" :showId="false" :showDates="true" @edit="editOrder" @ready="readyOrder"
    @deliver="deliverOrder" @delete="clickToDeleteOrder"></order-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 0.35rem;
}

.btn-addorder {
  margin-top: 1.85rem;
}
</style>
