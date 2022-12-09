<script setup>
import { ref, watch, computed, inject } from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useProductsStore } from "../../stores/products.js"

import ProductDetail from "./ProductDetail.vue"

const router = useRouter()
const axios = inject('axios')
const toast = inject('toast')
const productsStore = useProductsStore()

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const newProduct = () => {
  return {
    id: null,
    name: '',
    type: '',
    photo_url: null,
    price: '',
    description: '',
  }
}

let originalValueStr = ''
const loadProduct = (id) => {
  originalValueStr = ''
  errors.value = null

  if (!id || (id < 0)) {
    product.value = newProduct()
    originalValueStr = dataAsString()

  } else {
    axios.get('products/' + id)
      .then((response) => {
        product.value = response.data.data
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
    productsStore.insertProduct(product.value)

      .then((insertedProduct) => {
        product.value = insertedProduct
        originalValueStr = dataAsString()

        toast.success('Product ' + product.value.name + ' was created successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('Product was not created due to validation errors!')
          errors.value = error.response.data.errors

        } else {
          toast.error('Product was not created due to unknown server error!')
        }
      })

  } else {
    productsStore.updateProduct(product.value)
      .then((updatedProduct) => {
        product.value = updatedProduct
        originalValueStr = dataAsString()

        toast.success('Product ' + product.value.name + ' was updated successfully.')
        router.back()
      })

      .catch((error) => {
        if (error.response.status == 422) {
          toast.error('Product ' + product.value.name + ' was not updated due to validation errors!')
          errors.value = error.response.data.errors

        } else {
          toast.error('Product ' + product.value.name + ' was not updated due to unknown server error!')
        }
      })
  }
}

const cancel = () => {
  originalValueStr = dataAsString()
  router.back()
}

const dataAsString = () => {
  return JSON.stringify(product.value)
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

const product = ref(newProduct())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)

const operation = computed(() => {
  return (!props.id || props.id < 0) ? 'insert' : 'update'
})

watch(
  () => props.id,

  (newValue) => {
    loadProduct(newValue)
  },

  { immediate: true }
)

</script>

<template>
  <confirmation-dialog ref="confirmationLeaveDialog" confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!" @confirmed="leaveConfirmed">
  </confirmation-dialog>

  <product-detail :operationType="operation" :product="product" :errors="errors" @save="save" @cancel="cancel"></product-detail>
</template>
