<template>
  <div class="min-h-screen bg-black">
    <Navbar />

    <div class="max-w-auto mx-auto px-4 py-8">
      <h1 class="text-3xl font-mono text-center text-white mb-8">
        Favoritos
      </h1>

      <div class="mb-6 flex justify-end">
        <select v-model="selectedGenre" class="border p-2 rounded">
          <option value="">Todos os gêneros</option>
          <option v-for="genre in userStore.genres" :key="genre.id" :value="genre.id">
            {{ genre.name }}
          </option>
        </select>
      </div>

      <MovieGrid :movies="userStore.favorites" :loading="userStore.loadingFavorites"
        loading-message="Carregando seus favoritos..." empty-message="Você ainda não adicionou nenhum filme favorito."
        :is-favorite="() => true" @open-modal="openModal" @toggle-favorite="toggleFavorite" />

      <MovieModal :show="showModal" :tmdbId="selectedMovie?.tmdb_id" @close="closeModal" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import Navbar from '@/components/Navbar.vue'
import MovieGrid from '@/components/MovieGrid.vue'
import MovieModal from '@/components/MovieModal.vue'
import { toast } from 'vue3-toastify'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()

const selectedGenre = ref('')
const selectedMovie = ref(null)
const showModal = ref(false)

watch(selectedGenre, (newGenre) => {
  userStore.fetchFavorites(newGenre || null)
})

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
