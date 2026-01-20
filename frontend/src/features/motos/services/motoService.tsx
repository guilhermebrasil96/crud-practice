import { apiClient } from '../../../api/client';
import type { ApiResponse } from '../../../shared/types/api';
import type { Moto } from '../types/moto';

export const motoService = {
  async getAll(): Promise<Moto[]> {
    const { data } = await apiClient.get<ApiResponse<{ motos: Moto[] }>>('/motos');
    if (data.success && data.data?.motos) return data.data.motos;
    return [];
  },

  async getById(id: number): Promise<Moto | null> {
    const { data } = await apiClient.get<ApiResponse<{ moto: Moto }>>(`/motos/${id}`);
    if (data.success && data.data?.moto) return data.data.moto;
    return null;
  },

  async create(payload: { name: string; description?: string; price?: number }): Promise<Moto> {
    const { data } = await apiClient.post<ApiResponse<{ moto: Moto }>>('/motos', payload);
    if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error creating');
    return data.data.moto;
  },

  async update(id: number, payload: Partial<{ name: string; description: string; price: number }>): Promise<Moto> {
    const { data } = await apiClient.put<ApiResponse<{ moto: Moto }>>(`/motos/${id}`, payload);
    if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error updating');
    return data.data.moto;
  },

  async delete(id: number): Promise<void> {
    const { data } = await apiClient.delete<ApiResponse<unknown>>(`/motos/${id}`);
    if (!data.success && data.error) throw new Error(data.error.message);
  },
};
