import api from './client'

export const getCities    = ()           => api.get('/cities')
export const createCity   = (data)       => api.post('/cities', data)
export const updateCity   = (id, data)   => api.put(`/cities/${id}`, data)
export const deleteCity   = (id)         => api.delete(`/cities/${id}`)
