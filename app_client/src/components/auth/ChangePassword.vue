<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user.js'

const router = useRouter()
const toast = inject('toast')

const passwords = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const userStore = useUserStore()
const errors = ref(null)

const emit = defineEmits(['changedPassword'])

const changePassword = async () => {
  errors.value = null
  const error = await userStore.changePassword(passwords.value)

  if (error) {
    passwords.value.password = ''

    toast.error('User credentials are invalid!')
    errors.value = error.response.data.errors

  } else {
    toast.success('User ' + userStore.user.name + ' password was successfully changed.')

    emit('changePassword')
    router.push({ name: 'User', params: { id: userStore.user.id } })
  }
}
</script>

<template>
  <form class="row pt-5 mt-5 needs-validation justify-content-center" novalidate @submit.prevent="changePassword">
    <div class="w-50 mt-4">
      <h3 class="mb-4 text-center">Change Password</h3>
      <div class="mb-3">
        <div class="mb-3">
          <label for="inputCurrentPassword" class="form-label">Current Password</label>
          <input type="password" class="form-control" id="inputCurrentPassword" required
            v-model="passwords.current_password">
          <field-error-message :errors="errors" fieldName="current_password"></field-error-message>
        </div>
      </div>
      <div class="mb-3">
        <div class="mb-3">
          <label for="inputPassword" class="form-label">New Password</label>
          <input type="password" class="form-control" id="inputPassword" required v-model="passwords.password">
          <field-error-message :errors="errors" fieldName="password"></field-error-message>
        </div>
      </div>
      <div class="mb-3">
        <div class="mb-3">
          <label for="inputPasswordConfirm" class="form-label">Password Confirmation</label>
          <input type="password" class="form-control" id="inputPasswordConfirm" required
            v-model="passwords.password_confirmation">
          <field-error-message :errors="errors" fieldName="password_confirmation"></field-error-message>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-center">
        <button type="button" class="btn btn-primary" @click="changePassword">Change</button>
      </div>
    </div>
  </form>
</template>
