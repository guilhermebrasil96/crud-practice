import { apiClient, formDataClient } from '../../../api/client';
import type { ApiResponse } from '../../../shared/types/api';
import type { Product } from '../types/product';

export const productService = {
    async getAll(): Promise<Product[]> {
        const { data } = await apiClient.get<ApiResponse<{ products: Product[] }>>('/products');
        if (data.success && data.data?.products) return data.data.products;
        return [];
      },
    
      async getById(id: number): Promise<Product | null> {
        const { data } = await apiClient.get<ApiResponse<{ product: Product }>>(`/products/${id}`);
        if (data.success && data.data?.product) return data.data.product;
        return null;
      },
    
      async create(payload: { name: string; description?: string; price?: number; image?: File | null }): Promise<Product> {
        if (payload.image instanceof File) {
          const fd = new FormData();
          fd.append('name', payload.name);
          fd.append('description', payload.description ?? '');
          if (payload.price != null) fd.append('price', String(payload.price));
          fd.append('image', payload.image);
          const { data } = await formDataClient.post<ApiResponse<{ product: Product }>>('/products', fd);
          if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error creating');
          return data.data.product;
        }
        const { data } = await apiClient.post<ApiResponse<{ product: Product }>>('/products', payload);
        if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error creating');
        return data.data.product;
      },
    
      async update(id: number, payload: Partial<{ name: string; description: string; price: number; image?: File | null }>): Promise<Product> {
        if (payload.image instanceof File) {
          const fd = new FormData();
          fd.append('name', payload.name ?? '');
          fd.append('description', payload.description ?? '');
          if (payload.price != null) fd.append('price', String(payload.price));
          fd.append('image', payload.image);
          const { data } = await formDataClient.put<ApiResponse<{ product: Product }>>(`/products/${id}`, fd);
          if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error updating');
          return data.data.product;
        }
        const { data } = await apiClient.put<ApiResponse<{ product: Product }>>(`/products/${id}`, payload);
        if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error updating');
        return data.data.product;
      },
    
      async delete(id: number): Promise<void> {
        const { data } = await apiClient.delete<ApiResponse<unknown>>(`/products/${id}`);
        if (!data.success && data.error) throw new Error(data.error.message);
      },

}