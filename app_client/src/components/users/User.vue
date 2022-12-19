<script setup>
import { ref, computed, watch, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUsersStore } from "../../stores/users.js"

import UserDetail from "./UserDetail.vue"

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')

const usersStore = useUsersStore()

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const newUser = () => {
  return {
    id: null,
    name: '',
    email: '',
    type: 'ED',
    photo_url: null,
    password: '',
    password_confirmation: '',
  }
}

let originalValueStr = ''
const loadUser = (id) => {
  originalValueStr = ''
  errors.value = null

  if (!id || (id < 0)) {
    user.value = newUser()
    originalValueStr = dataAsString()

  } else {
    axios.get('users/' + id)
      .then((response) => {
        user.value = response.data.data
        originalValueStr = dataAsString()
      })

      .catch((error) => {
        console.log(error)
      })
  }
}

const save = () => {
  errors.value = null

  if (operation.value == 'insert') {
    usersStore.insertUser(user.value)

      .then((insertedUser) => {
        user.value = insertedUser
        originalValueStr = dataAsString()

        toast.success('User ' + user.value.name + ' was created successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('User was not created due to validation errors!')
          errors.value = error.response.data.errors

        } else {
          toast.error('User was not created due to unknown server error!')
        }
      })

  } else {
    usersStore.updateUser(user.value)
      .then((updatedUser) => {
        user.value = updatedUser
        originalValueStr = dataAsString()

        toast.success('User ' + user.value.name + ' was updated successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('User ' + user.value.name + ' was not updated due to validation errors!')
          errors.value = error.response.data.errors

        } else {
          toast.error('User ' + user.value.name + ' was not updated due to unknown server error!')

        }
      })
  }
}

const cancel = () => {
  originalValueStr = dataAsString()
  router.back()
}

const dataAsString = () => {
  return JSON.stringify(user.value)
}

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = dataAsString()

  if (originalValueStr != newValueStr) {
    nextCallBack = next
    confirmationLeaveDialog.value.show()

  } else {
    next()
  }
})

const user = ref(newUser())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)

const operation = computed(() => {
  return (!props.id || props.id < 0) ? 'insert' : 'update'
})

watch(
  () => props.id,

  (newValue) => {
    loadUser(newValue)
  },

  { immediate: true }
)

</script>

<template>
  <confirmation-dialog ref="confirmationLeaveDialog" confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!" @confirmed="leaveConfirmed">
  </confirmation-dialog>

  <user-detail :operationType="operation" :user="user" :errors="errors" @save="save" @cancel="cancel"></user-detail>
</template>
