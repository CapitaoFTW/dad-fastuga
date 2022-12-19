<script setup>
import { ref } from 'vue'
import { useUserStore } from "../../stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  order: {
    type: Object,
    required: true
  },
  order_items: {
    type: Array,
    default: () => [],
  },
  users: {
    type: Array,
    required: true
  },
  showChef: {
    type: Boolean,
    default: true,
  },
  showDeliverer: {
    type: Boolean,
    default: false,
  },
  showChefButtons: {
    type: Boolean,
    default: false,
  },
  showReadyButton: {
    type: Boolean,
    default: false,
  },
  showCancelButton: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['back', 'prepareItem', 'readyItem', 'ready', 'deliver', 'cancel'])

const backClick = () => {
  emit('back')
}

const prepareItemClick = (order_item) => {
  emit('prepareItem', order_item)
}

const readyItemClick = (order_item) => {
  emit('readyItem', order_item)
}

const readyClick = (order) => {
  emit('ready', order)
}

const deliveredClick = (order) => {
  emit('deliver', order)
}

const cancelledClick = (order) => {
  emit('cancel', order)
}

</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Status</th>
        <th>Product</th>
        <th v-if="showChef">Chef</th>
        <th>Notes</th>
        <th>Price</th>
        <th v-if="showChefButtons"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="order_item in order_items" :key="order_item.id">
        <td>{{ order.ticket_number + '-' + order_item.order_local_number }}</td>
        <td>{{ order_item.status_name }}</td>
        <td>{{ order_item.product_name }}</td>
        <td v-if="showChef">{{ order_item.chef ?? '-- No Chef --' }}</td>
        <td>{{ order_item.notes }}</td>
        <td>{{ order_item.price }} €</td>
        <td class="text-end" v-if="showChefButtons">
          <div class="d-flex justify-content-end">
            <button v-if="order_item.status == 'W'" class="btn btn-xs btn-primary text-light"
              @click="prepareItemClick(order_item)">
              <i class="bi bi-xs bi-skip-start-btn-fill"></i>
            </button>
            <button v-if="order_item.status == 'P'" class="btn btn-xs btn-success text-light"
              @click="readyItemClick(order_item)">
              <i class="bi bi-xs bi-check-circle-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="row mt-4 mb-4 m-0">
    <div class="d-flex justify-content-between">
      <span><b>Total Price:</b> &nbsp;{{ order.total_price }} €</span>
      <span><b>Points Gained:</b> &nbsp;{{ order.points_gained }}</span>
      <span><b>Payment Type:</b> &nbsp;{{ order.payment_type }}</span>
    </div>
    <div class="row" style="padding-right: 0rem">
      <div class="col-4">
        <span><b>Total Paid:</b> &nbsp;{{ order.total_paid }} €</span>
      </div>
      <div class="col-4 text-center">
        <span><b>Points Used to Pay:</b> &nbsp;{{ order.points_used_to_pay }}</span>
      </div>
      <div class="col-4 pr-0 text-end" style="padding-right:0rem">
        <span><b>Payment Reference:</b> &nbsp;{{ order.payment_reference }}</span>
      </div>
    </div>
    <div class="d-flex justify-content-between">
      <span><b>Total Paid with Points:</b> &nbsp;{{ order.total_paid_with_points }} €</span>
      <span v-if="showDeliverer"><b>Deliverer:</b> {{ props.order.deliverer ?? 'None' }}</span>
    </div>
  </div>
  <div class="mb-5 d-flex justify-content-center">
    <button type="button" class="btn btn-primary" @click="backClick"><i class="bi bi-arrow-left-circle"></i><b
        class="position-relative" style="bottom:0.1rem">Back</b>
    </button>
    <button v-if="showReadyButton" class="btn btn-success text-light" @click="readyClick(order)"><b>Ready Order</b>
    </button>
    <button v-if="showCancelButton" class="btn btn-warning text-light" @click="cancelledClick(order)"><b>Cancel
        Order</b>
    </button>
  </div>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>