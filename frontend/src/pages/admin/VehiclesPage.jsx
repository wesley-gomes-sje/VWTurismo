import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { getVehicles, createVehicle } from '../../api/vehicles'
import Card from '../../components/ui/Card'
import Button from '../../components/ui/Button'
import Input from '../../components/ui/Input'
import Alert from '../../components/ui/Alert'

const empty = { brand: '', model: '', plate: '', year: '' }

export default function VehiclesPage() {
  const qc = useQueryClient()
  const [form, setForm] = useState(empty)
  const [error, setError] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['vehicles'],
    queryFn: () => getVehicles().then((r) => r.data.data),
  })

  const create = useMutation({
    mutationFn: (data) => createVehicle(data),
    onSuccess: () => { qc.invalidateQueries(['vehicles']); setForm(empty); setError('') },
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao cadastrar veículo.'),
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    create.mutate(form)
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Veículos</h1>
      <Alert message={error} />

      <Card>
        <h2 className="font-semibold mb-4 text-gray-700">Novo veículo</h2>
        <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-3">
          <Input label="Marca"  value={form.brand} onChange={(e) => setForm({ ...form, brand: e.target.value })} required />
          <Input label="Modelo" value={form.model} onChange={(e) => setForm({ ...form, model: e.target.value })} required />
          <Input label="Placa"  value={form.plate} onChange={(e) => setForm({ ...form, plate: e.target.value })} required />
          <Input label="Ano" type="number" value={form.year}  onChange={(e) => setForm({ ...form, year: e.target.value })}  required />
          <div className="col-span-2">
            <Button type="submit" disabled={create.isPending}>
              {create.isPending ? 'Cadastrando...' : 'Cadastrar Veículo'}
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
                <th className="pb-2">Marca</th>
                <th className="pb-2">Modelo</th>
                <th className="pb-2">Placa</th>
                <th className="pb-2">Ano</th>
              </tr>
            </thead>
            <tbody>
              {data?.map((v) => (
                <tr key={v.id} className="border-b last:border-0 hover:bg-gray-50">
                  <td className="py-2">{v.brand}</td>
                  <td className="py-2">{v.model}</td>
                  <td className="py-2 font-mono">{v.plate}</td>
                  <td className="py-2">{v.year}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>
    </div>
  )
}
