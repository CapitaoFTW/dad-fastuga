<script setup>
import { ref, watch, computed, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  user: {
    type: Object,
    required: true
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

const editingUser = ref(props.user)

watch(
  () => props.user,

  (newUser) => {
    editingUser.value = newUser
  }
)

const userTitle = computed(() => {
  if (!editingUser.value) {
    return ''
  }

  return props.operationType == 'insert' ? 'New User' : 'Profile'
})

const photoFullUrl = computed(() => {
  return editingUser.value.photo_url ? serverBaseUrl + "/storage/fotos/" + editingUser.value.photo_url : avatarNoneUrl
})

const uploadFile = (e) => {
  editingUser.value.photo_url = e.target.files[0]
}

const save = () => {
  emit("save", editingUser.value);
}

const cancel = () => {
  emit("cancel", editingUser.value);
}
</script>

<template>
  <form class="row pt-3 pb-2 mb-3 needs-validation justify-content-center" novalidate @submit.prevent="save">
    <h3 class="mb-3">{{ userTitle }}</h3>
    <hr>
    <div class="w-75">
      <div class="text-center pt-3">
        <img :src="photoFullUrl" class="img-fluid rounded-circle" />
        <br />
        <input type="file" id="inputPhoto" class="inputPhoto text-primary"
          @change="uploadFile" />
        <label class="text-primary" for="inputPhoto">Alterar Foto</label>
        <field-error-message :errors="errors" fieldName="photo_url"></field-error-message>
      </div>
      <div class="mb-2">
        <label for="inputName" class="form-label">Name</label>
        <input type="text" class="form-control" id="inputName" placeholder="Name" v-model="editingUser.name" />
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
      </div>
      <div class="mb-2">
        <label for="inputEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="inputEmail" placeholder="Email" v-model="editingUser.email"
          required />
        <field-error-message :errors="errors" fieldName="email"></field-error-message>
      </div>
      <div v-if="editingUser.type == 'C'" class="mb-2">
        <label for="inputPhone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="inputPhone" placeholder="Phone Number"
          v-model="editingUser.customer.phone" required />
        <field-error-message :errors="errors" fieldName="phone"></field-error-message>
      </div>
      <div v-if="editingUser.type == 'C'" class="mb-2">
        <label for="inputNIF" class="form-label">NIF</label>
        <input type="text" class="form-control" id="inputNIF" placeholder="NIF" v-model="editingUser.customer.nif"
          required />
        <field-error-message :errors="errors" fieldName="nif"></field-error-message>
      </div>
      <div v-if="editingUser.type == 'C'" class="mb-2">
        <label for="inputPaymentType" class="form-label">Payment Type</label>
        <select class="form-select" id="inputPaymentType" v-model="editingUser.customer.default_payment_type" required>
          <option :value="null" disabled>Choose an option</option>
          <option value="VISA">VISA</option>
          <option value="MBWAY">MBWAY</option>
          <option value="PAYPAL">PAYPAL</option>
        </select>
        <field-error-message :errors="errors" fieldName="default_payment_type"></field-error-message>
      </div>
      <div v-if="editingUser.type == 'C'" class="mb-2">
        <label for="inputPaymentRef" class="form-label">Payment Reference</label>
        <input type="text" class="form-control" id="inputPaymentRef" placeholder="Payment Reference"
          v-model="editingUser.customer.default_payment_reference" disabled />
        <field-error-message :errors="errors" fieldName="default_payment_reference"></field-error-message>
      </div>
    </div>
    <div class="mt-3 mb-5 d-flex justify-content-center">
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
</style>
