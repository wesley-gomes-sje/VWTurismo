<div class="FormEsquerda formbase">
    <form action="/vehicle/register" method="POST">
        <h1>Cadastrar</h1>
        <?= $message ?><br>
        <input type="text" name="brand" placeholder="Digite a marca do veículo" required />
        <input type="text" name="model" placeholder="Digite o modelo do veículo" required />
        <input type="text" name="plate" maxlength="7" placeholder="Digite a placa do veículo" required />
        <input type="number" name="year" placeholder="Digite o ano do veículo" required />
        <button>Salvar</button>
    </form>
</div>
<div class="FormDireita formbase">
    <h1>Todos os Veículos</h1>
    <table class="tabelaVerifica" style="margin: 0px 35px;">
        <tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Placa</th>
            <th>Ano</th>
        </tr>
        <?php foreach ($data as $item): ?>
        <tr>
            <td><?= $item['brand'] ?></td>
            <td><?= $item['model'] ?></td>
            <td><?= $item['plate'] ?></td>
            <td><?= $item['year'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
