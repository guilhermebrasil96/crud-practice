import axios from 'axios';
import type { Product, ProductsResponse } from '../types/product';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

export const productService = {
  async getAllProducts(): Promise<Product[]> {
    const response = await axios.get<ProductsResponse>(`${API_URL}/products`);
    
    if (response.data.success && response.data.data?.products) {
      return response.data.data.products;
    }
    
    return [];
  },
};
