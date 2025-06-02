<template>
  <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden cursor-pointer flex flex-col
           hover:shadow-lg transition-shadow duration-300" @click="$emit('click')">
    <!-- Bloco da Imagem -->
    <div class="w-full aspect-[2/3] relative bg-gray-700 rounded-t-lg overflow-hidden">
      <template v-if="movie.poster_path || movie.backdrop_path">
        <img :src="`https://image.tmdb.org/t/p/w342${movie.poster_path || movie.backdrop_path}`" alt="Poster"
          class="w-full h-full object-cover" />
      </template>
      <template v-else>
        <div class="absolute inset-0 flex items-center justify-center text-gray-500 font-semibold text-4xl select-none">
          SEM IMAGEM
        </div>
      </template>
    </div>

    <!-- Conteúdo (Título e Data) -->
    <div class="p-4 flex-1">
      <h2 class="text-lg font-semibold mb-2 text-gray-100">{{ movie.title }}</h2>
      <p class="text-sm text-gray-400">
        {{ movie.release_date?.trim() ? movie.release_date : 'Data não informada' }}
      </p>
    </div>

    <!-- Rodapé com o botão de favorito -->
    <div class="px-4 pb-4 flex justify-end">
      <button @click.stop="$emit('toggle-favorite', movie)" :class="[
        'text-xl p-2 rounded-full transition-colors',
        isFavorite ? 'text-red-500 hover:text-red-600' : 'text-gray-500 hover:text-gray-400'
      ]" title="Favorito" aria-label="Favoritar">
        <svg v-if="isFavorite" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
          class="w-6 h-6">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 
               2 12.28 2 8.5 2 5.42 4.42 3 
               7.5 3c1.74 0 3.41 0.81 4.5 
               2.09C13.09 3.81 14.76 3 
               16.5 3 19.58 3 22 5.42 
               22 8.5c0 3.78-3.4 6.86-8.55 
               11.54L12 21.35z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l-1.45-1.32C5.4 15.36 
            2 12.28 2 8.5 2 5.42 4.42 
            3 7.5 3 9.24 3 10.91 3.81 
            12 5.09 13.09 3.81 14.76 
            3 16.5 3 19.58 3 22 5.42 
            22 8.5c0 3.78-3.4 6.86-8.55 
            11.54L12 21z" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  movie: Object,
  isFavorite: Boolean
})
</script>
