import { apiClient } from '../../../api/client';
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

  async create(payload: { name: string; description?: string; price?: number }): Promise<Car> {
    const { data } = await apiClient.post<ApiResponse<{ car: Car }>>('/cars', payload);
    if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error al crear');
    return data.data.car;
  },

  async update(id: number, payload: Partial<{ name: string; description: string; price: number }>): Promise<Car> {
    const { data } = await apiClient.put<ApiResponse<{ car: Car }>>(`/cars/${id}`, payload);
    if (!data.success || !data.data?.car) throw new Error(data.error?.message || 'Error al actualizar');
    return data.data.car;
  },

  async delete(id: number): Promise<void> {
    const { data } = await apiClient.delete<ApiResponse<unknown>>(`/cars/${id}`);
    if (!data.success && data.error) throw new Error(data.error.message);
  },
};
