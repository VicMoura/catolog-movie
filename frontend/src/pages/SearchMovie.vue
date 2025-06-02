<template>
  <div class="min-h-screen bg-gray-900 text-gray-300">
    <Navbar class="w-full bg-gray-800 shadow-md" />

    <div class="max-w-full mx-auto px-4 py-8">
      <h1 class="text-3xl font-mono text-center mb-8">
        BUSCAR FILMES
      </h1>

      <form @submit.prevent="handleSearch" class="flex gap-4 mb-8 w-full max-w-full">
        <input type="text" v-model="query" placeholder="Digite o nome do filme..." class="flex-1 px-4 py-3 rounded-md bg-gray-800 border border-gray-700
                 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
                 text-gray-300 placeholder-gray-500" />
        <button type="submit" :disabled="loading" class="bg-sky-600 hover:bg-sky-700 disabled:bg-sky-400
                 text-white font-semibold px-6 rounded-md transition duration-200">
          <span v-if="loading"
            class="animate-spin inline-block mr-2 border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
          Buscar
        </button>
      </form>

      <MovieGrid :movies="movies" :loading="loading" loading-message="Buscando filmes..."
        empty-message="Nenhum filme encontrado. Tente outra busca."
        :is-favorite="(movie) => userStore.isFavorite(movie.tmdb_id)" @open-modal="openModal"
        @toggle-favorite="toggleFavorite" />


      <MovieModal :show="showModal" :tmdbId="selectedMovie?.tmdb_id" @close="closeModal" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import MovieGrid from '@/components/MovieGrid.vue'
import MovieModal from '@/components/MovieModal.vue'
import { TmdbProvider } from '@/providers/TmdbProvider'
import { toast } from 'vue3-toastify'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()

const query = ref('')
const movies = ref([])
const loading = ref(false)

const selectedMovie = ref(null)
const showModal = ref(false)

const handleSearch = async () => {
  try {
    loading.value = true
    const result = await TmdbProvider.searchMovies(query.value)
    movies.value = result
  } catch (error) {
    toast.error('Erro ao buscar filmes')
  } finally {
    loading.value = false
  }
}

function openModal(movie) {
  selectedMovie.value = movie
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedMovie.value = null
}

async function toggleFavorite(movie) {

  const toastId = toast.info('Atualizando favorito...', { autoClose: false });

  try {
    await userStore.toggleFavorite(movie);

    toast.update(toastId, {
      render: userStore.isFavorite(movie.tmdb_id) ? 'Adicionado aos favoritos' : 'Removido dos favoritos',
      type: 'success',
      autoClose: 3000,
      isLoading: false,
    });
  } catch (erro) {
    console.log(erro);
    toast.update(toastId, {
      render: erro.response?.data?.message || 'Erro ao atualizar favorito',
      type: 'error',
      autoClose: 3000,
      isLoading: false,
    });
  }
}


onMounted(() => {
  userStore.initialize()
})
</script>
