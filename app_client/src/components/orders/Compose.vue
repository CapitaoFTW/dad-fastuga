<script setup>
import { ref, watch, computed, onMounted, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user.js"

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')
const userStore = useUserStore()

const loadOrder = () => {
  return sessionStorage.getItem('order')
}

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const users = ref([])
const errors = ref(null)
const confirmationLeaveDialog = ref(null)

watch(
  () => props.id,

  (newValue) => {
    loadOrder(newValue)
  },

  { immediate: true, }
)

const order = computed(() => {
  return sessionStorage.getItem('order')
})

onMounted(() => {
  loadOrder()
})
</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Orders</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ order }}</h5>
    </div>
  </div>
  <hr>
</template>
