<script setup>
import { inject } from "vue"
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user.js"

const serverBaseUrl = inject("serverBaseUrl")
const userStore = useUserStore()

const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: false,
  },
  showType: {
    type: Boolean,
    default: true,
  },
  showDescription: {
    type: Boolean,
    default: true,
  },
  showPhoto: {
    type: Boolean,
    default: true,
  },
  showPrice: {
    type: Boolean,
    default: true,
  },
  showAddButton: {
    type: Boolean,
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: true,
  }
})

const photoFullUrl = (product) => {
  return product.photo_url ? serverBaseUrl + "/storage/products/" + product.photo_url : avatarNoneUrl
}

const emit = defineEmits(['add', 'edit', 'delete'])

const addClick = (product) => {
  emit('add', product)
}

const editClick = (product) => {
  emit('edit', product)
}

const deleteClick = (product) => {
  emit('delete', product)
}
</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showPhoto" class="align-middle">Photo</th>
        <th class="align-middle">Name</th>
        <th v-if="showDescription" class="align-middle">Description</th>
        <th v-if="showPrice" class="align-middle">Price</th>
        <th v-if="(userStore.user?.type == 'C' || !userStore.user || userStore.user?.type == 'EM')"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="product in products" :key="product.id">
        <td v-if="showId" class="align-middle">{{ product.id }}</td>
        <td v-if="showPhoto" class="align-middle">
          <img :src="photoFullUrl(product)" class="rounded-circle img_photo" />
        </td>
        <td class="align-middle">{{ product.name }}</td>
        <td v-if="showDescription" class="align-middle">{{ product.description }}</td>
        <td class="align-middle">{{ product.price }} €</td>
        <td class="text-end align-middle">
          <div class="d-flex justify-content-end">
            <button v-if="userStore.user?.type == 'C' || !userStore.user" class="btn btn-xs btn-primary text-light"
              @click="addClick(product)"><i class="bi bi-xs bi-cart-plus-fill"></i>
            </button>
            <button v-if="userStore.user?.type == 'EM'" class="btn btn-xs btn-primary text-light"
              @click="editClick(product)">
              <i class="bi bi-xs bi-pencil-fill"></i>
            </button>
            <button v-if="userStore.user?.type == 'EM'" class="btn btn-xs btn-danger text-light"
              @click="deleteClick(product)"><i class="bi bi-xs bi-trash3-fill"></i>
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
