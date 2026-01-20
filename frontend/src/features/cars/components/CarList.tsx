import { useEffect, useState } from 'react';
import type { Car } from '../types/car';
import { carService } from '../services/carService';
import { CarCard } from './CarCard';
import { CarForm } from './CarForm';

export function CarList() {

  const [cars, setCars] = useState<Car[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [editing, setEditing] = useState<Car | null>(null);
    const [formOpen, setFormOpen] = useState(false);

    const load = async () => {
        setLoading(true);
        setError(null);
        try {
          const data = await carService.getAll();
          setCars(data);
        } catch (e) {
          setError(e instanceof Error ? e.message : 'Error loading');
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
      if (!confirm(`Delete "${c.name}"?`)) return;
      try {
        await carService.delete(c.id);
        setCars((prev) => prev.filter((x) => x.id !== c.id));
      } catch (e) {
        alert(e instanceof Error ? e.message : 'Error deleting');
      }
    };
  
    if (loading) return <div className="list-loading">Loading...</div>;
    if (error) return <div className="list-error">{error}</div>;

      return (
        <div className="product-list">
          <div className="product-list-header">
            <h1>Cars</h1>
            <button type="button" className="btn-primary" onClick={openCreate}>
              New Car
            </button>
          </div>
          <div className="product-list-grid">
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

