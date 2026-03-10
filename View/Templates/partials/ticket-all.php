<div class="FormDireita formbase" style="width: auto; height: auto">
    <h1>Verificar</h1>
    <table class="tabelaVerifica" style="margin: 0px 35px; margin-bottom: 20px">
        <tr>
            <th>Data</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Placa</th>
            <th>Distancia KM</th>
            <th>Preço</th>
        </tr>
        <?php foreach ($tickets as $line): ?>
        <tr>
            <td><?= (new DateTime($line['date']))->format('d/m/Y') ?></td>
            <td><?= $line['origin'] ?></td>
            <td><?= $line['destination'] ?></td>
            <td><?= $line['brand'] ?></td>
            <td><?= $line['model'] ?></td>
            <td><?= $line['plate'] ?></td>
            <td><?= $line['distance'] ?></td>
            <td>R$ <?= $line['price'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
