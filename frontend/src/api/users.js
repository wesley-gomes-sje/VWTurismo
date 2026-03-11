import api from './client'

export const getUsers = ()   => api.get('/users')
export const getUser  = (id) => api.get(`/users/${id}`)
