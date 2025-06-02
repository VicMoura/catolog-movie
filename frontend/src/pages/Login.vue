<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-slate-800 px-4">
    <div class="max-w-md w-full bg-slate-900 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-shadow duration-300">

      <img class="w-32 mx-auto mb-6" src="../assets/tmdb.png" alt="Logo TMDB" />

      <h2 class="text-3xl font-mono text-center text-white mb-8">Entrar na sua conta</h2>

      <form @submit.prevent="handleSubmit" class="space-y-6">

        <BaseInput id="email" label="Email" type="email" v-model="email" placeholder="seuemail@exemplo.com" required />
        <BaseInput id="password" label="Senha" type="password" v-model="password" placeholder="••••••••" required />

        <div v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</div>

        <BaseButton type="submit" :disabled="loading" variant="primary">
          <span v-if="loading"
            class="animate-spin inline-block mr-2 border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
          Entrar
        </BaseButton>

        <BaseButton type="button" variant="secondary" @click="router.push('/register')">Registrar</BaseButton>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { toast } from 'vue3-toastify'

import BaseInput from '@/components/BaseInput.vue'
import BaseButton from '@/components/BaseButton.vue'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const router = useRouter()
const userStore = useUserStore()

const handleSubmit = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    await userStore.login({
      email: email.value,
      password: password.value
    })

    toast.success('Login realizado')
    router.push('/search-movie')

  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Erro ao fazer login'
    toast.error(errorMessage.value)

  } finally {
    loading.value = false
  }
}
</script>
