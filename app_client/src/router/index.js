import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from "../stores/user.js"
import { useOrderItemsStore } from "../stores/order_items.js"

import Products from '../components/products/Products.vue'
import Product from '../components/products/Product.vue'
import Statistics from "../components/Statistics.vue"
import Register from "../components/auth/Register.vue"
import Login from "../components/auth/Login.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import Users from "../components/users/Users.vue"
import User from "../components/users/User.vue"
import ComposeOrder from "../components/orders/ComposeOrder.vue"
import Orders from "../components/orders/Orders.vue"
import Order from "../components/orders/Order.vue"
import RouteRedirector from "../components/global/RouteRedirector.vue"

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Products',
      component: Products
    },
    {
      path: '/products/:id',
      name: 'Product',
      component: Product,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/products/new',
      name: 'NewProduct',
      component: Product,
      props: { id: -1 }
    },
    {
      path: '/redirect/:redirectTo',
      name: 'Redirect',
      component: RouteRedirector,
      props: route => ({ redirectTo: route.params.redirectTo })
    },
    {
      path: '/register',
      name: 'Register',
      component: Register
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/password',
      name: 'ChangePassword',
      component: ChangePassword
    },
    {
      path: '/statistics',
      name: 'Statistics',
      component: Statistics
    },
    {
      path: '/order',
      name: 'ComposeOrder',
      component: ComposeOrder,
    },
    {
      path: '/orders',
      name: 'Orders',
      component: Orders,
    },
    {
      path: '/orders/:id',
      name: 'Order',
      component: Order,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/users',
      name: 'Users',
      component: Users,
    },
    {
      path: '/users/new',
      name: 'NewUser',
      component: User,
      props: { id: -1 }
    },
    {
      path: '/users/:id',
      name: 'User',
      component: User,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    }
  ]
})

let handlingFirstRoute = true

router.beforeEach((to, from, next) => {
  if (handlingFirstRoute) {
    handlingFirstRoute = false

    next({ name: 'Redirect', params: { redirectTo: to.fullPath } })
    return

  } else if (to.name == 'Redirect') {
    next()
    return
  }

  const userStore = useUserStore()
  const orderItemsStore = useOrderItemsStore()

  if (userStore.user && (to.name == 'Register' || to.name == 'Login')) {
    next({ name: 'Products' })
    return
  }

  if (!userStore.user && (to.name == 'Orders' || to.name == 'ComposeOrder' && orderItemsStore.totalProducts != 0 || to.name == 'Register' || to.name == 'Login' || to.name == 'Products')) {
    next()
    return
  }

  if (!userStore.user) {
    next({ name: 'Products' })
    return
  }

  if (to.name == 'Users') {
    if (userStore.user.type == 'EM') {
      next()
      return
    }

    next({ name: 'Products' })
    return
  }

  if (to.name == 'Statistics') {
    if (userStore.user.type != 'EM') {
      next({ name: 'Products' })
      return
    }
  }

  if (to.name == 'User') {
    if ((userStore.user.type == 'EM') || (userStore.user.id == to.params.id)) {
      next()
      return
    }

    next({ name: 'Products' })
    return
  }

  if (to.name == 'ComposeOrder') {
    if (orderItemsStore.totalProducts == 0) {
      if (userStore.user && userStore.user != 'C') {
        next({ name: 'Products' })
        return
      }
    }
  }

  if (to.name != 'Products' && to.name != 'Register' && to.name != 'Login' && to.name != 'ChangePassword' && to.name != 'Orders' && to.name != 'ComposeOrder'
    && to.name != 'Order' && to.name != 'Users' && to.name != 'User' && to.name != 'Statistics' && to.name != 'Products' && to.name != 'Product' && to.name != 'NewProduct' && to.name != 'Products') {
    next({ name: 'Products' })
    return
  }

  next()
})

export default router
