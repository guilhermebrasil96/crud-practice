import { useEffect, useState } from 'react';
import { productService } from '../services/productService';
import type { Product } from '../types/product';
import { ProductCard } from './ProductCard';
import { ProductForm } from './ProductForm';
import './ProductList.css';

export function ProductList() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [formOpen, setFormOpen] = useState(false);
  const [editing, setEditing] = useState<Product | null>(null);

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const data = await productService.getAll();
      setProducts(data);
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

  const openEdit = (p: Product) => {
    setEditing(p);
    setFormOpen(true);
  };

  const closeForm = () => {
    setFormOpen(false);
    setEditing(null);
  };

  const handleSave = async (data: { name: string; description: string; price?: number }) => {
    if (editing) {
      await productService.update(editing.id, data);
    } else {
      await productService.create(data);
    }
    closeForm();
    await load();
  };

  const handleDelete = async (p: Product) => {
    if (!confirm(`Delete "${p.name}"?`)) return;
    try {
      await productService.delete(p.id);
      setProducts((prev) => prev.filter((x) => x.id !== p.id));
    } catch (e) {
      alert(e instanceof Error ? e.message : 'Error deleting');
    }
  };

  if (loading) return <div className="list-loading">Loading...</div>;
  if (error) return <div className="list-error">{error}</div>;

  return (
    <div className="product-list">
      <div className="product-list-header">
        <h1>Products</h1>
        <button type="button" className="btn-primary" onClick={openCreate}>
          New Product
        </button>
      </div>
      <div className="product-list-grid">
        {products.map((p) => (
          <ProductCard key={p.id} product={p} onEdit={openEdit} onDelete={handleDelete} />
        ))}
      </div>
      {formOpen && (
        <ProductForm
          product={editing}
          onSave={handleSave}
          onCancel={closeForm}
        />
      )}
    </div>
  );
}
