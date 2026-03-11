<div class="FormDireita formbase">
    <h1>Listagem</h1>
    <table>
        <tr>
            <th>Clientes</th>
            <th>E-mail</th>
        </tr>
        <?php foreach ($data as $linha): ?>
        <tr>
            <td><?= $linha['name'] ?></td>
            <td><?= $linha['email'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
