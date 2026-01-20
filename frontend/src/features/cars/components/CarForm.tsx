import { useState, useEffect } from 'react';
import type { Car } from '../types/car';
import './CarForm.css';

interface CarFormProps {
  car: Car | null;
  onSave: (data: { name: string; description: string; price?: number }) => Promise<void>;
  onCancel: () => void;
}

export function CarForm({ car, onSave, onCancel }: CarFormProps) {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isEdit = car != null;

  useEffect(() => {
    if (car) {
      setName(car.name);
      setDescription(car.description);
      setPrice(car.price != null ? String(car.price) : '');
    } else {
      setName('');
      setDescription('');
      setPrice('');
    }
  }, [car]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    const n = name.trim();
    if (!n) {
      setError('Name is required');
      return;
    }
    setSaving(true);
    try {
      const payload: { name: string; description: string; price?: number } = {
        name: n,
        description: description.trim(),
      };
      const p = price.trim();
      if (p && !Number.isNaN(Number(p))) payload.price = Number(p);
      await onSave(payload);
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Error saving');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="form-backdrop" onClick={onCancel}>
      <div className="form-modal" onClick={(e) => e.stopPropagation()}>
        <h2>{isEdit ? 'Edit car' : 'New car'}</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="form-error">{error}</div>}
          <div className="form-field">
            <label htmlFor="car-name">Name *</label>
            <input
              id="car-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              autoFocus
              required
            />
          </div>
          <div className="form-field">
            <label htmlFor="car-desc">Description</label>
            <textarea
              id="car-desc"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
            />
          </div>
          <div className="form-field">
            <label htmlFor="car-price">Price</label>
            <input
              id="car-price"
              type="number"
              step="0.01"
              min="0"
              value={price}
              onChange={(e) => setPrice(e.target.value)}
              placeholder="0.00"
            />
          </div>
          <div className="form-actions">
            <button type="button" onClick={onCancel} disabled={saving}>
              Cancel
            </button>
            <button type="submit" disabled={saving}>
              {saving ? 'Saving…' : isEdit ? 'Save' : 'Create'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
