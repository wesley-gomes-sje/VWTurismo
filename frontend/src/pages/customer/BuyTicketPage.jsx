import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation } from '@tanstack/react-query'
import { getCities } from '../../api/cities'
import { getVehicles } from '../../api/vehicles'
import { buyTicket } from '../../api/tickets'
import Card from '../../components/ui/Card'
import Button from '../../components/ui/Button'
import Alert from '../../components/ui/Alert'

const today = new Date().toISOString().split('T')[0]

export default function BuyTicketPage() {
  const navigate = useNavigate()
  const [form, setForm] = useState({ origin: '', destination: '', vehicle_id: '', date: today })
  const [error, setError]     = useState('')
  const [success, setSuccess] = useState('')

  const { data: cities = [] } = useQuery({
    queryKey: ['cities'],
    queryFn: () => getCities().then((r) => r.data.data),
  })

  const { data: vehicles = [] } = useQuery({
    queryKey: ['vehicles'],
    queryFn: () => getVehicles().then((r) => r.data.data),
  })

  const buy = useMutation({
    mutationFn: (data) => buyTicket(data),
    onSuccess: (res) => {
      const price = res.data.data?.price
      setSuccess(`Passagem comprada! Valor: R$ ${parseFloat(price).toFixed(2)}`)
      setTimeout(() => navigate('/tickets'), 2000)
    },
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao comprar passagem.'),
  })

  const set = (field) => (e) => {
    setError('')
    setForm({ ...form, [field]: e.target.value })
  }

  const SelectField = ({ label, field, options, labelKey = 'name', valueKey = 'id' }) => (
    <div className="flex flex-col gap-1">
      <label className="text-sm font-medium text-gray-700">{label}</label>
      <select
        value={form[field]}
        onChange={set(field)}
        required
        className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white"
      >
        <option value="">Selecione...</option>
        {options.map((o) => (
          <option key={o[valueKey]} value={o[valueKey]}>
            {typeof labelKey === 'function' ? labelKey(o) : o[labelKey]}
          </option>
        ))}
      </select>
    </div>
  )

  const handleSubmit = (e) => {
    e.preventDefault()
    if (form.origin === form.destination) {
      setError('Origem e destino não podem ser iguais.')
      return
    }
    buy.mutate({ ...form, vehicle_id: parseInt(form.vehicle_id) })
  }

  return (
    <div className="max-w-xl mx-auto space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Comprar Passagem</h1>

      <Card>
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Alert message={error} />
          <Alert message={success} type="success" />

          <SelectField label="Origem"  field="origin"      options={cities} />
          <SelectField label="Destino" field="destination" options={cities} />
          <SelectField
            label="Veículo"
            field="vehicle_id"
            options={vehicles}
            valueKey="id"
            labelKey={(v) => `${v.brand} ${v.model} — ${v.plate}`}
          />

          <div className="flex flex-col gap-1">
            <label className="text-sm font-medium text-gray-700">Data da viagem</label>
            <input
              type="date"
              min={today}
              value={form.date}
              onChange={set('date')}
              required
              className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>

          <Button type="submit" disabled={buy.isPending || !!success}>
            {buy.isPending ? 'Processando...' : 'Confirmar Compra'}
          </Button>
        </form>
      </Card>

      <p className="text-sm text-gray-500 text-center">
        O preço é calculado automaticamente com base na distância da rota (R$ 0,50/km).
      </p>
    </div>
  )
}
