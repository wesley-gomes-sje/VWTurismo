import api from './client'

export const getVehicles  = ()     => api.get('/vehicles')
export const createVehicle = (data) => api.post('/vehicles', data)
