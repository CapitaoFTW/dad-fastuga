<script setup>
import { useRouter, RouterLink, RouterView } from "vue-router"
import { ref, inject } from "vue"
import { useUserStore } from "./stores/user"
import { useOrdersStore } from "./stores/orders"
import { useOrderItemsStore } from "./stores/order_items"

const router = useRouter()
const socket = inject("socket")
const toast = inject("toast")
const userStore = useUserStore()
const orderItemsStore = useOrderItemsStore()
const ordersStore = useOrdersStore()

const buttonSidebarExpand = ref(null)

const logout = async () => {
  if (await userStore.logout()) {
    toast.success("User has logged out of the application.")

    clickMenuOption()
    router.push({ name: 'Login' })

  } else {
    toast.error("There was a problem logging out of the application!")
  }
}

const clickMenuOption = () => {
  if (window.getComputedStyle(buttonSidebarExpand.value).display !== "none") {
    buttonSidebarExpand.value.click()
  }
}

socket.on('newHotDishes', (data) => {
  if (userStore.user?.type == 'EC') {

    if (data.numberHotDishes == 1)
      toast.info(`One Hot Dish has been ordered (Ticket #${data.ticket_number})`)

    if (data.numberHotDishes > 1)
      toast.info(`${data.numberHotDishes} new Hot Dishes have been ordered! (Ticket #${data.ticket_number})`)
  }

  if (userStore.user?.type == 'ED') {
    if (data.numberHotDishes == 0) {
      toast.info(`Order #${data.ticket_number} has all Order Items ready!`)
    }
  }
})

socket.on('preparingOrderItem', (data) => {
  if (userStore.user?.type == 'EC')
    toast.info(`Order Item #${data.ticket_number}-${data.order_item.order_local_number} is now being prepared by ${data.order_item.chef}!`)
})

socket.on('readyOrderItem', (data) => {
  if (userStore.user?.type == 'ED')
    toast.info(`Order Item #${data.ticket_number}-${data.order_item.order_local_number} is now ready by ${data.order_item.chef}!`)
})

socket.on('readyOrder', (order) => {

  if (userStore.customerId && userStore.customerId == order.customer_id)
    toast.info(`Your Order #${order.ticket_number} is now ready for pickup!`)

  else if (!userStore.customerId && !userStore.user)
    toast.info(`The Order #${order.ticket_number} is now ready for pickup!`)

  ordersStore.loadOrders()
})

socket.on('deliveredOrder', (order) => {

  if (userStore.user?.type == 'EC' || userStore.user?.type == 'ED')
    toast.info(`Order #${order.ticket_number} has been delivered to a customer by ${order.deliverer}!`)

  ordersStore.loadOrders()
})

socket.on('cancelledOrder', (data) => {

  if (userStore.user?.type == 'EM')
    toast.info(`Order #${data.order.ticket_number} has been cancelled by ${data.manager}!`)

  if (userStore.user.customer?.id == data.order.customer_id)
    toast.info(`Your Order #${data.order.ticket_number} has been cancelled!`)

  ordersStore.loadOrders()
})

</script>

