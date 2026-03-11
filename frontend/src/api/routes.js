import api from './client'

export const getRoutes  = ()     => api.get('/routes')
export const createRoute = (data) => api.post('/routes', data)
