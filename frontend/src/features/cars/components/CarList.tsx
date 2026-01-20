import { useEffect, useState } from 'react';
import { carService } from '../services/carService';
import type { Car } from '../types/car';
import { CarCard } from './CarCard';
import { CarForm } from './CarForm';
import './CarList.css';

export function CarList() {
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Car | null>(null);

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const data = await carService.getAll();
      setCars(data);
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Error al cargar');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, []);

  const openCreate = () => {
    setEditing(null);
    setFormOpen(true);
  };

  const openEdit = (c: Car) => {
    setEditing(c);
    setFormOpen(true);
  };

  const closeForm = () => {
    setFormOpen(false);
    setEditing(null);
  };

  const handleSave = async (data: { name: string; description: string; price?: number }) => {
    if (editing) {
      await carService.update(editing.id, data);
    } else {
      await carService.create(data);
    }
    closeForm();
    await load();
  };

  const handleDelete = async (c: Car) => {
    if (!confirm(`¿Eliminar "${c.name}"?`)) return;
    try {
      await carService.delete(c.id);
      setCars((prev) => prev.filter((x) => x.id !== c.id));
    } catch (e) {
      alert(e instanceof Error ? e.message : 'Error al eliminar');
    }
  };

  if (loading) return <div className="list-loading">Cargando...</div>;
  if (error) return <div className="list-error">{error}</div>;

  return (
    <div className="car-list">
      <div className="car-list-header">
        <h1>Coches</h1>
        <button type="button" className="btn-primary" onClick={openCreate}>
          Nuevo coche
        </button>
      </div>
      <div className="car-list-grid">
        {cars.map((c) => (
          <CarCard key={c.id} car={c} onEdit={openEdit} onDelete={handleDelete} />
        ))}
      </div>
      {formOpen && (
        <CarForm
          car={editing}
          onSave={handleSave}
          onCancel={closeForm}
        />
      )}
    </div>
  );
}
