import { API_URL } from '../../../api/client';
import type { Car } from '../types/car';
import '../../../shared/styles/ProductCard.css';

interface CarCardProps {
  car: Car;
  onEdit?: (c: Car) => void;
  onDelete?: (c: Car) => void;
}

export function CarCard({ car, onEdit, onDelete }: CarCardProps) {
  return (
    <div className="product-card">
      <div className="product-info">
        {car.image && (
          <img src={`${API_URL}/${car.image}`} alt="" className="product-card-image" />
        )}
        <h3 className="product-title">{car.name}</h3>
        <p className="product-description">{car.description}</p>
        <p className="product-price">{car.price != null ? `€${car.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="product-actions">
            {onEdit && <button type="button" onClick={() => onEdit(car)}>Edit</button>}
            {onDelete && <button type="button" onClick={() => onDelete(car)}>Delete</button>}
          </div>
        )}
      </div>
    </div>
  );
}
