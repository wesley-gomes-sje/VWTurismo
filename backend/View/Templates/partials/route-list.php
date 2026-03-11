<div class="FormEsquerda formbase" style="height: auto">
    <form action="/route/register" method="POST">
        <h1>Cadastrar</h1>
        <?= $message ?><br>
        <p style="color:white">Cidade Origem</p>
        <select name="origin" placeholder="Cidade Origem" required>
            <?= $city ?>
        </select>
        <p style="color:white">Cidade Destino</p>
        <select name="destination" placeholder="Cidade Destino" required>
            <?= $city ?>
        </select>
        <input id="distance" type="number" name="distance" placeholder="Distancia em KM" required />
        <button style="margin-bottom:40px; margin-top:20px">Salvar</button>
    </form>
</div>
<div class="FormDireita formbase">
    <h1>Listagem</h1>
    <table>
        <tr>
            <th>Origem</th>
            <th>Destino</th>
            <th>Distancia</th>
        </tr>
        <?php foreach ($data as $line): ?>
        <tr>
            <td><?= $line['origin'] ?></td>
            <td><?= $line['destination'] ?></td>
            <td><?= $line['distance'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
