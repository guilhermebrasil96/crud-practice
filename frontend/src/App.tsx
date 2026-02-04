import { Navigate, Route, Routes } from 'react-router-dom';
import { Layout } from './components/Layout';
import { CarList } from './features/cars';
import { MotoList } from './features/motos/components/MotoList';
import './App.css';
import { ProductList } from './features/products/components/ProductList';

function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="/cars" replace />} />
        <Route path="cars" element={<CarList />} />
        <Route path="motos" element={<MotoList />} />
        <Route path="products" element={<ProductList />} />
      </Route>
      <Route path="*" element={<Navigate to="/cars" replace />} />
    </Routes>
  );
}

export default App;
