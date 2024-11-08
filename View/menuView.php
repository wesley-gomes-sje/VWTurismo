<?php
class menuView
{
    public function __construct() {}
    //DIRECIONAMENTO PARA TELAS
    public function admin()
    {
        $content = '<img src="../assets/logo.PNG" class="img">';
        include './View/Templates/templateAdm.php';
    }
    public function customer()
    {
        $content = '<img src="../assets/logo.PNG"> ';
        include './View/Templates/templateCustomer.php';
    }


    //fORMULARIO DE SALVAR ONIBUS
    public function createVehicle($message, $data)
    {
        $content = '<div class="FormEsquerda formbase">
        <form action="/vehicle/register" method="POST">
            <h1>Cadastrar</h1>
            ' . "{$message}" . '<br>
            <input type="text" name="brand" placeholder="Digite a marca do veículo" required />
            <input type="text" name="model" placeholder="Digite o modelo do veículo" required />
            <input type="text" name="plate" maxlength="7" placeholder="Digite a placa do veículo" required />
            <input type="number" name="year" placeholder="Digite o ano do veículo" required />
            <button>Salvar</button>
        </form>
    </div>
    <div class="FormDireita formbase">
    <h1>Todos os Veículos</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;">';
        $trH = '<tr>
            <th> Marca </th>
            <th> Modelo </th>
            <th> Placa </th>
            <th> Ano </th>
         </tr>';
        $content .= $table . $trH;
        foreach ($data as $item) {
            $listing =  '<tr>';
            $listing .= '<td>' . $item['brand'] . '</td>' . '<td>' . $item['model'] . '</td>' .  '<td>' . $item['plate'] . '</td>' .'<td>' . $item['year'] . '</td>';
            $listing .= '</tr>';
            $content .= $listing;
        };
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateAdm.php';
    }



