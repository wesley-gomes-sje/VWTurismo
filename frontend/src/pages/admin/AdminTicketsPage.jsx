import { useQuery } from '@tanstack/react-query'
import { getTickets } from '../../api/tickets'
import Card from '../../components/ui/Card'

export default function AdminTicketsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['tickets'],
    queryFn: () => getTickets().then((r) => r.data.data),
  })

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Todas as Passagens</h1>

      <Card>
        {isLoading ? (
          <p className="text-gray-500">Carregando...</p>
        ) : !data?.length ? (
          <p className="text-gray-500">Nenhuma passagem cadastrada.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b text-left text-gray-600">
                  <th className="pb-2 pr-4">Passageiro</th>
                  <th className="pb-2 pr-4">Origem</th>
                  <th className="pb-2 pr-4">Destino</th>
                  <th className="pb-2 pr-4">Data</th>
                  <th className="pb-2 pr-4">Preço</th>
                  <th className="pb-2 pr-4">Veículo</th>
                  <th className="pb-2">Placa</th>
                </tr>
              </thead>
              <tbody>
                {data.map((t, i) => (
                  <tr key={i} className="border-b last:border-0 hover:bg-gray-50">
                    <td className="py-2 pr-4 font-medium">{t.name}</td>
                    <td className="py-2 pr-4">{t.origin}</td>
                    <td className="py-2 pr-4">{t.destination}</td>
                    <td className="py-2 pr-4">{t.date?.split(' ')[0]}</td>
                    <td className="py-2 pr-4 font-medium text-green-700">
                      R$ {parseFloat(t.price).toFixed(2)}
                    </td>
                    <td className="py-2 pr-4">{t.brand} {t.model}</td>
                    <td className="py-2 font-mono text-gray-500">{t.plate}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>

      {data?.length > 0 && (
        <p className="text-sm text-gray-500 text-right">
          Total: {data.length} passagem{data.length !== 1 ? 's' : ''} •{' '}
          Receita: R$ {data.reduce((s, t) => s + parseFloat(t.price), 0).toFixed(2)}
        </p>
      )}
    </div>
  )
}
