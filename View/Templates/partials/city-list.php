<div class="FormEsquerda formbase">
    <form action="/city/register" method="POST">
        <h1>Cadastrar</h1>
        <?= $message ?><br>
        <input type="text" name="name" placeholder="Digite o nome da Cidade" required />
        <button>Salvar</button>
    </form>
</div>
<div class="FormDireita formbase">
    <h1>Listagem</h1>
    <table>
        <tr>
            <th>Cidades</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($data as $item): ?>
        <tr>
            <td style="width:80px"><?= $item['name'] ?></td>
            <td>
                <a href="/city/show&id=<?= $item['id'] ?>">
                    <button style="width:100%" type="button">Editar</button>
                </a>
            </td>
            <td style="width:70px">
                <a href="/city/delete&id=<?= $item['id'] ?>">
                    <button style="width:100%" type="button">Excluir</button>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
