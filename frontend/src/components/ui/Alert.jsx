export default function Alert({ message, type = 'error' }) {
  if (!message) return null
  const styles = {
    error:   'bg-red-50 border-red-400 text-red-700',
    success: 'bg-green-50 border-green-400 text-green-700',
    info:    'bg-blue-50 border-blue-400 text-blue-700',
  }
  return (
    <div className={`border-l-4 p-3 rounded text-sm ${styles[type]}`}>
      {message}
    </div>
  )
}
