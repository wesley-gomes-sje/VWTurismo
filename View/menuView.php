<?php

namespace App\View;

class menuView
{
    public function admin(): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/admin-home.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function customer(): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/customer-home.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateCustomer.php';
    }

    public function createVehicle($message, $data): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/vehicles.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function registerCity($message, $data): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/city-list.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function editCity($data): void
    {
        $id   = $data[0]['id'];
        $name = $data[0]['name'];
        ob_start();
        include __DIR__ . '/Templates/partials/city-edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function registerRoute($message, $data, $cities): void
    {
        $city = $this->listCities($cities);
        ob_start();
        include __DIR__ . '/Templates/partials/route-list.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function registerTicket($message, $cities, $vehicles, $tickets): void
    {
        $onibus = $this->listVehicles($vehicles);
        $cidade = $this->listCities($cities);
        ob_start();
        include __DIR__ . '/Templates/partials/ticket-register.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateCustomer.php';
    }

    public function all($tickets): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/ticket-all.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateCustomer.php';
    }

    public function allTickets($data): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/ticket-admin.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function custormers($data): void
    {
        ob_start();
        include __DIR__ . '/Templates/partials/customers.php';
        $content = ob_get_clean();
        include __DIR__ . '/Templates/templateAdm.php';
    }

    public function listCities(array $data): string
    {
        $html = '';
        foreach ($data as $item) {
            $html .= '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
        }
        return $html;
    }

    public function listVehicles(array $data): string
    {
        $html = '';
        foreach ($data as $item) {
            $html .= '<option value="' . $item['id'] . '">' . $item['brand'] . '</option>';
        }
        return $html;
    }
}
