import { Navigate } from 'react-router-dom'
import { useAuthStore } from '../../store/authStore'

export default function ProtectedRoute({ children, adminOnly = false }) {
  const { user } = useAuthStore()

  if (!user) return <Navigate to="/login" replace />
  if (adminOnly && user.profile !== 'admin') return <Navigate to="/" replace />

  return children
}
