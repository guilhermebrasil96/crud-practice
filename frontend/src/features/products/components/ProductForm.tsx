import { useState, useEffect } from 'react';
import type { Product } from '../types/product';
import './ProductForm.css';

interface ProductFormProps {
  product: Product | null;
  onSave: (data: { name: string; description: string; price?: number }) => Promise<void>;
  onCancel: () => void;
}

export function ProductForm({ product, onSave, onCancel }: ProductFormProps) {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isEdit = product != null;

  useEffect(() => {
    if (product) {
      setName(product.name);
      setDescription(product.description);
      setPrice(product.price != null ? String(product.price) : '');
    } else {
      setName('');
      setDescription('');
      setPrice('');
    }
  }, [product]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    const n = name.trim();
    if (!n) {
      setError('El nombre es obligatorio');
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
      setError(e instanceof Error ? e.message : 'Error al guardar');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="form-backdrop" onClick={onCancel}>
      <div className="form-modal" onClick={(e) => e.stopPropagation()}>
        <h2>{isEdit ? 'Editar producto' : 'Nuevo producto'}</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="form-error">{error}</div>}
          <div className="form-field">
            <label htmlFor="product-name">Nombre *</label>
            <input
              id="product-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              autoFocus
              required
            />
          </div>
          <div className="form-field">
            <label htmlFor="product-desc">Descripción</label>
            <textarea
              id="product-desc"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
            />
          </div>
          <div className="form-field">
            <label htmlFor="product-price">Precio</label>
            <input
              id="product-price"
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
              Cancelar
            </button>
            <button type="submit" disabled={saving}>
              {saving ? 'Guardando…' : isEdit ? 'Guardar' : 'Crear'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
