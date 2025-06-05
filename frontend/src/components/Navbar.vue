<template>
  <nav class="bg-gray-900 border-b border-gray-700 w-full">
    <div class="w-full flex flex-wrap items-center justify-between p-4">
      <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="@/assets/tmdb.png" class="h-8 w-8" alt="Logo" />
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-100">TMDB</span>
      </a>
      <button @click="toggleMenu" type="button"
        class="inline-flex items-center justify-center p-2 w-10 h-10 text-sm text-gray-400 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600"
        aria-controls="navbar-hamburger" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 1h15M1 7h15M1 13h15" />
        </svg>
      </button>
      <div :class="[isOpen ? 'block' : 'hidden', 'w-full']" id="navbar-hamburger">
        <ul class="flex flex-col font-medium mt-4 rounded-lg bg-gray-900 border border-gray-700">
          <li>
            <router-link to="/search-movie" class="block py-2 px-3 text-gray-100 rounded-sm hover:bg-gray-700">
              Home
            </router-link>
          </li>

          <li>
            <router-link to="/favorites" class="block py-2 px-3 text-gray-100 rounded-sm hover:bg-gray-700">
              Favoritos
            </router-link>
          </li>
          <li>
            <button @click="logout"
              class="block w-full text-left px-4 py-2 text-red-500 hover:bg-red-800 hover:text-white rounded-sm">
              Sair
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router'
import { UserProvider } from '@/providers/UserProvider'
import { toast } from 'vue3-toastify'
import { ref } from 'vue'

const router = useRouter()
const route = useRoute()
const isOpen = ref(false)

const logout = () => {
  UserProvider.logout()
  toast.success('Você saiu com sucesso.')
  router.push('/login')
}

const toggleMenu = () => {
  isOpen.value = !isOpen.value
}
</script>
