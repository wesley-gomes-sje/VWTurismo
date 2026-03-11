import { Link, useNavigate } from 'react-router-dom'
import { useAuthStore } from '../../store/authStore'

export default function Navbar() {
  const { user, logout } = useAuthStore()
  const navigate = useNavigate()

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  const isAdmin = user?.profile === 'admin'

  return (
    <nav className="bg-brand-700 text-white px-6 py-3 flex items-center justify-between shadow">
      <Link to="/" className="text-xl font-bold tracking-wide">VWTurismo</Link>

      <div className="flex items-center gap-6 text-sm">
        {isAdmin ? (
          <>
            <Link to="/admin/cities"   className="hover:text-brand-100">Cidades</Link>
            <Link to="/admin/vehicles" className="hover:text-brand-100">Veículos</Link>
            <Link to="/admin/routes"   className="hover:text-brand-100">Rotas</Link>
            <Link to="/admin/tickets"  className="hover:text-brand-100">Passagens</Link>
            <Link to="/admin/users"    className="hover:text-brand-100">Clientes</Link>
          </>
        ) : (
          <>
            <Link to="/tickets"     className="hover:text-brand-100">Minhas Passagens</Link>
            <Link to="/tickets/buy" className="hover:text-brand-100">Comprar</Link>
          </>
        )}

        <span className="text-brand-200">|</span>
        <span className="text-brand-200 font-medium">{user?.name}</span>
        <button onClick={handleLogout} className="hover:text-red-300 transition-colors">
          Sair
        </button>
      </div>
    </nav>
  )
}
