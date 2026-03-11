import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { getRoutes, createRoute } from '../../api/routes'
import { getCities } from '../../api/cities'
import Card from '../../components/ui/Card'
import Button from '../../components/ui/Button'
import Alert from '../../components/ui/Alert'

export default function RoutesPage() {
  const qc = useQueryClient()
  const [form, setForm] = useState({ origin: '', destination: '', distance: '' })
  const [error, setError] = useState('')

  const { data: routes, isLoading } = useQuery({
    queryKey: ['routes'],
    queryFn: () => getRoutes().then((r) => r.data.data),
  })

  const { data: cities = [] } = useQuery({
    queryKey: ['cities'],
    queryFn: () => getCities().then((r) => r.data.data),
  })

  const create = useMutation({
    mutationFn: (d) => createRoute(d),
    onSuccess: () => { qc.invalidateQueries(['routes']); setForm({ origin: '', destination: '', distance: '' }); setError('') },
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao cadastrar rota.'),
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    create.mutate({ ...form, distance: parseFloat(form.distance) })
  }

  const Select = ({ label, name, value, onChange }) => (
    <div className="flex flex-col gap-1">
      <label className="text-sm font-medium text-gray-700">{label}</label>
      <select
        value={value}
        onChange={onChange}
        required
        className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
      >
        <option value="">Selecione...</option>
        {cities.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
      </select>
    </div>
  )

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Rotas</h1>
      <Alert message={error} />

      <Card>
        <h2 className="font-semibold mb-4 text-gray-700">Nova rota</h2>
        <form onSubmit={handleSubmit} className="grid grid-cols-3 gap-3">
          <Select label="Origem"  name="origin"      value={form.origin}      onChange={(e) => setForm({ ...form, origin: e.target.value })} />
          <Select label="Destino" name="destination" value={form.destination} onChange={(e) => setForm({ ...form, destination: e.target.value })} />
          <div className="flex flex-col gap-1">
            <label className="text-sm font-medium text-gray-700">Distância (km)</label>
            <input
              type="number"
              step="0.1"
              min="1"
              value={form.distance}
              onChange={(e) => setForm({ ...form, distance: e.target.value })}
              required
              className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>
          <div className="col-span-3">
            <Button type="submit" disabled={create.isPending}>
              {create.isPending ? 'Cadastrando...' : 'Cadastrar Rota'}
            </Button>
          </div>
        </form>
      </Card>

      <Card>
        {isLoading ? (
          <p className="text-gray-500">Carregando...</p>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-600">
                <th className="pb-2">Origem</th>
                <th className="pb-2">Destino</th>
                <th className="pb-2">Distância</th>
                <th className="pb-2">Preço estimado</th>
              </tr>
            </thead>
            <tbody>
              {routes?.map((r, i) => (
                <tr key={i} className="border-b last:border-0 hover:bg-gray-50">
                  <td className="py-2">{r.origin}</td>
                  <td className="py-2">{r.destination}</td>
                  <td className="py-2">{r.distance} km</td>
                  <td className="py-2 font-medium">R$ {(r.distance * 0.5).toFixed(2)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>
    </div>
  )
}
