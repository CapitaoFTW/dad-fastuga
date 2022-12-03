<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import UserTable from "./UserTable.vue"

const router = useRouter()
const axios = inject('axios')
const users = ref([])

const totalUsers = computed(() => {
  return users.value.length
})

const loadUsers = () => {
  axios.get('users')
    .then((response) => {
      users.value = response.data.data
    })

    .catch((error) => {
      console.log(error)
    })
}

const editUser = (user) => {
  router.push({ name: 'User', params: { id: user.id } })
}

onMounted(() => {
  loadUsers()
})
</script>

<template>
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Users</h1>
    </div>
  <user-table :users="users" :showId="false" @edit="editUser"></user-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 2.3rem;
}
</style>

