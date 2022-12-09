import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from "../stores/user.js"

import Products from '../components/products/Products.vue'
import Product from '../components/products/Product.vue'
import Dashboard from "../components/Dashboard.vue"
import Register from "../components/auth/Register.vue"
import Login from "../components/auth/Login.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import Users from "../components/users/Users.vue"
import User from "../components/users/User.vue"
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
      props: route => ({ redirectTo: route.params.redirectTo})   
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
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard
    },
    /*{
      path: '/tasks/current',
      name: 'CurrentTasks',
      component: Tasks,
      props: { onlyCurrentTasks: true, tasksTitle: 'Current Tasks' }
    },*/
    {
      path: '/orders',
      name: 'Orders',
      component: Orders,
    },
    {
      path: '/orders/new',
      name: 'NewOrder',
      component: Order,
      props: { id: -1 }
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
    }, 
    /*{
      path: '/orders/:id/tasks',
      name: 'OrderTasks',
      component: OrderTasks,
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/orders/:id/tasks/new',
      name: 'NewTaskOfOrder',
      component: Task,
      props: route => ({ id:-1, fixedOrder:  parseInt(route.params.id) })
    },
    {
      path: '/tasks/new',
      name: 'NewTask',
      component: Task,
      props: { id: -1 }
    },
    {
      path: '/tasks/:id',
      name: 'Task',
      component: Task,
      props: route => ({ id: parseInt(route.params.id) })    
    },*/
    {
      path: '/reports',
      name: 'Reports',
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
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

  if (userStore.user && (to.name == 'Register' || to.name == 'Login')) {
    next({ name: 'Products' })
    return
  }

  if (to.name == 'Register' || to.name == 'Login' || to.name == 'Products') {
    next()
    return
  }

  if (!userStore.user) {
    next({ name: 'Login' })
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

  if (to.name == 'Reports') {
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

  /*if (to.name != 'Dashboard' && to.name != 'Register' && to.name != 'Login' && to.name != 'ChangePassword' && to.name != 'Orders' && to.name != 'NewOrder'
  && to.name != 'Order' && to.name != 'Users' && to.name != 'User' && to.name != 'Reports' && to.name != 'about') {
    next({ name: 'Products' })
    return
  }*/
  
  next()
})

export default router
