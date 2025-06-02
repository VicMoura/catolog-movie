import axios from '@/lib/axios'

export const TmdbProvider = {
  
  /**
   * Realiza uma busca de filmes no backend, que consulta a API TMDB.
   * 
   * @param {string} query - Texto da busca
   * @returns {Promise} - Lista de filmes encontrados
   */
  searchMovies(query) {
    return axios.get('/movies/search', {
      params: { query }
    })
    .then(response => response.data.data)
  }

}
