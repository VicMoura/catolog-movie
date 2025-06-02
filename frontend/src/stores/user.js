import { defineStore } from "pinia";
import axios from "@/lib/axios";
import { UserProvider } from "@/providers/UserProvider";

function saveToken(token) {
  if (token) localStorage.setItem("token", token);
  else localStorage.removeItem("token");
}

function setAuthHeader(token) {
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  else delete axios.defaults.headers.common["Authorization"];
}

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    favorites: [],
    genres: [],
    loadingFavorites: false,
    genre_selected: null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isFavorite: (state) => (movieId) =>
      state.favorites.some((f) => f.tmdb_id === movieId),
  },

  persist: {
    storage: localStorage,
    paths: ["token", "user", "favorites", "genres", "genre_selected"],
  },

  actions: {
    async login(credentials) {
      try {
        const response = await UserProvider.login(credentials);
        this.token = response.access_token;
        saveToken(this.token);
        setAuthHeader(this.token);
        await this.fetchUser();
        await this.fetchFavorites();
      } catch (error) {
        throw error;
      }
    },

    async fetchUser() {
      try {
        const response = await UserProvider.getUser();
        this.user = response.data;
      } catch (error) {
        this.user = null;
        throw error;
      }
    },

    async fetchFavorites(genreId = null) {
      console.log(genreId);
      if (!this.token) return;
      this.genre_selected = genreId;
      this.loadingFavorites = true;
      try {
        const params = {};
        if (genreId) params.genre_id = genreId;
        const response = await axios.get("/favorites", { params });
        this.favorites = response.data.data;
      } catch (error) {
        console.error("Erro ao buscar favoritos", error);
      } finally {
        this.loadingFavorites = false;
      }
    },

    async logout() {
      try {
        await UserProvider.logout();
      } finally {
        this.token = null;
        this.user = null;
        this.favorites = [];
        this.$reset();
        saveToken(null);
        setAuthHeader(null);
      }
    },

    async toggleFavorite(movie) {
      console.log("teste");
      console.log(movie);
      try {
        if (this.isFavorite(movie.tmdb_id)) {
          await axios.delete(`/favorites/${movie.tmdb_id}`);
          this.favorites = this.favorites.filter(
            (f) => f.tmdb_id !== movie.tmdb_id
          );
        } else {
          const movieResponse = await axios.post("/favorites", {
            tmdb_id: movie.tmdb_id,
          });
          this.favorites = [...this.favorites, movieResponse.data.data];
        }
      } catch (error) {
        console.error("Erro ao atualizar favoritos", error);
        throw error;
      }
    },

    async fetchGenres() {
      if (this.genres.length) return;
      try {
        const response = await axios.get("/genres");
        this.genres = response.data.data;
      } catch (error) {
        toast.error("Erro ao carregar os gêneros");
      }
    },

    initialize() {
      if (this.token) {
        setAuthHeader(this.token);
        if (!this.genres.length) this.fetchGenres();
        if (!this.user) this.fetchUser();
        if (!this.favorites.length || this.genre_selected !== null)
          this.fetchFavorites();
      }
    },
  },
});
