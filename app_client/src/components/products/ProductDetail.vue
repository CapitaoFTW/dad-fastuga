<script setup>
import { ref, watch, computed, inject } from "vue";
import productNoneUrl from '@/assets/product-none.png'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    required: false
  },
  operationType: {
    type: String,
    default: 'update'  // insert / update
  }
})

const emit = defineEmits(["save", "cancel"]);

const editingProduct = ref(props.product)

watch(
  () => props.product,

  (newProduct) => {
    editingProduct.value = newProduct
  },

  { immediate: true }
)

const productTitle = computed(() => {
  if (!editingProduct.value) {
    return ''
  }

  return props.operationType == 'insert' ? 'New Product' : ('Product #' + editingProduct.value.id)               
})

const photoFullUrl = computed(() => {
  return editingProduct.value.photo_url ? serverBaseUrl + "/storage/products/" + editingProduct.value.photo_url : productNoneUrl
})

const convertBase64 = (file) => {
  return new Promise((resolve, reject) => {
    const fileReader = new FileReader()
    fileReader.readAsDataURL(file)

    fileReader.onload = () => {
      resolve(fileReader.result)
    }

    fileReader.onerror = (error) => {
      reject(error)
    }
  })
}

const uploadImage = async (e) => {
  const image = e.target.files[0]
  const base64 = await convertBase64(image)
  editingProduct.value.photo = base64
}

const save = () => {
  emit("save", editingProduct.value);
}

const cancel = () => {
  emit("cancel", editingProduct.value);
}
</script>

<template>
  <form class="row pt-3 pb-2 mb-3 needs-validation justify-content-center" novalidate @submit.prevent="save">
    <h3 class="mb-3">{{ productTitle }}</h3>
    <hr>
    <div class="w-75">
      <div class="text-center pt-3">
        <img :src="photoFullUrl" class="img-fluid rounded-circle opacity-25"/>
        <br />
        <input type="file" id="inputPhoto" class="inputPhoto text-primary" @change="uploadImage" />
        <label v-if="operationType == 'update'" class="text-primary" for="inputPhoto">Alterar Foto</label>
        <label v-if="operationType == 'insert'" class="text-primary" for="inputPhoto">Escolher Foto</label>
        <field-error-message :errors="errors" fieldName="photo"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputName" class="form-label">Name</label>
        <input type="text" class="form-control" id="inputName" placeholder="Name" v-model="editingProduct.name" />
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputPrice" class="form-label">Price (€)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="inputPrice" placeholder="Price" v-model="editingProduct.price"/>
        <field-error-message :errors="errors" fieldName="price"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputType" class="form-label">Type</label>
        <select class="form-select" :class="{ 'text-secondary' : editingProduct.type == '', '' : editingProduct.type != '' }" id="inputType" v-model="editingProduct.type" required>
          <option value="" disabled class="">Choose a Type</option>
          <option value="cold dish">Cold Dish</option>
          <option value="hot dish">Hot Dish</option>
          <option value="drink">Drink</option>
          <option value="dessert">Dessert</option>
        </select>
        <field-error-message :errors="errors" fieldName="type"></field-error-message>
      </div>
      <div class="mb-3">
        <label for="inputDescription" class="form-label">Description</label>
        <textarea class="form-control" id="inputDescription" placeholder="Description"
          v-model="editingProduct.description" required rows="4"/>
        <field-error-message :errors="errors" fieldName="description"></field-error-message>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.inputPhoto {
  width: 0.1px;
  height: 0.1px;
  opacity: 0;
  overflow: hidden;
  position: absolute;
  z-index: -1;
}

.inputPhoto+label {
  cursor: pointer;
  /* "hand" cursor */
  font-weight: bold;
}

.img-fluid {
  width: 115px;
  height: 115px;
}
</style>
