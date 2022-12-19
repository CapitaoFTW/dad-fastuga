<script setup>
import { inject } from "vue"
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user.js"

const serverBaseUrl = inject("serverBaseUrl")
const userStore = useUserStore()

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: true,
  },
  showEmail: {
    type: Boolean,
    default: true,
  },
  showType: {
    type: Boolean,
    default: true,
  },
  showPhoto: {
    type: Boolean,
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showToggleButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: true,
  }
})

const emit = defineEmits(['edit', 'toggle', 'delete'])

const photoFullUrl = (user) => {
  return user.photo_url ? serverBaseUrl + "/storage/fotos/" + user.photo_url : avatarNoneUrl
}

const editClick = (user) => {
  emit("edit", user)
}

const toggleClick = (user) => {
  emit('toggle', user)
}

const deleteClick = (user) => {
  emit('delete', user)
}

const canViewUserDetail = (userId) => {
  if (!userStore.user) {
    return false
  }

  return userStore.user.type == 'EM' || userStore.user.id == userId
}
</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showPhoto" class="align-middle"><i class="bi bi-image"></i></th>
        <th class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle"><i class="bi bi-at"></i></th>
        <th v-if="showEditButton || showDeleteButton || showToggleButton"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.id">
        <td v-if="showId" class="align-middle">{{ user.id }}</td>
        <td v-if="showPhoto" class="align-middle">
          <img :src="photoFullUrl(user)" class="rounded-circle img_photo" />
        </td>
        <td class="align-middle">{{ user.name }}</td>
        <td v-if="showEmail" class="align-middle">{{ user.email }}</td>
        <td class="text-end align-middle" v-if="showEditButton || showDeleteButton || showToggleButton">
          <div class="d-flex justify-content-end" v-if="canViewUserDetail(user.id)">
            <button class="btn btn-xs btn-primary text-light" @click="editClick(user)" v-if="showEditButton">
              <i class="bi bi-xs bi-pencil-fill"></i>
            </button>
            <button class="btn btn-xs" :class="{ 'btn-success' : user.blocked == 0, 'btn-danger' : user.blocked == 1, }" @click="toggleClick(user)" v-if="showToggleButton">
              <i class="bi bi-xs" :class="{ 'bi-unlock-fill': user.blocked == 0, 'bi-lock-fill': user.blocked == 1, }"></i>
            </button>
            <button class="btn btn-xs btn-secondary text-light" @click="deleteClick(user)" v-if="showDeleteButton"><i
                class="bi bi-xs bi-trash3-fill"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}
</style>
