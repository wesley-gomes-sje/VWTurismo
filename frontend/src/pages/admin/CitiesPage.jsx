import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { getCities, createCity, updateCity, deleteCity } from '../../api/cities'
import Card from '../../components/ui/Card'
import Button from '../../components/ui/Button'
import Input from '../../components/ui/Input'
import Alert from '../../components/ui/Alert'

export default function CitiesPage() {
  const qc = useQueryClient()
  const [name, setName]       = useState('')
  const [editing, setEditing] = useState(null) // { id, name }
  const [error, setError]     = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['cities'],
    queryFn: () => getCities().then((r) => r.data.data),
  })

  const create = useMutation({
    mutationFn: (n) => createCity({ name: n }),
    onSuccess: () => { qc.invalidateQueries(['cities']); setName('') },
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao criar cidade.'),
  })

  const update = useMutation({
    mutationFn: ({ id, name: n }) => updateCity(id, { name: n }),
    onSuccess: () => { qc.invalidateQueries(['cities']); setEditing(null) },
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao atualizar.'),
  })

  const remove = useMutation({
    mutationFn: (id) => deleteCity(id),
    onSuccess: () => qc.invalidateQueries(['cities']),
    onError: (e) => setError(e.response?.data?.message ?? 'Erro ao remover.'),
  })

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Cidades</h1>
      <Alert message={error} />

      <Card>
        <h2 className="font-semibold mb-3 text-gray-700">Nova cidade</h2>
        <div className="flex gap-2">
          <Input
            placeholder="Nome da cidade"
            value={name}
            onChange={(e) => setName(e.target.value)}
            className="flex-1"
          />
          <Button onClick={() => create.mutate(name)} disabled={!name || create.isPending}>
            Adicionar
          </Button>
        </div>
      </Card>

      <Card>
        {isLoading ? (
          <p className="text-gray-500">Carregando...</p>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left text-gray-600">
                <th className="pb-2">ID</th>
                <th className="pb-2">Nome</th>
                <th className="pb-2 text-right">Ações</th>
              </tr>
            </thead>
            <tbody>
              {data?.map((city) => (
                <tr key={city.id} className="border-b last:border-0 hover:bg-gray-50">
                  <td className="py-2 text-gray-400">{city.id}</td>
                  <td className="py-2">
                    {editing?.id === city.id ? (
                      <Input
                        value={editing.name}
                        onChange={(e) => setEditing({ ...editing, name: e.target.value })}
                        className="w-48"
                      />
                    ) : (
                      city.name
                    )}
                  </td>
                  <td className="py-2 flex gap-2 justify-end">
                    {editing?.id === city.id ? (
                      <>
                        <Button variant="primary" onClick={() => update.mutate(editing)}>Salvar</Button>
                        <Button variant="secondary" onClick={() => setEditing(null)}>Cancelar</Button>
                      </>
                    ) : (
                      <>
                        <Button variant="secondary" onClick={() => setEditing(city)}>Editar</Button>
                        <Button variant="danger" onClick={() => remove.mutate(city.id)}>Remover</Button>
                      </>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>
    </div>
  )
}
