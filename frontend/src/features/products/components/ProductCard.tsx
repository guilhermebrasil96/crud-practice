import type { Product } from '../types/product';
import './ProductCard.css';

interface ProductCardProps {
  product: Product;
  onEdit?: (p: Product) => void;
  onDelete?: (p: Product) => void;
}

export function ProductCard({ product, onEdit, onDelete }: ProductCardProps) {
  return (
    <div className="product-card">
      <div className="product-info">
        <h3 className="product-title">{product.name}</h3>
        <p className="product-description">{product.description}</p>
        <p className="product-price">{product.price != null ? `€${product.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="product-actions">
            {onEdit && <button type="button" onClick={() => onEdit(product)}>Editar</button>}
            {onDelete && <button type="button" onClick={() => onDelete(product)}>Eliminar</button>}
          </div>
        )}
      </div>
    </div>
  );
}
