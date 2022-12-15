<script setup>
import { computed } from "vue"
import { useUserStore } from "../../stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  orders: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: false,
  },
  showStatus: {
    type: Boolean,
    default: false,
  },
  showPrice: {
    type: Boolean,
    default: true,
  },
  showBillInformation: {
    type: Boolean,
    default: true,
  },
  showDate: {
    type: Boolean,
    default: true,
  },
  showTotalProducts: {
    type: Boolean,
    default: false,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: true,
  }
})

const emit = defineEmits(['edit', 'delete', 'complete'])

const editClick = (order) => {
  emit('edit', order)
}

const deleteClick = (order) => {
  emit('delete', order)
}

const cancelledOrDelivered = computed(() => {
  return props.orders.filter(order => order.status == 'C' || order.status == 'D')
})

const completedClick = (order,user) => {
  emit('complete', order,user)
}

</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th v-if="showId">ID</th>
        <th>#</th>
        <th v-if="showStatus">Status</th>
        <th>Customer</th>
        <th v-if="showDate">Date</th>
        <th v-if="showBillInformation">Total Price</th>
        <th v-if="showBillInformation">Total Paid</th>
        <th v-if="showBillInformation">Total Paid with Points</th>
        <th v-if="showBillInformation">Points Gained</th>
        <th v-if="showBillInformation">Points Used</th>
        <th v-if="showBillInformation">Payment Type</th>
        <th v-if="showBillInformation">Reference</th>
        <th v-for="order in cancelledOrDelivered.splice(0, 1)">Deliverer</th>
        <th v-if="showTotalProducts">Total Products</th>
        <th v-if="showEditButton || showDeleteButton"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="order in orders" :key="order.id">
        <td v-if="showId">{{ order.id }}</td>
        <td>{{ order.ticket_number }}</td>
        <td v-if="showStatus">{{ order.status_name }}</td>
        <td>{{ order.customer_name }}</td>
        <td v-if="showDate">{{ order.date }}</td>
        <td v-if="showBillInformation">{{ order.total_price }} €</td>
        <td v-if="showBillInformation">{{ order.total_paid }} €</td>
        <td v-if="showBillInformation">{{ order.total_paid_with_points }} €</td>
        <td v-if="showBillInformation">{{ order.points_gained }}</td>
        <td v-if="showBillInformation">{{ order.points_used_to_pay }}</td>
        <td v-if="showBillInformation">{{ order.payment_type }}</td>
        <td v-if="showBillInformation">{{ order.payment_reference }}</td>
        <td v-if="order.delivered_by">{{ order.deliverer }}</td>
        <td v-if="showTotalProducts">{{ order.total_products }}</td>
        <td class="text-end" v-if="showEditButton || showDeleteButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary text-light" @click="editClick(order)" v-if="showEditButton">
              <i class="bi bi-xs bi-pencil-fill"></i>
            </button>
            <button v-if="userStore.user?.type == 'EC'" class="btn btn-success text-light" @click="completedClick(order, userStore.user)">
              <i class="bi bi-check2"></i>
            </button>
            <button class="btn btn-xs btn-danger" @click="deleteClick(order)" v-if="showDeleteButton"><i
                class="bi bi-xs bi-trash3-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>
