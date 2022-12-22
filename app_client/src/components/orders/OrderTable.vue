<script setup>
import { useUserStore } from '../../stores/user'

const userStore = useUserStore()

const props = defineProps({
  orders: {
    type: Array,
    default: () => [],
  },
  showStatus: {
    type: Boolean,
    default: false,
  },
  showToCustomer: {
    type: Boolean,
    default: false,
  },
  showDeliverer: {
    type: Boolean,
    default: false,
  },
  showBillInformation: {
    type: Boolean,
    default: true,
  },
  showTotalProducts: {
    type: Boolean,
    default: true,
  },
  showViewButton: {
    type: Boolean,
    default: true,
  },
  showDeliverButton: {
    type: Boolean,
    default: true,
  },
  showCancelButton: {
    type: Boolean,
    default: true,
  }
})

const emit = defineEmits(['view', 'pickup', 'deliver', 'cancel'])

const viewClick = (order) => {
  emit('view', order)
}

const pickupClick = (order) => {
  emit('pickup', order)
}

const cancelledClick = (order) => {
  emit('cancel', order)
}

</script>

<template>
  <table class="table" v-if="orders.length != 0">
    <thead>
      <tr>
        <th>Ticket</th>
        <th v-if="showStatus">Status</th>
        <th v-if="!showToCustomer">Customer</th>
        <th v-if="showDeliverer"><i class="bi bi-truck"></i></th>
        <th v-if="!showToCustomer"><i class="bi bi-calendar-date"></i></th>
        <th v-if="showBillInformation"><i class="bi bi-credit-card"></i></th>
        <th v-if="showBillInformation">Reference</th>
        <th v-if="!showToCustomer">Products</th>
        <th v-if="showBillInformation">Price</th>
        <th v-if="showBillInformation">Paid</th>
        <th v-if="showViewButton || showToCustomer/*|| showDeliverButton*/ || showCancelButton"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="order in orders" :key="order.id">
        <td>#{{ order.ticket_number }}</td>
        <td v-if="showStatus">{{ order.status_name }}</td>
        <td v-if="!showToCustomer">{{ order.customer_name ? order.customer_name.split(' ')[0] + ' ' + order.customer_name.split(' ')[order.customer_name.split(' ').length - 1] : '' }}</td>
        <td v-if="showDeliverer">{{ order.deliverer ? order.deliverer.split(' ')[0] + ' ' + order.deliverer.split(' ')[order.deliverer.split(' ').length - 1] : '' }}</td>
        <td class="text-nowrap" v-if="!showToCustomer">{{ order.date }}</td>
        <td v-if="showBillInformation">{{ order.payment_type }}</td>
        <td v-if="showBillInformation">{{ order.payment_reference }}</td>
        <td v-if="!showToCustomer">{{ order.total_products }}</td>
        <td v-if="showBillInformation">{{ order.total_price }} €</td>
        <td v-if="showBillInformation">{{ order.total_paid }} €</td>
        <td class="text-end" v-if="showViewButton || showToCustomer/*|| showDeliverButton*/ || showCancelButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary text-light" @click="viewClick(order)" v-if="showViewButton">
              <i class="bi bi-xs bi-eye-fill"></i>
            </button>
            <button v-if="order.status != 'C' && showCancelButton" class="btn btn-xs btn-warning text-light"
              @click="cancelledClick(order)">
              <i class="bi bi-xs bi-x-octagon-fill"></i>
            </button>
            <button v-if="(order.customer_id && order.customer_userId == userStore.userId) || (!userStore.user && !order.customer_id)" class="btn btn-xs btn-success text-light"
              @click="pickupClick(order)">
              <i class="bi bi-xs bi-box-seam-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <div v-else v-if="showToCustomer" class="d-flex justify-content-center">
    <h6>-- No Orders Ready Yet --</h6>
  </div>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>