<template>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
      <router-link class="navbar-brand col-md-3 col-lg-2 me-0 px-3 bg-transparent" :to="{ name: 'Products' }"
        @click="clickMenuOption">
        <img src="@/assets/logo.png" alt="" width="30" height="24" class="d-inline-block align-text-top" />
        &nbsp; FasTuga
      </router-link>
      <button id="buttonSidebarExpandId" ref="buttonSidebarExpand" class="navbar-toggler" type="button"
        data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item"
            v-if="orderItemsStore.totalProducts != 0 && (!userStore.user || userStore.user?.type == 'C')">
            <router-link class="nav-link" :class="{ active: $route.name == 'ComposeOrder' }"
              :to="{ name: 'ComposeOrder' }" @click="clickMenuOption"><i class="bi bi-cart3 m-0"></i><span
                class="rounded-circle align-top badge badge-pill badge-danger"><span class="text-light">{{
                    orderItemsStore.totalProducts
                }}</span></span>
            </router-link>
          </li>
          <li class="nav-item" v-show="!userStore.user">
            <router-link class="nav-link" :class="{ active: $route.name === 'Register' }" :to="{ name: 'Register' }"
              @click="clickMenuOption">
              <i class="bi bi-person-check-fill"></i>
              Register
            </router-link>
          </li>
          <li class="nav-item" v-show="!userStore.user">
            <router-link class="nav-link" :class="{ active: $route.name === 'Login' }" :to="{ name: 'Login' }"
              @click="clickMenuOption">
              <i class="bi bi-box-arrow-in-right"></i>
              Login
            </router-link>
          </li>
          <li class="nav-item dropdown">
            <a v-if="userStore.user" class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img :src="(userStore.userPhotoUrl ?? '@/assets/avatar-none.png')"
                class="rounded-circle z-depth-0 avatar-img" alt="avatar image" />
              <span class="avatar-text">{{ userStore.user.name }}</span>
            </a>
            <a v-else class="nav-link dropdown-toggle arrow" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img src="@/assets/avatar-none.png" class="rounded-circle z-depth-0 avatar-img" alt="avatar image" />
              <span class="avatar-text">Anonymous</span>
            </a>
            <ul v-show="userStore.user" class="dropdown-menu dropdown-menu-dark dropdown-menu-end"
              aria-labelledby="navbarDropdownMenuLink">
              <li>
                <router-link class="dropdown-item"
                  :class="{ active: $route.name == 'User' && $route.params.id == userStore.userId }"
                  :to="{ name: 'User', params: { id: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-person-square"></i><span class="position-absolute"
                    style="top: 1.1rem; right:5.4rem">Profile</span>
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangePassword' }"
                  :to="{ name: 'ChangePassword' }" @click="clickMenuOption">
                  <i class="bi bi-key-fill"></i>
                  Change Password
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li>
                <a class="dropdown-item" href="/" @click.prevent="logout">
                  <i class="bi bi-arrow-right"></i><span class="position-absolute"
                    style="bottom: 1.1rem; left: 3rem">Logout</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: $route.name === 'Products' }" :to="{ name: 'Products' }"
                @click="clickMenuOption">
                <i class="bi bi-clipboard"></i>
                Menu
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: $route.name === 'Orders' }" :to="{ name: 'Orders' }"
                @click="clickMenuOption">
                <i class="bi bi-receipt"></i>
                Orders
              </router-link>
            </li>
            <li class="nav-item" v-show="userStore.user?.type == 'EM'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Users' }" :to="{ name: 'Users' }"
                @click="clickMenuOption">
                <i class="bi bi-people"></i>
                Users
              </router-link>
            </li>
            <li class="nav-item" v-show="userStore.user?.type == 'EM'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Statistics' }"
                :to="{ name: 'Statistics' }" @click="clickMenuOption">
                <i class="bi bi-bar-chart"></i>
                Statistics
              </router-link>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-2 text-muted"
            v-if="!userStore.user && ordersStore.totalMyInProgressOrders != 0 || userStore.user?.type == 'C' && ordersStore.totalMyInProgressOrders != 0">
            <span>My Orders</span>
          </h6>
          <ul class="nav flex-column mb-2">
            <li class="nav-item" v-for="order in ordersStore.myInProgressOrders" :key="order.id">
              <router-link class="nav-link" :class="{ active: $route.name == 'Order' && $route.params.id == order.id }"
                :to="{ name: 'Order', params: { id: order.id } }" @click="clickMenuOption">
                <i class="bi bi-ticket-detailed"></i> Ticket #{{ order.ticket_number }}
              </router-link>
            </li>
          </ul>

          <div class="d-block d-md-none">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              User
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item" v-show="!userStore.user">
                <router-link class="nav-link" :class="{ active: $route.name === 'Register' }" :to="{ name: 'Register' }"
                  @click="clickMenuOption">
                  <i class="bi bi-person-check-fill"></i>
                  Register
                </router-link>
              </li>
              <li class="nav-item" v-show="!userStore.user">
                <router-link class="nav-link" :class="{ active: $route.name === 'Login' }" :to="{ name: 'Login' }"
                  @click="clickMenuOption">
                  <i class="bi bi-box-arrow-in-right"></i>
                  Login
                </router-link>
              </li>
              <li class="nav-item dropdown" v-show="userStore.user">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink2" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false" style="padding-left: 3px">
                  <img :src="userStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatar image" />
                  <span class="avatar-text">{{ userStore.user?.name }}</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                  <li>
                    <router-link class="dropdown-item"
                      :class="{ active: $route.name == 'User' && $route.params.id == userStore.userId }"
                      :to="{ name: 'User', params: { id: userStore.userId } }" @click="clickMenuOption">
                      <i class="bi bi-person-square"></i><span class="position-absolute"
                        style="top: 1.1rem; right:5.4rem">Profile</span>
                    </router-link>
                  </li>
                  <li>
                    <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangePassword' }"
                      :to="{ name: 'ChangePassword' }" @click="clickMenuOption">
                      <i class="bi bi-key-fill"></i>
                      Change Password
                    </router-link>
                  </li>
                  <li>
                    <hr class="dropdown-divider" />
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" @click.prevent="logout">
                      <i class="bi bi-arrow-right"></i><span class="position-absolute"
                        style="bottom: 1.1rem; left: 3rem">Logout</span>
                    </a>
                  </li>
                </ul>
              </li>
              <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-5 mb-1 text-muted"
                v-if="orderItemsStore.totalProducts != 0 && (!userStore.user || userStore.user?.type == 'C')">
                Composing Order
              </h6>
              <li class="nav-item"
                v-if="orderItemsStore.totalProducts != 0 && (!userStore.user || userStore.user?.type == 'C')">
                <router-link class="nav-link cart" :class="{ active: $route.name == 'ComposeOrder' }"
                  :to="{ name: 'ComposeOrder' }" @click="clickMenuOption"><i class="bi bi-cart3 m-0"></i><span
                    class="rounded-circle align-top badge badge-pill badge-danger"><span class="text-light">{{
                        orderItemsStore.totalProducts
                    }}</span></span>
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<style>
@import "./assets/dashboard.css";

span.badge {
  background-color: #dc3545;
  padding: 0.2rem;
}

.avatar-img {
  margin: -1.2rem 0.8rem -2rem 0.8rem;
  width: 3.3rem;
  height: 3.3rem;
}

.avatar-text {
  line-height: 2.2rem;
  margin: 1rem 0.5rem -2rem 0;
  padding-top: 1rem;
}

.dropdown-item {
  font-size: 0.875rem;
}

.btn:focus {
  outline: none;
  box-shadow: none;
}

#sidebarMenu {
  overflow-y: auto;
}

.dropdown-toggle.arrow::after {
  content: none;
}
</style>
