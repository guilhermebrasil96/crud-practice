import { API_URL } from '../../../api/client';
import type { Product } from '../types/product';
import '../../../shared/styles/ProductCard.css';

interface ProductCardProps {
  product: Product;
  onEdit?: (p: Product) => void;
  onDelete?: (p: Product) => void;
}

export function ProductCard({ product, onEdit, onDelete }: ProductCardProps) {
  return (
    <div className="product-card">
      <div className="product-info">
        {product.image && (
          <img src={`${API_URL}/${product.image}`} alt="" className="product-card-image" />
        )}
        <h3 className="product-title">{product.name}</h3>
        <p className="product-price">{product.price != null ? `€${product.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="product-actions">
            {onDelete && <button type="button" onClick={() => onDelete(product)}>Delete</button>}
          </div>
        )}
      </div>
    </div>
  );
}
