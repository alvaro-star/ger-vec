import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/views/Layout/AppLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: AppLayout,
      children: [
        {
          path: '', name: 'home',
          component: () => import('@/views/HomeView.vue'),
        },
        {
          path: 'about', name: 'about',
          component: () => import('@/views/AboutView.vue'),
        },
        {
          path: 'veiculo',
          children: [
            {
              path: '', name: 'veiculos.index',
              component: () => import('@/views/Veiculos/IndexPage/IndexView.vue'),
            },
            {
              path: ':id', name: 'veiculos.show',
              component: () => import('@/views/Veiculos/ShowView.vue'),
            },
            {
              path: ':id/edit', name: 'veiculos.edit',
              component: () => import('@/views/Veiculos/EditView.vue'),
            },
          ]
        },
        {
          path: 'revisao',
          children: [
            {
              path: '', name: 'revisoes.index',
              component: () => import('@/views/Revisoes/IndexView.vue'),
            },
            {
              path: 'create', name: 'revisoes.create',
              component: () => import('@/views/Revisoes/CreateView.vue'),
            },
            {
              path: ':id', name: 'revisoes.show',
              component: () => import('@/views/Revisoes/ShowView.vue'),
            },
            {
              path: ':id/edit', name: 'revisoes.edit',
              component: () => import('@/views/Revisoes/EditView.vue'),
            },
          ]
        },
        {
          path: 'pessoa',
          children: [
            {
              path: '', name: 'pessoas.index',
              component: () => import('@/views/Pessoas/IndexView.vue'),
            },
            {
              path: 'create', name: 'pessoas.create',
              component: () => import('@/views/Pessoas/CreateView.vue'),
            },
            {
              path: ':id', name: 'pessoas.show',
              component: () => import('@/views/Pessoas/ShowView.vue'),
            },
            {
              path: ':id/edit', name: 'pessoas.edit',
              component: () => import('@/views/Pessoas/EditView.vue'),
            },
            {
              path: ':id/veiculos/create', name: 'veiculos.create',
              component: () => import('@/views/Veiculos/CreateView.vue'),
            },
          ]
        }
      ]
    }
  ]
})

export default router