    //fORMULARIO DE SALVAR CIDADES
    public function registerCity($message, $data)
    {
        $content = '<div class="FormEsquerda formbase">
        <form action="/city/register" method="POST">
        <h1>Cadastrar</h1>
            ' . "{$message}" . '<br>
            <input type="text" name="name" placeholder="Digite o nome da Cidade" required />
            <button>Salvar</button>
        </form>
    </div>
    <div class="FormDireita formbase">
    <h1>Listagem</h1>';
        $table = '<table>';
        $trH = '<tr>
        <th> Cidades </th>
        <th> Ações </th>
     </tr>';
        $content .= $table . $trH;
        foreach ($data as $item) {
            $listing = '<tr>';
            $listing .= '<td style="width:80px">' . $item['name'] . '</td>' . '<td>' . '<a href="/city/show&id=' . $item['id'] . '">
            <button style="width:100%" type="button" >Editar</button></a>' . '</td>'
                . '<td style="width:70px">' . '<a href="/city/delete&id=' . $item['id'] . '">
            <button style="width:100%" type="button">Excluir</button></a>' . '</td>';
            $listing .= '</tr>';
            $content .= $listing;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateAdm.php';
    }




    //EDITAR A CIDADE FORM ABRE
    public function editCity($data)
    {
        $id = $data[0]['id'];
        $name = $data[0]['name'];
        $content = '<div class="FormEsquerda formbase">
            <form action="/city/edit&id=' . $id . '" method="POST">
            <h1>Editar</h1>
            <input type="text" name="name" value="' . $name . '" required />
            <button>Salvar</button>
            </form>
            </div>';
        include './View/Templates/templateAdm.php';
    }





    //fORMULARIO DE SALVAR TRAJETOS
    public function registerRoute($message, $data, $cities)
    {

        
        $city = $this->listCities($cities);
        $content = '<div class="FormEsquerda formbase"style="height: auto">
            <form action="/route/register" method="POST">
            <h1>Cadastrar</h1>
            ' . "{$message}" . '<br>
            <p style="color:white">Cidade Origem</p>
            <select name="origin" placeholder="Cidade Origem" required>
            ' . $city . '
            </select>
            <p style="color:white">Cidade Destino</p>
            <select name="destination" placeholder="Cidade Destino" required>
            ' . $city . '
            </select>
            <input id="distance" type="number" name="distance" placeholder="Distancia em KM" required />
            <button style="margin-bottom:40px; margin-top:20px">Salvar</button>
            </form>
             </div>
             <div class="FormDireita formbase">
             <h1>Listagem</h1>';
        $table = '<table>';
        $trH = '<tr>
             <th> Origem </th>
             <th> Destino </th>
             <th> Distancia </th>
          </tr>';
        $content .= $table . $trH;
        foreach ($data as $line) {
            $list = '<tr>';
            $list .= '<td>' . $line['origin'] . '</td>' . '<td>' . $line['destination'] . '</td>' . '<td>' . $line['distance'] . '</td>';
            $list .= '</tr>';
            $content .= $list;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateAdm.php';
    }



    //COMPRAR PASSAGEM
    public function registerTicket($message, $cities, $vehicles, $tickets)
    {
        $onibus = $this->listVehicles($vehicles);
        $cidade = $this->listCities($cities);
        $content = '<div class="FormPassagem formbase" style="height: auto">
        <form action="/tickets/register" method="POST">
        <h1>Comprar</h1>
        ' . "{$message}" . '<br>
        <p style="color:white">Escolha o onibus</p>
        <select name="vehicle" placeholder="Onibus" required>
        ' . $onibus . '
        </select>
        <p style="color:white">Cidade Origem</p>
        <select name="origin" placeholder="Cidade Origem" required>
            ' . $cidade . '
            </select>
            <p style="color:white">Cidade Destino</p>
            <select name="destination" placeholder="Cidade Destino" required>
            ' . $cidade . '
            </select>
            <p style="color:white">Data</p>
        <input id="date" type="date" name="date" placeholder="Data da Viagem" style="width: 262.5px"/>
        <button style="margin-bottom:15px; margin-top:2px">Comprar</button>
        </form>
        </div>
        <div class="FormDireita formbase" style="width: auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;">';
        $trH = '<tr >
        <th> Data </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $content .= $table . $trH;
        foreach ($tickets as $item) {
            $listing = '<tr>';
            $listing .= '<td>' . (new DateTime($item['date']))->format('d/m/Y') . '</td>' . '<td>' . $item['origin'] . '</td>' . '<td>' . $item['destination'] . '</td>' . '<td>' .  $item['distance'] . '</td>' . '<td>' . 'R$ ' . $item['price'] . '</td>';
            $listing .= '</tr>';
            $content .= $listing;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateCustomer.php';
    }




    //LISTAGEM DE PASSAGENS CLIENTE
    public function all($tickets)
    {
        $content = '<div class="FormDireita formbase" style="width: auto;height:auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;margin-bottom:20px">';
        $trH = '<tr >
        <th> Data </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Marca </th>
        <th> Modelo </th>
        <th> Placa </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $content .= $table . $trH;
        foreach ($tickets as $line) {
            $listing = '<tr>';
            $listing .= '<td>' . (new DateTime($line['date']))->format('d/m/Y')  . '</td>' . '<td>' . $line['origin'] . '</td>' . '<td>' . $line['destination'] . '</td>' . '<td>' . $line['brand'] . '</td>' . '<td>' . $line['model'] . '</td>' . '<td>' . $line['plate'] . '</td>' .  '<td>' .  $line['distance'] . '</td>' . '<td>' . 'R$ ' . $line['price'] . '</td>';
            $listing .= '</tr>';
            $content .= $listing;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateCustomer.php';
    }



    //LISTAR TODAS AS PASSAGENS
    public function allTickets($data)
    {
        $content = '<div class="FormDireita formbase" style="width: auto;height:auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;margin-bottom:20px">';
        $trH = '<tr >
        <th> Data </th>
        <th> Cliente </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Onibus </th>
        <th> Placa </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $content .= $table . $trH;
        foreach ($data as $line) {
            $listing = '<tr>';
            $listing .= '<td>' . (new DateTime($line['date']))->format('d/m/Y') . '</td>' . '<td>' . $line['name'] . '</td>' .  '<td>' . $line['origin'] . '</td>' . '<td>' . $line['destination'] . '</td>' . '<td>' . $line['brand'] . '</td>' .'<td>' . $line['model'] . '</td>' . '<td>' . $line['plate'] . '</td>' .  '<td>' .  $line['distance'] . '</td>' . '<td>' . 'R$ ' . $line['price'] . '</td>';
            $listing .= '</tr>';
            $content .= $listing;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateAdm.php';
    }



    //LISTAGEM DE CLIENTES
    public function custormers($data)
    {
        $content = '<div class="FormDireita formbase">
        <h1>Listagem</h1>';
        $table = '<table>';
        $trH = '<tr>
            <th> Clientes </th>
            <th> E-mail </th>
         </tr>';
        $content .= $table . $trH;
        foreach ($data as $linha) {
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['name'] . '</td>' . '<td>' . $linha['email'] . '</td>';
            $listagem .= '</tr>';
            $content .= $listagem;
        }
        $content .= '</table>';
        $content .= '</div>';
        include './View/Templates/templateAdm.php';
    }




    //LISTAGEM DAS OPÇÕES
    public function listCities($data)
    {
        $city = '';
        foreach ($data as $item) {
            $city .=  '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
        }
        return $city;
    }
    
    public function listVehicles($data)
    {
        $vehicle = '';
        foreach ($data as $item) {
            $vehicle .= '<option value="' . $item['id'] . '">' . $item['brand'] . '</option>';
        }
        return $vehicle;
    }
}
