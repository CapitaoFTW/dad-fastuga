<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from '../../stores/users.js';
import { useUserStore } from "../../stores/user.js"

import UserTable from "./UserTable.vue"

const router = useRouter()
const toast = inject("toast")
const socket = inject('socket')

const usersStore = useUsersStore()
const userStore = useUserStore()
const userToDelete = ref(null)
const filterByType = ref('')
const deleteConfirmationDialog = ref(null)

const loadUsers = () => {
  usersStore.loadUsers()
    .catch((error) => {
      console.log(error)
    })
}

const addUser = () => {
  router.push({ name: 'NewUser' })
}

const editUser = (user) => {
  router.push({ name: 'User', params: { id: user.id } })
}

const blockUser = (user) => {
  if (usersStore.blockUser(user)) {
    if (user.blocked == 0)
      toast.success("User " + user.name + " was unblocked")

    else
      toast.success("User " + user.name + " was blocked")

    socket.emit('blockUser', { user, manager: userStore.user.name })

  } else {

    if (user.blocked == 0)
      toast.error("It was not possible to block User " + user.name + "!")

    else
      toast.error("It was not possible to unblock User " + user.name + "!")
  }
}

const deleteUserConfirmed = () => {
  usersStore.deleteUser(userToDelete.value)
    .then((deletedUser) => {
      toast.info("User " + userToDeleteDescription.value + " was deleted")

      socket.emit('deleteUser', { user: userToDelete.value, manager: userStore.user.name })
    })

    .catch(() => {
      toast.error("It was not possible to delete User " + userToDeleteDescription.value + "!")
    })
}

const clickToDeleteUser = (user) => {
  userToDelete.value = user
  deleteConfirmationDialog.value.show()
}

const filteredUsers = computed(() => {
  return usersStore.getUsersByFilter(filterByType.value)
})

const totalUsers = computed(() => {
  return usersStore.getUsersByFilterTotal(filterByType.value)
})

const userToDeleteDescription = computed(() => {
  return userToDelete.value ? `#${userToDelete.value.id} (${userToDelete.value.name})` : ""
})

socket.on('newUser', () => {
  loadUsers()
})

socket.on('updateUser', () => {
  loadUsers()
})

socket.on('deleteUser', () => {
  loadUsers()
})

socket.on('blockUser', () => {
  loadUsers()
})

onMounted(() => {
  loadUsers()
})
</script>

<template>
  <confirmation-dialog ref="deleteConfirmationDialog" confirmationBtn="Delete user"
    :msg="`Do you really want to delete the user ${userToDeleteDescription}?`" @confirmed="deleteUserConfirmed">
  </confirmation-dialog>

  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">Users</h3>
    </div>
    <div class="mx-2 total-filter">
      <h5 class="mt-4">Total: {{ totalUsers }}</h5>
    </div>
  </div>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectType" class="form-label">Filter by Type:</label>
      <select class="form-select" id="selectType" v-model="filterByType">
        <option value="">Any</option>
        <option value="C">Customer</option>
        <option value="ED">Employee Delivery</option>
        <option value="EC">Employee Chef</option>
        <option value="EM">Manager</option>
      </select>
    </div>
    <div class="mx-2 mt-2">
      <button type="button" class="btn btn-success px-4 btn-adduser" @click="addUser"><i
          class="bi bi-xs bi-person-plus-fill"></i>&nbsp; Add User</button>
    </div>
  </div>
  <user-table :users="filteredUsers" @block="blockUser" @edit="editUser"
    @delete="clickToDeleteUser"></user-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filter {
  margin-top: 0.35rem;
}

.btn-adduser {
  margin-top: 1.85rem;
}
</style>

