<script setup>
  import { ref, computed, onMounted, inject } from 'vue'
  import {useRouter} from 'vue-router'
  import TaskTable from "./TaskTable.vue"
  import { useUserStore } from "../../stores/user.js";
  import { useOrdersStore } from "../../stores/orders.js"
  
  const axios = inject('axios')
  const router = useRouter()
  const userStore = useUserStore();
  const ordersStore = useOrdersStore()

  const tasks = ref([])
  const filterByOrderId = ref(-1)
  const filterByCompleted = ref(-1)

  const loadTasks = () => {
    axios.get('users/' + userStore.userId + '/tasks')
      .then((response) => {
        tasks.value = response.data.data
      })
      .catch((error) => {
        console.log(error)
      })
  }
  
  const addTask = () => {
    router.push({ name: 'NewTask'})
  }
  
  const editTask = (task) => {
    router.push({ name: 'Task', params: { id: task.id } })
  }

  const deletedTask = (deletedTask) => {
      let idx = tasks.value.findIndex((t) => t.id === deletedTask.id)
      if (idx >= 0) {
        tasks.value.splice(idx, 1)
      }
  }

  const props = defineProps({
    tasksTitle: {
      type: String,
      default: 'Tasks'
    },
    onlyCurrentTasks: {
      type: Boolean,
      default: false
    }
  })

  const filteredTasks = computed( () => {
    return tasks.value.filter(t =>
        (props.onlyCurrentTasks && !t.completed)
        ||
        (!props.onlyCurrentTasks && (
          (filterByOrderId.value == -1
            || filterByOrderId.value == t.order_id
          ) &&
          (filterByCompleted.value == -1
            || filterByCompleted.value == 0 && !t.completed
            || filterByCompleted.value == 1 && t.completed
          ))))

  })

  const totalTasks = computed( () => {
    return tasks.value.reduce((c, t) =>
        (props.onlyCurrentTasks && !t.completed)
          ||
          (!props.onlyCurrentTasks && (
            (filterByOrderId.value == -1
              || filterByOrderId.value == t.order_id
            ) &&
            (filterByCompleted.value == -1
              || filterByCompleted.value == 0 && !t.completed
              || filterByCompleted.value == 1 && t.completed
            ))) ? c + 1 : c, 0)
    
  })

  
  onMounted (() => {
    loadTasks()
  })
</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">{{ tasksTitle }}</h3>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total: {{ totalTasks }}</h5>
    </div>
  </div>
  <hr>
  <div
    v-if="!onlyCurrentTasks"
    class="mb-3 d-flex justify-content-between flex-wrap"
  >
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label
        for="selectCompleted"
        class="form-label"
      >Filter by completed:</label>
      <select
        class="form-select"
        id="selectCompleted"
        v-model="filterByCompleted"
      >
        <option value="-1">Any</option>
        <option value="0">Pending Tasks</option>
        <option value="1">Completed Tasks</option>
      </select>
    </div>
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label
        for="selectOrder"
        class="form-label"
      >Filter by order:</label>
      <select
        class="form-select"
        id="selectOrder"
        v-model="filterByOrderId"
      >
        <option value="-1">Any</option>
        <option :value="null">-- No order --</option>
        <option v-for="prj in ordersStore.orders" :key="prj.id" :value="prj.id">
          {{prj.name}}
        </option>
      </select>
    </div>
    <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addtask"
        @click="addTask"
      ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Task</button>
    </div>
  </div>
  <task-table
    :tasks="filteredTasks"
    :showId="true"
    :showOwner="false"
    @edit="editTask"
    @deleted="deletedTask"
  ></task-table>
</template>


<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addtask {
  margin-top: 1.85rem;
}
</style>
