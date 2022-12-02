<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user.js'

const router = useRouter()
const toast = inject('toast')

const credentials = ref({
  email: '',
  password: ''
})

const userStore = useUserStore()

const emit = defineEmits(['login'])

const login = async () => {
  if (await userStore.login(credentials.value)) {
    toast.success('User ' + userStore.user.name + ' has entered the application.')

    emit('login')
    router.back()

  } else {
    credentials.value.password = ''
    toast.error('User credentials are invalid!')
  }
}
</script>


<template>
  <form class="row pt-5 mt-5 needs-validation justify-content-center" novalidate @submit.prevent="login">
    <div class="w-75 mt-4">
      <h3 class="mb-4 text-center">Login</h3>
      <div class="mb-3">
        <label for="inputUsername" class="form-label">E-mail</label>
        <input type="text" class="form-control" id="inputUsername" v-model="credentials.username">
      </div>
      <div class="mb-3">
        <label for="inputPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="inputPassword" v-model="credentials.password">
      </div>
      <div class="mt-5 d-flex justify-content-center">
        <button type="button" class="btn btn-primary px-5" @click="login">Login</button>
      </div>
    </div>
  </form>
</template>

