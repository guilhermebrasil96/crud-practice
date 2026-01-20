import type { Car } from '../types/car';
import './CarCard.css';

interface CarCardProps {
  car: Car;
  onEdit?: (c: Car) => void;
  onDelete?: (c: Car) => void;
}

export function CarCard({ car, onEdit, onDelete }: CarCardProps) {
  return (
    <div className="car-card">
      <div className="car-info">
        <h3 className="car-title">{car.name}</h3>
        <p className="car-description">{car.description}</p>
        <p className="car-price">{car.price != null ? `€${car.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="car-actions">
            {onEdit && <button type="button" onClick={() => onEdit(car)}>Editar</button>}
            {onDelete && <button type="button" onClick={() => onDelete(car)}>Eliminar</button>}
          </div>
        )}
      </div>
    </div>
  );
}
