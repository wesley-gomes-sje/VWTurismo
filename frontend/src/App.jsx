import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'

import AppLayout      from './components/layout/AppLayout'
import ProtectedRoute from './components/layout/ProtectedRoute'

import LoginPage         from './pages/auth/LoginPage'
import RegisterPage      from './pages/auth/RegisterPage'
import HomePage          from './pages/HomePage'

import CitiesPage        from './pages/admin/CitiesPage'
import VehiclesPage      from './pages/admin/VehiclesPage'
import RoutesPage        from './pages/admin/RoutesPage'
import AdminTicketsPage  from './pages/admin/AdminTicketsPage'
import UsersPage         from './pages/admin/UsersPage'

import TicketsPage       from './pages/customer/TicketsPage'
import BuyTicketPage     from './pages/customer/BuyTicketPage'

const queryClient = new QueryClient({
  defaultOptions: { queries: { retry: 1, staleTime: 30_000 } },
})

const Admin = ({ children }) => <ProtectedRoute adminOnly>{children}</ProtectedRoute>

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <Routes>
          {/* Públicas */}
          <Route path="/login"    element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />

          {/* Protegidas */}
          <Route element={<ProtectedRoute><AppLayout /></ProtectedRoute>}>

            {/* Home redireciona por perfil */}
            <Route path="/" element={<HomePage />} />

            {/* Admin */}
            <Route path="/admin/cities"   element={<Admin><CitiesPage /></Admin>} />
            <Route path="/admin/vehicles" element={<Admin><VehiclesPage /></Admin>} />
            <Route path="/admin/routes"   element={<Admin><RoutesPage /></Admin>} />
            <Route path="/admin/tickets"  element={<Admin><AdminTicketsPage /></Admin>} />
            <Route path="/admin/users"    element={<Admin><UsersPage /></Admin>} />

            {/* Cliente */}
            <Route path="/tickets"     element={<TicketsPage />} />
            <Route path="/tickets/buy" element={<BuyTicketPage />} />
          </Route>

          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </BrowserRouter>
    </QueryClientProvider>
  )
}
