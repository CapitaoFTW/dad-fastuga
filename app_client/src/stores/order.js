import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"

export const useOrderStore = defineStore('order', () => {

    const axios = inject('axios')

    return {  }
})
