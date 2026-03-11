<?php

namespace Tests\Unit\View;

use App\View\menuView;
use PHPUnit\Framework\TestCase;

class menuViewTest extends TestCase
{
    private menuView $view;

    protected function setUp(): void
    {
        $this->view = new menuView();
    }

    public function testListCitiesGeneratesOptions(): void
    {
        $data = [
            ['id' => 1, 'name' => 'São Paulo'],
            ['id' => 2, 'name' => 'Rio de Janeiro'],
        ];

        $result = $this->view->listCities($data);

        $this->assertStringContainsString('<option value="1">São Paulo</option>', $result);
        $this->assertStringContainsString('<option value="2">Rio de Janeiro</option>', $result);
    }

    public function testListCitiesEmptyArray(): void
    {
        $this->assertSame('', $this->view->listCities([]));
    }

    public function testListVehiclesGeneratesOptions(): void
    {
        $data = [
            ['id' => 1, 'brand' => 'Mercedes'],
            ['id' => 2, 'brand' => 'Volvo'],
        ];

        $result = $this->view->listVehicles($data);

        $this->assertStringContainsString('<option value="1">Mercedes</option>', $result);
        $this->assertStringContainsString('<option value="2">Volvo</option>', $result);
    }

    public function testListVehiclesEmptyArray(): void
    {
        $this->assertSame('', $this->view->listVehicles([]));
    }
}
