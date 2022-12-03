<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user.js'

const router = useRouter()
const toast = inject('toast')
const errors = ref(null)
const userStore = useUserStore()

const credentials = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  nif: '',
  payment_type: '',
  payment_reference: ''
})

const emit = defineEmits(['register'])

const register = async () => {
  errors.value = null
  const error = await userStore.register(credentials.value)

  if (error) {
    credentials.value.password = ''

    if (error.response.status == 422) {
      toast.error('User credentials are invalid!')
      errors.value = error.response.data.errors

    } else {

      toast.error('User was not updated due to unknown server error!')
    }

  } else {
    toast.success('User ' + userStore.user.name + ' was successfully registered')

    emit('register')
    router.push({ name: 'User', params: { id: userStore.user.id } })
  }
}

</script>

<template>
  <form class="row pt-3 pb-2 mb-3 needs-validation justify-content-center" novalidate @submit.prevent="register">
    <div class="w-50 mt-4">
      <h3 class="mb-4 text-center">Register</h3>
      <div class="mb-3">
        <label for="inputName" class="form-label">Name</label>
        <input type="text" class="form-control" id="inputName" required v-model="credentials.name">
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputEmail" class="form-label">E-mail</label>
        <input type="email" class="form-control" id="inputEmail" required v-model="credentials.email">
        <field-error-message :errors="errors" fieldName="email"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="inputPassword" required v-model="credentials.password">
        <field-error-message :errors="errors" fieldName="password"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPasswordConfirm" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="inputPasswordConfirm" required
          v-model="credentials.password_confirmation">
        <field-error-message :errors="errors" fieldName="password_confirmation"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPhone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="inputPhone" required v-model="credentials.phone">
        <field-error-message :errors="errors" fieldName="phone"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputNIF" class="form-label">NIF</label>
        <input type="text" class="form-control" id="inputNIF" required v-model="credentials.nif">
        <field-error-message :errors="errors" fieldName="nif"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPaymentType" class="form-label">Payment Type</label>
        <input type="text" class="form-control" id="inputPaymentType" required v-model="credentials.payment_type">
        <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
      </div>
      <div class="mb-4">
        <label for="inputPaymentRef" class="form-label">Payment Reference</label>
        <input type="text" class="form-control" id="inputPaymentRef" required v-model="credentials.payment_reference">
        <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
      </div>
      <div class="d-flex justify-content-center mt-2">
        <button type="button" class="btn btn-primary px-5" @click="register">Register</button>
      </div>
    </div>
  </form>
</template>