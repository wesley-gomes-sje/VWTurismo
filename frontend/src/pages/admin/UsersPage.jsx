import { useQuery } from '@tanstack/react-query'
import { getUsers } from '../../api/users'
import Card from '../../components/ui/Card'

export default function UsersPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['users'],
    queryFn: () => getUsers().then((r) => r.data.data),
  })

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Clientes</h1>

      <Card>
        {isLoading ? (
          <p className="text-gray-500">Carregando...</p>
        ) : !data?.length ? (
          <p className="text-gray-500">Nenhum cliente cadastrado.</p>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-600">
                <th className="pb-2">Nome</th>
                <th className="pb-2">E-mail</th>
              </tr>
            </thead>
            <tbody>
              {data.map((u, i) => (
                <tr key={i} className="border-b last:border-0 hover:bg-gray-50">
                  <td className="py-2 font-medium">{u.name}</td>
                  <td className="py-2 text-gray-600">{u.email}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
        {data?.length > 0 && (
          <p className="text-xs text-gray-400 mt-4">{data.length} cliente{data.length !== 1 ? 's' : ''} cadastrado{data.length !== 1 ? 's' : ''}</p>
        )}
      </Card>
    </div>
  )
}
