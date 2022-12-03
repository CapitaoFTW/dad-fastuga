<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useOrdersStore } from "../../stores/orders.js"
import OrderTable from "./OrderTable.vue"

const router = useRouter()
const axios = inject("axios")
const toast = inject("toast")

const ordersStore = useOrdersStore()
const orderToDelete = ref(null)
const users = ref([])
const filterByCustomerId = ref(null)
const filterByStatus = ref('P')
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

const addOrder = () => {
  router.push({ name: 'NewOrder' })
}

const editOrder = (order) => {
  router.push({ name: 'Order', params: { id: order.id } })
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
  return ordersStore.getOrdersByFilter(filterByCustomerId.value, filterByStatus.value)
})

const totalOrders = computed(() => {
  return ordersStore.getOrdersByFilterTotal(filterByCustomerId.value, filterByStatus.value)
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
        <option :value="null" disabled>Choose an option</option>
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
    <div class="mx-2 mt-2">
      <button type="button" class="btn btn-success px-4 btn-addorder" @click="addOrder"><i
          class="bi bi-xs bi-file-earmark-plus"></i>&nbsp; Add Order</button>
    </div>
  </div>
  <order-table :orders="filteredOrders" :showId="false" :showDates="true" @edit="editOrder"
    @delete="clickToDeleteOrder"></order-table>
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
