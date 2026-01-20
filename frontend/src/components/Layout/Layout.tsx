import { NavLink, Outlet } from 'react-router-dom';
import './Layout.css';

export function Layout() {
  return (
    <div className="layout">
      <nav className="layout-nav">
        <NavLink to="/products" className={({ isActive }) => (isActive ? 'active' : '')}>
          Products
        </NavLink>
        <NavLink to="/cars" className={({ isActive }) => (isActive ? 'active' : '')}>
          Cars
        </NavLink>
      </nav>
      <main className="layout-main">
        <Outlet />
      </main>
    </div>
  );
}
