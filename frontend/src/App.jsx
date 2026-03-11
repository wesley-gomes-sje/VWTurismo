import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'

import AppLayout      from './components/layout/AppLayout'
import ProtectedRoute from './components/layout/ProtectedRoute'

import LoginPage    from './pages/auth/LoginPage'
import RegisterPage from './pages/auth/RegisterPage'
import CitiesPage   from './pages/admin/CitiesPage'
import TicketsPage  from './pages/customer/TicketsPage'

const queryClient = new QueryClient({
  defaultOptions: { queries: { retry: 1, staleTime: 30_000 } },
})

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
            {/* Admin */}
            <Route path="/admin/cities"   element={<ProtectedRoute adminOnly><CitiesPage /></ProtectedRoute>} />

            {/* Cliente */}
            <Route path="/tickets" element={<TicketsPage />} />

            {/* Raiz → redireciona por perfil */}
            <Route path="/" element={<Navigate to="/tickets" replace />} />
          </Route>

          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </BrowserRouter>
    </QueryClientProvider>
  )
}
