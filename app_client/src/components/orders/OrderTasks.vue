<script setup>
import { ref, watch, computed, inject } from "vue";
import { useRouter } from "vue-router";

import TaskTable from "../tasks/TaskTable.vue";

const router = useRouter();

const axios = inject("axios");

const props = defineProps({
  id: {
    type: Number,
    default: null,
  },
});

const emptyOrder = () => {
  return {
    id: null,
    name: "",
    status_name: "",
    tasks: [],
  };
};

const loadOrder = (id) => {
  if (!id || id < 0) {
    order.value = emptyOrder.value();
  } else {
    axios
      .get("orders/" + id + "/tasks")
      .then((response) => {
        order.value = response.data.data;
      })
      .catch((error) => {
        console.log(error);
      });
  }
};

const editOrder = () => {
  router.push({ name: "Order", params: { id: props.id } });
};

const addTask = () => {
  router.push({ name: "NewTaskOfOrder", params: { id: props.id } });
};

const editTask = (task) => {
  router.push({ name: "Task", params: { id: task.id } });
};

const deletedTask = (deletedTask) => {
  let idx = order.value.tasks.findIndex((t) => t.id === deletedTask.id);
  if (idx >= 0) {
    order.value.tasks.splice(idx, 1);
  }
};

const order = ref(emptyOrder());

const filterByCompleted = ref(0);

watch(
  () => props.id,
  (newValue) => {
    loadOrder(newValue);
  },
  { immediate: true }
);

const filteredTasks = computed(() => {
  return order.value.tasks.filter(
    (t) =>
      filterByCompleted.value == -1 ||
      (filterByCompleted.value == 0 && !t.completed) ||
      (filterByCompleted.value == 1 && t.completed)
  );
});

const totalTasks = computed(() => {
  return order.value.tasks.reduce(
    (c, t) =>
      filterByCompleted.value == -1 ||
      (filterByCompleted.value == 0 && !t.completed) ||
      (filterByCompleted.value == 1 && t.completed)
        ? c + 1
        : c,
    0
  );
});
</script>

<template>
  <div class="mx-2">
    <h3 class="mt-4">Order # {{ order.id }} : {{ order.name }}</h3>
  </div>
  <hr />
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h5 class="mt-4">Order status: {{ order.status_name }}</h5>
    </div>
    <div class="mx-2 total-filtro">
      <h5 class="mt-4">Total tasks: {{ totalTasks }}</h5>
    </div>
  </div>
  <div class="mb-4 d-flex flex-wrap justify-content-between">
    <div class="mx-2 mt-2 flex-grow-1">
      <label for="selectCompleted" class="form-label">Filter by completed:</label>
      <select class="form-select" id="selectCompleted" v-model="filterByCompleted">
        <option value="-1">Any</option>
        <option value="0">Pending Tasks</option>
        <option value="1">Completed Tasks</option>
      </select>
    </div>
    <div class="mx-2 mt-2">
      <button type="button" class="btn btn-secondary px-4 btn-top" @click="editOrder">
        <i class="bi bi-xs bi-pencil"></i>&nbsp; Edit Order
      </button>
    </div>
    <div class="mx-2 mt-2">
      <button type="button" class="btn btn-success px-4 btn-top" @click="addTask">
        <i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Task
      </button>
    </div>
  </div>

  <task-table
    :tasks="filteredTasks"
    :showId="true"
    :showOwner="true"
    :showOrder="false"
    @edit="editTask"
    @deleted="deletedTask"
  ></task-table>
</template>

<style scoped>
.btn-top {
  margin-top: 1.85rem;
}
</style>
