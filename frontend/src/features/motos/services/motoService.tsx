import { apiClient, formDataClient } from '../../../api/client';
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

  async create(payload: { name: string; description?: string; price?: number; image?: File | null }): Promise<Moto> {
    if (payload.image instanceof File) {
      const fd = new FormData();
      fd.append('name', payload.name);
      fd.append('description', payload.description ?? '');
      if (payload.price != null) fd.append('price', String(payload.price));
      fd.append('image', payload.image);
      const { data } = await formDataClient.post<ApiResponse<{ moto: Moto }>>('/motos', fd);
      if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error creating');
      return data.data.moto;
    }
    const { data } = await apiClient.post<ApiResponse<{ moto: Moto }>>('/motos', payload);
    if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error creating');
    return data.data.moto;
  },

  async update(id: number, payload: Partial<{ name: string; description: string; price: number; image?: File | null }>): Promise<Moto> {
    if (payload.image instanceof File) {
      const fd = new FormData();
      fd.append('name', payload.name ?? '');
      fd.append('description', payload.description ?? '');
      if (payload.price != null) fd.append('price', String(payload.price));
      fd.append('image', payload.image);
      const { data } = await formDataClient.put<ApiResponse<{ moto: Moto }>>(`/motos/${id}`, fd);
      if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error updating');
      return data.data.moto;
    }
    const { data } = await apiClient.put<ApiResponse<{ moto: Moto }>>(`/motos/${id}`, payload);
    if (!data.success || !data.data?.moto) throw new Error(data.error?.message || 'Error updating');
    return data.data.moto;
  },

  async delete(id: number): Promise<void> {
    const { data } = await apiClient.delete<ApiResponse<unknown>>(`/motos/${id}`);
    if (!data.success && data.error) throw new Error(data.error.message);
  },
};
