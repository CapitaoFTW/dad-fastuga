<script setup>
import { ref, watch, computed } from 'vue'
import { useUserStore } from "../../stores/user.js"
const userStore = useUserStore()

const props = defineProps({
  order: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    required: false
  },
  operationType: {
    type: String,
    default: 'insert'  // insert / update
  },
  users: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['save', 'cancel', 'complete'])

const editingOrder = ref(props.order)

watch(
  () => props.order,

  (newOrder) => {
    editingOrder.value = newOrder
  }
)

const orderTitle = computed(() => {
  if (!editingOrder.value) {
    return ''
  }

  return props.operationType == 'insert' ? 'New Order' : 'Order #' + editingOrder.value.id
})

const save = () => {
  emit('save', editingOrder.value)
}

const cancel = () => {
  emit('cancel', editingOrder.value)
}

const complete = () => {
  emit('complete', editingOrder.value)
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{ orderTitle }}</h3>
    <hr>
    <div class="d-flex flex-wrap justify-content-between">
      <div class="row mb-3 bill_information">
        <label for="inputTotalPrice" class="col-sm-3 col-form-label">Total Price</label>
        <div class="col-sm-9">
          <input type="number" class="form-control" id="inputTotalPrice" v-model="editingOrder.total_price">
          <field-error-message :errors="errors" fieldName="total_price"></field-error-message>
        </div>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputTotalPaid" class="col-sm-3 col-form-label">Total Paid</label>
        <div class="col-sm-9">
          <input type="number" class="form-control" id="inputTotalPaid" v-model="editingOrder.total_paid">
          <field-error-message :errors="errors" fieldName="total_paid"></field-error-message>
        </div>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputTotalPaidPoints" class="col-sm-3 col-form-label">Total Paid</label>
        <div class="col-sm-9">
          <input type="number" class="form-control" id="inputTotalPaidPoints"
            v-model="editingOrder.total_paid_with_points">
          <field-error-message :errors="errors" fieldName="total_paid_with_points"></field-error-message>
        </div>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputPoints" class="col-sm-3 col-form-label">Points Gained</label>
        <div class="col-sm-9">
          <input type="number" class="form-control" id="inputPoints" v-model="editingOrder.points_gained">
          <field-error-message :errors="errors" fieldName="points_gained"></field-error-message>
        </div>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputPointsPay" class="col-sm-3 col-form-label">Points Used to Pay</label>
        <div class="col-sm-9">
          <input type="number" class="form-control" id="inputPointsPay" v-model="editingOrder.points_used_to_pay">
          <field-error-message :errors="errors" fieldName="points_used_to_pay"></field-error-message>
        </div>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputPaymentType" class="form-label">Payment Type</label>
        <select class="form-select" id="inputPaymentType" v-model="editingOrder.payment_type">
          <option :value="null"></option>
          <option value="VISA">VISA</option>
          <option value="MBWAY">MBWAY</option>
          <option value="PAYPAL">PAYPAL</option>
        </select>
        <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
      </div>
      <div class="row mb-3 bill_information">
        <label for="inputPaymentReference" class="col-sm-3 col-form-label">Reference</label>
        <div class="col-sm-9">
          <input type="string" class="form-control" id="inputPaymentReference" v-model="editingOrder.payment_reference">
          <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
        </div>
      </div>
    </div>

    <div class="mb-3 me-3 flex-grow-1">
      <label for="inputDeliverer" class="form-label">Deliverer</label>
      <select class="form-select pe-2" id="inputDeliverer" v-model="editingOrder.delivered_by">
        <option :value="null">-- No Deliverer --</option>
        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
      </select>
      <field-error-message :errors="errors" fieldName="delivered_by"></field-error-message>
    </div>

    <div class="d-flex flex-wrap justify-content-between">
      <div class="mb-3 me-3 flex-grow-1">
        <label for="inputTotalProducts" class="form-label">Total Products</label>
        <input type="number" class="form-control" id="inputTotalProducts" placeholder="Total Products" v-model="editingOrder.total_products">
        <field-error-message :errors="errors" fieldName="total_products"></field-error-message>
      </div>
    </div>

    <div class="mb-3 d-flex justify-content-end">
      <button v-if="userStore.user?.type == 'EC' && order.status == 'P'" class="btn btn-success px-5" @click="complete">
        Complete
      </button>
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>

  </form>
</template>

<style scoped>
.bill_information {
  width: 26rem;
}
</style>
