import { API_URL } from '../../../api/client';
import type { Moto } from '../types/moto';
import '../../../shared/styles/ProductCard.css';

interface MotoCardProps {
  moto: Moto;
  onEdit?: (p: Moto) => void;
  onDelete?: (p: Moto) => void;
}

export function MotoCard({ moto, onEdit, onDelete }: MotoCardProps) {
  return (
    <div className="product-card">
      <div className="product-info">
        {moto.image && (
          <img src={`${API_URL}/${moto.image}`} alt="" className="product-card-image" />
        )}
        <h3 className="product-title">{moto.name}</h3>
        <p className="product-description">{moto.description}</p>
        <p className="product-price">{moto.price != null ? `€${moto.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="product-actions">
            {onEdit && <button type="button" onClick={() => onEdit(moto)}>Edit</button>}
            {onDelete && <button type="button" onClick={() => onDelete(moto)}>Delete</button>}
          </div>
        )}
      </div>
    </div>
  );
}
