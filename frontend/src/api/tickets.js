import api from './client'

export const getTickets  = ()     => api.get('/tickets')
export const getTicket   = (id)   => api.get(`/tickets/${id}`)
export const buyTicket   = (data) => api.post('/tickets', data)
