<template>
    <div>
        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <svg class="animate-spin h-10 w-10 text-sky-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p class="text-lg font-mono">{{ loadingMessage }}</p>
        </div>

        <div v-else>
            <div v-if="movies.length === 0" class="text-center text-gray-500">
                {{ emptyMessage }}
            </div>

            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <MovieCard v-for="movie in movies" :key="movie.id || movie.tmdb_id" :movie="movie"
                    :is-favorite="isFavorite(movie)" @click="onOpenModal(movie)"
                    @toggle-favorite="onToggleFavorite(movie)" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { watch } from 'vue'
import MovieCard from '@/components/MovieCard.vue'

const props = defineProps({
    movies: Array,
    loading: Boolean,
    loadingMessage: {
        type: String,
        default: 'Carregando...'
    },
    emptyMessage: {
        type: String,
        default: 'Nenhum filme encontrado.'
    },
    isFavorite: {
        type: Function,
        default: () => () => false
    }
})

const emit = defineEmits(['open-modal', 'toggle-favorite'])

const onOpenModal = (movie) => emit('open-modal', movie)
const onToggleFavorite = (movie) => emit('toggle-favorite', movie)
</script>
