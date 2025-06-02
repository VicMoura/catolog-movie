import axios from "@/lib/axios";

export const UserProvider = {
  async register(userData) {
    const response = await axios.post("/users", userData);
    return response.data;
  },

  async login(credentials) {
    const response = await axios.post("/users/login", credentials);
    return response.data.data;
  },

  async getUser() {
    const response = await axios.get("/users/detail");
    return response.data;
  },

  async logout() {
    const response = await axios.post("/users/logout");
    return response.data;
  },
};
