<?php
require_once __DIR__ . '/../connection.php';

require_once __DIR__ . '/migrations/create_table_vehicles.php';
require_once __DIR__ . '/migrations/create_table_tickets.php';
require_once __DIR__ . '/migrations/create_table_cities.php';
require_once __DIR__ . '/migrations/create_table_routes.php';
require_once __DIR__ . '/migrations/create_table_users.php';

echo "Iniciando a criação de todas as tabelas...\n";
$connection = new Connection();
$pdo = $connection->connect();

if ($pdo) {
    CreateUsersTable::up($pdo);
    CreateCitiesTable::up($pdo);
    CreateVehiclesTable::up($pdo);
    CreateRoutesTable::up($pdo);
    CreateTicketsTable::up($pdo);
    echo "Todas as tabelas foram criadas com sucesso.\n";
}
else {
    echo "Falha ao conectar ao banco de dados. Migrações não realizadas.\n";
}