import { apiClient, formDataClient } from '../../../api/client';
import type { ApiResponse } from '../../../shared/types/api';
import type { Car } from '../types/car';

export const carService = {
  async getAll(): Promise<Car[]> {
    const { data } = await apiClient.get<ApiResponse<{ cars: Car[] }>>('/cars');
    if (data.success && data.data?.cars) return data.data.cars;
    return [];
  },

  async getById(id: number): Promise<Car | null> {
    const { data } = await apiClient.get<ApiResponse<{ car: Car }>>(`/cars/${id}`);
    if (data.success && data.data?.car) return data.data.car;
    return null;
  },

  async create(payload: { name: string; description?: string; price?: number; image?: File | null }): Promise<Car> {
    if (payload.image instanceof File) {
      const fd = new FormData();
      fd.append('name', payload.name);
      fd.append('description', payload.description ?? '');
      if (payload.price != null) fd.append('price', String(payload.price));
      fd.append('image', payload.image);
      const { data } = await formDataClient.post<ApiResponse<{ car: Car }>>('/cars', fd);
      if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error creating');
      return data.data.car;
    }
    const { data } = await apiClient.post<ApiResponse<{ car: Car }>>('/cars', payload);
    if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error creating');
    return data.data.car;
  },

  async update(id: number, payload: Partial<{ name: string; description: string; price: number; image?: File | null }>): Promise<Car> {
    if (payload.image instanceof File) {
      const fd = new FormData();
      fd.append('name', payload.name ?? '');
      fd.append('description', payload.description ?? '');
      if (payload.price != null) fd.append('price', String(payload.price));
      fd.append('image', payload.image);
      const { data } = await formDataClient.put<ApiResponse<{ car: Car }>>(`/cars/${id}`, fd);
      if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error updating');
      return data.data.car;
    }
    const { data } = await apiClient.put<ApiResponse<{ car: Car }>>(`/cars/${id}`, payload);
    if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error updating');
    return data.data.car;
  },

  async delete(id: number): Promise<void> {
    const { data } = await apiClient.delete<ApiResponse<unknown>>(`/cars/${id}`);
    if (!data.success && data.error) throw new Error(data.error.message);
  },
};
