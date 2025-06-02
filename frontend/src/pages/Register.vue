<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-sky-900 px-4">
    <div class="max-w-md w-full bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-2xl transition-shadow duration-300">

      <img class="w-32 mx-auto mb-6" src="../assets/tmdb.png" alt="Logo TMDB" />

      <h2 class="text-3xl font-mono text-center text-white mb-8">Criar uma conta</h2>

      <form @submit.prevent="handleSubmit" class="space-y-6">

        <BaseInput id="name" label="Nome Completo" v-model="name" placeholder="Seu nome" required />
        <BaseInput id="email" label="Email" type="email" v-model="email" placeholder="seuemail@exemplo.com" required />
        <BaseInput id="password" label="Senha" type="password" v-model="password" placeholder="••••••••" required />
        <BaseInput id="password_confirmation" label="Confirmar Senha" type="password" v-model="passwordConfirmation"
          placeholder="••••••••" required />

        <div v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</div>

        <BaseButton type="submit" :disabled="loading" variant="primary">
          <span v-if="loading"
            class="animate-spin inline-block mr-2 border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
          Cadastrar
        </BaseButton>

        <BaseButton type="button" variant="secondary" @click="router.push('/login')">Voltar para Login</BaseButton>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { UserProvider } from '@/providers/UserProvider'
import { toast } from 'vue3-toastify'

import BaseInput from '@/components/BaseInput.vue'
import BaseButton from '@/components/BaseButton.vue'

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const errorMessage = ref('')

const router = useRouter()

const handleSubmit = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    await UserProvider.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    toast.success('Cadastro realizado com sucesso!')
    router.push('/login')

  } catch (error) {
    const msg = error.response?.data?.message || 'Erro ao cadastrar'
    toast.error(msg)
    errorMessage.value = msg

  } finally {
    loading.value = false
  }
}
</script>
