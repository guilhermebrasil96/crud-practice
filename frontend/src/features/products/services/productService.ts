import { apiClient } from '../../../api/client';
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

  async create(payload: { name: string; description?: string; price?: number }): Promise<Product> {
    const { data } = await apiClient.post<ApiResponse<{ product: Product }>>('/products', payload);
    if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error al crear');
    return data.data.product;
  },

  async update(id: number, payload: Partial<{ name: string; description: string; price: number }>): Promise<Product> {
    const { data } = await apiClient.put<ApiResponse<{ product: Product }>>(`/products/${id}`, payload);
    if (!data.success || !data.data?.product) throw new Error(data.error?.message || 'Error al actualizar');
    return data.data.product;
  },

  async delete(id: number): Promise<void> {
    const { data } = await apiClient.delete<ApiResponse<unknown>>(`/products/${id}`);
    if (!data.success && data.error) throw new Error(data.error.message);
  },
};
