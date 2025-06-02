<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 backdrop-blur-sm"
    @click.self="close">
    <div
      class="bg-gray-900 rounded-2xl max-w-3xl w-full p-8 relative shadow-xl overflow-y-auto max-h-[80vh] scroll-smooth text-gray-200 scrollbar-thin scrollbar-thumb-indigo-600 scrollbar-track-gray-700">

      <button @click="close"
        class="absolute top-4 right-4 text-gray-400 hover:text-gray-100 transition-colors duration-200 text-3xl font-extrabold leading-none focus:outline-none"
        aria-label="Fechar modal">
        &times;
      </button>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg class="animate-spin h-10 w-10 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
      </div>

      <div v-else>
        <h2 class="text-4xl font-extrabold mb-2 tracking-tight">
          {{ movieDetails?.details.title }}
        </h2>
        <p class="italic text-gray-400 mb-4 text-lg" v-if="movieDetails?.details.tagline">
          "{{ movieDetails.details.tagline }}"
        </p>
        <p class="text-sm text-gray-500 mb-6">
          {{ formatDate(movieDetails?.details.release_date) }}
        </p>

        <img v-if="movieDetails?.details.backdrop_path || movieDetails?.details.poster_path"
          :src="`https://image.tmdb.org/t/p/w780${movieDetails.details.backdrop_path || movieDetails.details.poster_path}`"
          alt="Imagem do filme" class="rounded-3xl w-full mb-8 shadow-lg object-cover max-h-96 mx-auto" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 text-gray-300">
          <div>
            <p class="mb-2">
              <strong>Gêneros:</strong>
              <span v-for="(genre, i) in movieDetails?.details.genres || []" :key="genre.id">
                {{ genre.name }}<span v-if="i < (movieDetails.details.genres.length - 1)">, </span>
              </span>
            </p>
            <p class="mb-2"><strong>Duração:</strong> {{ formatRuntime(movieDetails?.details.runtime) }}</p>
            <p class="mb-2"><strong>País(es):</strong> {{(movieDetails?.details.production_countries || []).map(c =>
              c.name).join(', ') }}</p>
            <p class="mb-2"><strong>Idiomas:</strong> {{(movieDetails?.details.spoken_languages || []).map(l =>
              l.name).join(', ') }}</p>
          </div>
          <div>
            <p class="mb-2">
              <strong>Nota:</strong>
              <span class="text-indigo-400 font-semibold">{{ movieDetails?.details.vote_average }}</span> / 10
              ({{ movieDetails?.details.vote_count }} avaliações)
            </p>
            <p class="mb-2" v-if="movieDetails?.details.budget"><strong>Orçamento:</strong> {{
              formatCurrency(movieDetails.details.budget) }}</p>
            <p class="mb-2" v-if="movieDetails?.details.revenue"><strong>Bilheteria:</strong> {{
              formatCurrency(movieDetails.details.revenue) }}</p>
          </div>
        </div>

        <p class="mb-8 leading-relaxed text-justify text-gray-300">
          {{ movieDetails?.details.overview || 'Sem descrição disponível.' }}
        </p>

        <a v-if="movieDetails?.details.imdb_id" :href="`https://www.imdb.com/title/${movieDetails.details.imdb_id}`"
          target="_blank" rel="noopener noreferrer"
          class="inline-block mb-8 px-5 py-3 bg-indigo-600 text-white rounded-xl font-semibold shadow-md hover:bg-indigo-700 transition-colors duration-200">
          Ver no IMDb
        </a>

        <div v-if="trailer" class="mb-10">
          <h3 class="text-2xl font-bold mb-4">Trailer</h3>
          <iframe :src="`https://www.youtube.com/embed/${trailer.key}`" frameborder="0" allowfullscreen
            class="w-full aspect-video rounded-xl shadow-lg"></iframe>
        </div>

        <div v-if="movieDetails?.credits.cast.length">
          <h3 class="text-2xl font-bold mb-6">Elenco Principal</h3>
          <div class="flex gap-4 overflow-x-auto scrollbar-thin scrollbar-thumb-indigo-600 scrollbar-track-gray-800">
            <div v-for="actor in movieDetails.credits.cast.slice(0, 6)" :key="actor.id"
              class="flex-shrink-0 w-28 text-center">
              <img v-if="actor.profile_path" :src="`https://image.tmdb.org/t/p/w185${actor.profile_path}`"
                :alt="`Foto de ${actor.name}`"
                class="rounded-xl mb-2 shadow-md hover:scale-105 transition-transform duration-300" />
              <div v-else
                class="w-28 h-40 flex items-center justify-center rounded-xl mb-2 bg-gray-700 text-gray-400 text-xs shadow-md">
                Sem imagem
              </div>
              <p class="text-sm font-semibold">{{ actor.name }}</p>
              <p class="text-xs italic text-gray-400">({{ actor.character }})</p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
import axios from '@/lib/axios'

const props = defineProps({
  show: Boolean,
  tmdbId: Number
})

const emit = defineEmits(['close'])

const movieDetails = ref(null)
const loading = ref(false)
const trailer = ref(null)

function close() {
  emit('close')
}

watch(
  () => props.show,
  async (newVal) => {
    if (newVal && props.tmdbId) {
      await fetchMovieDetails()
    }
    if (!newVal) {
      movieDetails.value = null
      trailer.value = null
    }
  }
)

async function fetchMovieDetails() {
  loading.value = true
  try {
    const response = await axios.get(`/movies/${props.tmdbId}/detail`)
    movieDetails.value = response.data.data

    const trailers = movieDetails.value.videos.filter(
      (v) => v.type === 'Trailer' && v.site === 'YouTube'
    )
    trailer.value = trailers.length ? trailers[0] : null
  } catch (error) {
    console.error('Erro ao buscar detalhes do filme:', error)
  } finally {
    loading.value = false
  }
}

function formatRuntime(minutes) {
  if (!minutes) return 'N/A'
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return `${h}h ${m}min`
}

function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}

function formatCurrency(value) {
  if (!value) return 'N/A'
  return value.toLocaleString('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 })
}
</script>
