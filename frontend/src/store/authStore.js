import { create } from 'zustand'

const stored = () => {
  try {
    return JSON.parse(localStorage.getItem('user'))
  } catch {
    return null
  }
}

export const useAuthStore = create((set) => ({
  user:  stored(),
  token: localStorage.getItem('token') ?? null,

  setAuth: (user, token) => {
    localStorage.setItem('user',  JSON.stringify(user))
    localStorage.setItem('token', token)
    set({ user, token })
  },

  logout: () => {
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    set({ user: null, token: null })
  },

  isAdmin: () => stored()?.profile === 'admin',
}))
