import axios, { InternalAxiosRequestConfig } from 'axios';

// Define API URL with fallback
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:5000/api';
const API_ORIGIN = API_URL.replace(/\/api$/, '');
console.log('Using API URL:', API_URL);

const api = axios.create({
  baseURL: API_URL,
  timeout: 10000, // Timeout after 10 seconds
  headers: {
    'Accept': 'application/json'
  }
});

// Add auth token to requests
api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = localStorage.getItem('token');
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  console.log(`Request: ${config.method?.toUpperCase()} ${config.url}`);
  return config;
});

// Add response interceptor for debugging
api.interceptors.response.use(
  (response) => {
    console.log(`API response: ${response.config.url} ${response.status}`);
    return response;
  },
  (error) => {
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('API error:', error.config?.url, error.response.status, error.response.data);
    } else if (error.request) {
      // The request was made but no response was received
      console.error('API no response:', error.config?.url, 'Request:', error.request);
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('API config error:', error.message);
    }
    return Promise.reject(error);
  }
);

export const articleAPI = {
  getAll: async (page = 1, limit = 9) => {
    console.log(`Fetching articles: page=${page}, limit=${limit}`);
    try {
      const response = await api.get(`/articles?page=${page}&limit=${limit}`);
      const normalize = (a: any) => ({
        ...a,
        coverImage: a?.coverImage?.startsWith('/uploads') ? `${API_ORIGIN}${a.coverImage}` : a?.coverImage,
      });
      if (Array.isArray(response.data)) {
        response.data = response.data.map(normalize);
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        response.data.data = response.data.data.map(normalize);
      }
      return response;
    } catch (error) {
      console.error('Error in getAll:', error);
      throw error;
    }
  },

  getById: async (id: string) => {
    try {
      const response = await api.get(`/articles/${id}`);
      const a = response.data;
      return {
        ...a,
        coverImage: a?.coverImage?.startsWith('/uploads') ? `${API_ORIGIN}${a.coverImage}` : a?.coverImage,
      };
    } catch (error) {
      console.error(`Error fetching article ${id}:`, error);
      throw error;
    }
  },

  create: async (articleData: FormData) => {
    try {
      const response = await api.post('/articles', articleData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      return response.data;
    } catch (error) {
      console.error('Error creating article:', error);
      throw error;
    }
  },

  update: async (id: string, articleData: FormData) => {
    try {
      const response = await api.put(`/articles/${id}`, articleData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      return response.data;
    } catch (error) {
      console.error(`Error updating article ${id}:`, error);
      throw error;
    }
  },

  delete: async (id: string) => {
    try {
      const response = await api.delete(`/articles/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting article ${id}:`, error);
      throw error;
    }
  },

  clearAll: async () => {
    try {
      const response = await api.delete('/articles');
      return response.data;
    } catch (error) {
      console.error('Error clearing all articles:', error);
      throw error;
    }
  },

  createSamples: async () => {
    try {
      const response = await api.post('/articles/samples');
      return response.data;
    } catch (error) {
      console.error('Error creating sample articles:', error);
      throw error;
    }
  }
};

export default api; 