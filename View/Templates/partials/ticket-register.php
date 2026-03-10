<div class="FormPassagem formbase" style="height: auto">
    <form action="/tickets/register" method="POST">
        <h1>Comprar</h1>
        <?= $message ?><br>
        <p style="color:white">Escolha o onibus</p>
        <select name="vehicle" placeholder="Onibus" required>
            <?= $onibus ?>
        </select>
        <p style="color:white">Cidade Origem</p>
        <select name="origin" placeholder="Cidade Origem" required>
            <?= $cidade ?>
        </select>
        <p style="color:white">Cidade Destino</p>
        <select name="destination" placeholder="Cidade Destino" required>
            <?= $cidade ?>
        </select>
        <p style="color:white">Data</p>
        <input id="date" type="date" name="date" placeholder="Data da Viagem" style="width: 262.5px" />
        <button style="margin-bottom:15px; margin-top:2px">Comprar</button>
    </form>
</div>
<div class="FormDireita formbase" style="width: auto">
    <h1>Verificar</h1>
    <table class="tabelaVerifica" style="margin: 0px 35px;">
        <tr>
            <th>Data</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Distancia KM</th>
            <th>Preço</th>
        </tr>
        <?php foreach ($tickets as $item): ?>
        <tr>
            <td><?= (new DateTime($item['date']))->format('d/m/Y') ?></td>
            <td><?= $item['origin'] ?></td>
            <td><?= $item['destination'] ?></td>
            <td><?= $item['distance'] ?></td>
            <td>R$ <?= $item['price'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
