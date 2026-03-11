import { useQuery } from '@tanstack/react-query'
import { getTickets } from '../../api/tickets'
import Card from '../../components/ui/Card'
import { Link } from 'react-router-dom'
import Button from '../../components/ui/Button'

export default function TicketsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['tickets'],
    queryFn: () => getTickets().then((r) => r.data.data),
  })

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-800">Minhas Passagens</h1>
        <Link to="/tickets/buy">
          <Button>Comprar passagem</Button>
        </Link>
      </div>

      <Card>
        {isLoading ? (
          <p className="text-gray-500">Carregando...</p>
        ) : !data?.length ? (
          <p className="text-gray-500">Você ainda não tem passagens.</p>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-600">
                <th className="pb-2">Origem</th>
                <th className="pb-2">Destino</th>
                <th className="pb-2">Data</th>
                <th className="pb-2">Preço</th>
                <th className="pb-2">Veículo</th>
              </tr>
            </thead>
            <tbody>
              {data.map((t, i) => (
                <tr key={i} className="border-b last:border-0 hover:bg-gray-50">
                  <td className="py-2">{t.origin}</td>
                  <td className="py-2">{t.destination}</td>
                  <td className="py-2">{t.date}</td>
                  <td className="py-2 font-medium">R$ {parseFloat(t.price).toFixed(2)}</td>
                  <td className="py-2">{t.brand} {t.model}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>
    </div>
  )
}
