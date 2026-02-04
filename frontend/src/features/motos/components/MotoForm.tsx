import { useState, useEffect } from 'react';
import { API_URL } from '../../../api/client';
import type { Moto } from '../types/moto';
import '../../../shared/styles/ProductForm.css';

interface MotoFormProps {
  moto: Moto | null;
  onSave: (data: { name: string; description: string; price?: number; image?: File | null }) => Promise<void>;
  onCancel: () => void;
}

export function MotoForm({ moto, onSave, onCancel }: MotoFormProps) {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isEdit = moto != null;

  useEffect(() => {
    if (moto) {
      setName(moto.name);
      setDescription(moto.description);
      setPrice(moto.price != null ? String(moto.price) : '');
      setImageFile(null);
    } else {
      setName('');
      setDescription('');
      setPrice('');
      setImageFile(null);
    }
  }, [moto]);

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
        <h2>{isEdit ? 'Edit moto' : 'New moto'}</h2>
        <form onSubmit={handleSubmit}>
          {error && <div className="form-error">{error}</div>}
          <div className="form-field">
            <label htmlFor="moto-name">Name *</label>
            <input
              id="moto-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              autoFocus
              required
            />
          </div>
          <div className="form-field">
            <label htmlFor="moto-desc">Description</label>
            <textarea
              id="moto-desc"
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
            />
          </div>
          <div className="form-field">
            <label htmlFor="moto-price">Price</label>
            <input
              id="moto-price"
              type="number"
              step="0.01"
              min="0"
              value={price}
              onChange={(e) => setPrice(e.target.value)}
              placeholder="0.00"
            />
          </div>
          <div className="form-field">
            <label htmlFor="moto-image">Image</label>
            {moto?.image && (
              <img src={`${API_URL}/${moto.image}`} alt="" className="form-image-preview" />
            )}
            <input
              id="moto-image"
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
