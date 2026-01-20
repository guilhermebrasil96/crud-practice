import { useState, useEffect } from 'react';
import { API_URL } from '../../../api/client';
import type { Product } from '../types/product';
import './ProductForm.css';

interface ProductFormProps {
  product: Product | null;
  onSave: (data: { name: string; description: string; price?: number; image?: File | null }) => Promise<void>;
  onCancel: () => void;
}

export function ProductForm({ product, onSave, onCancel }: ProductFormProps) {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isEdit = product != null;

  useEffect(() => {
    if (product) {
      setName(product.name);
      setDescription(product.description);
      setPrice(product.price != null ? String(product.price) : '');
      setImageFile(null);
    } else {
      setName('');
      setDescription('');
      setPrice('');
      setImageFile(null);
    }
  }, [product]);

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
      const payload: { name: string; description: string; price?: number; image?: File | null } = {
        name: n,
        description: description.trim(),
      };
      const p = price.trim();
      if (p && !Number.isNaN(Number(p))) payload.price = Number(p);
      if (imageFile) payload.image = imageFile;
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
        <h2>{isEdit ? 'Edit product' : 'New product'}</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="form-error">{error}</div>}
          <div className="form-field">
            <label htmlFor="product-name">Name *</label>
            <input
              id="product-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              autoFocus
              required
            />
          </div>
          <div className="form-field">
            <label htmlFor="product-desc">Description</label>
            <textarea
              id="product-desc"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
            />
          </div>
          <div className="form-field">
            <label htmlFor="product-price">Price</label>
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
          <div className="form-field">
            <label htmlFor="product-image">Image</label>
            {product?.image && (
              <img src={`${API_URL}/${product.image}`} alt="" className="form-image-preview" />
            )}
            <input
              id="product-image"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              onChange={(e) => setImageFile(e.target.files?.[0] ?? null)}
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
