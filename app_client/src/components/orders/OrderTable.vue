<script setup>

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

const emit = defineEmits(['view', 'deliver', 'cancel'])

const viewClick = (order) => {
  emit('view', order)
}

const deliveredClick = (order) => {
  emit('deliver', order)
}

const cancelledClick = (order) => {
  emit('cancel', order)
}

</script>

<template>
  <table class="table" v-if="orders.length != 0">
    <thead>
      <tr>
        <th>#</th>
        <th v-if="showStatus">Status</th>
        <th v-if="!showToCustomer">Customer</th>
        <th v-if="showDeliverer">Deliverer</th>
        <th v-if="!showToCustomer">Date</th>
        <th v-if="showBillInformation">Payment Type</th>
        <th v-if="showBillInformation">Reference</th>
        <th v-if="!showToCustomer">Total Products</th>
        <th v-if="showBillInformation">Total Price</th>
        <th v-if="showBillInformation">Total Paid</th>
        <th v-if="showViewButton || showDeliverButton || showCancelButton"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="order in orders" :key="order.id">
        <td>{{ order.ticket_number }}</td>
        <td v-if="showStatus">{{ order.status_name }}</td>
        <td v-if="!showToCustomer">{{ order.customer_name ?? '-- No Customer --' }}</td>
        <td v-if="showDeliverer">{{ order.deliverer ?? '-- No Deliverer --' }}</td>
        <td v-if="!showToCustomer">{{ order.date }}</td>
        <td v-if="showBillInformation">{{ order.payment_type }}</td>
        <td v-if="showBillInformation">{{ order.payment_reference }}</td>
        <td v-if="!showToCustomer">{{ order.total_products }}</td>
        <td v-if="showBillInformation">{{ order.total_price }} €</td>
        <td v-if="showBillInformation">{{ order.total_paid }} €</td>
        <td class="text-end" v-if="showViewButton || showDeliverButton || showCancelButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-primary text-light" @click="viewClick(order)" v-if="showViewButton">
              <i class="bi bi-xs bi-eye-fill"></i>
            </button>
            <button v-if="order.status == 'R' && showDeliverButton" class="btn btn-xs btn-success text-light"
              @click="deliveredClick(order)">
              <i class="bi bi-xs bi-box-seam-fill"></i>
            </button>
            <button v-if="order.status != 'C' && showCancelButton" class="btn btn-xs btn-warning text-light"
              @click="cancelledClick(order)">
              <i class="bi bi-xs bi-x-octagon-fill"></i>
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
