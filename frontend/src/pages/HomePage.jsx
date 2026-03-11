import { useAuthStore } from '../store/authStore'
import { Navigate } from 'react-router-dom'

export default function HomePage() {
  const { user } = useAuthStore()
  if (!user) return <Navigate to="/login" replace />
  return <Navigate to={user.profile === 'admin' ? '/admin/cities' : '/tickets'} replace />
}
