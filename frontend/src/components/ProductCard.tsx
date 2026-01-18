import type { Product } from '../types/product';
import './ProductCard.css';

interface ProductCardProps {
  product: Product;
}

export function ProductCard({ product }: ProductCardProps) {
  return (
    <div className="product-card">
      <img 
        src={product.thumbnail} 
        alt={product.title}
        className="product-image"
      />
      <div className="product-info">
        <h3 className="product-title">{product.title}</h3>
        <p className="product-category">{product.category}</p>
        <p className="product-price">${product.price}</p>
        <p className="product-rating">⭐ {product.rating}</p>
      </div>
    </div>
  );
}
