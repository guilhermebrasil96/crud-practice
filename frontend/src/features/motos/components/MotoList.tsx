import { useEffect, useState } from 'react';
import { motoService } from '../services/motoService';
import type { Moto } from '../types/moto';
import { MotoCard } from './MotoCard';
import { MotoForm } from './MotoForm';
import '../../products/components/ProductList.css';

export function MotoList() {
  const [motos, setMotos] = useState<Moto[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Moto | null>(null);

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const data = await motoService.getAll();
      setMotos(data);
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

  const openEdit = (p: Moto) => {
    setEditing(p);
    setFormOpen(true);
  };

  const closeForm = () => {
    setFormOpen(false);
    setEditing(null);
  };

  const handleSave = async (data: { name: string; description: string; price?: number }) => {
    if (editing) {
      await motoService.update(editing.id, data);
    } else {
      await motoService.create(data);
    }
    closeForm();
    await load();
  };

  const handleDelete = async (p: Moto) => {
    if (!confirm(`Delete "${p.name}"?`)) return;
    try {
      await motoService.delete(p.id);
      setMotos((prev) => prev.filter((x) => x.id !== p.id));
    } catch (e) {
      alert(e instanceof Error ? e.message : 'Error deleting');
    }
  };

  if (loading) return <div className="list-loading">Loading...</div>;
  if (error) return <div className="list-error">{error}</div>;

  return (
    <div className="product-list">
      <div className="product-list-header">
        <h1>Motos</h1>
        <button type="button" className="btn-primary" onClick={openCreate}>
          New Moto
        </button>
      </div>
      <div className="product-list-grid">
        {motos.map((p) => (
          <MotoCard key={p.id} moto={p} onEdit={openEdit} onDelete={handleDelete} />
        ))}
      </div>
      {formOpen && (
        <MotoForm
          moto={editing}
          onSave={handleSave}
          onCancel={closeForm}
        />
      )}
    </div>
  );
}
