import type { Moto } from '../types/moto';
import './MotoCard.css';

interface MotoCardProps {
  moto: Moto;
  onEdit?: (p: Moto) => void;
  onDelete?: (p: Moto) => void;
}

export function MotoCard({ moto, onEdit, onDelete }: MotoCardProps) {
  return (
    <div className="moto-card">
      <div className="moto-info">
        <h3 className="moto-title">{moto.name}</h3>
        <p className="moto-description">{moto.description}</p>
        <p className="moto-price">{moto.price != null ? `€${moto.price}` : '—'}</p>
        {(onEdit || onDelete) && (
          <div className="moto-actions">
            {onEdit && <button type="button" onClick={() => onEdit(moto)}>Edit</button>}
            {onDelete && <button type="button" onClick={() => onDelete(moto)}>Delete</button>}
          </div>
        )}
      </div>
    </div>
  );
}
