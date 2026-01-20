import { Navigate, Route, Routes } from 'react-router-dom';
import { Layout } from './components/Layout';
import { ProductList } from './features/products';
import { CarList } from './features/cars';
import './App.css';
import { MotoList } from './features/motos/components/MotoList';

function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="/products" replace />} />
        <Route path="products" element={<ProductList />} />
        <Route path="cars" element={<CarList />} />
        <Route path="motos" element={<MotoList />} />
      </Route>
      <Route path="*" element={<Navigate to="/products" replace />} />
    </Routes>
  );
}

export default App;
