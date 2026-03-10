<div class="FormEsquerda formbase">
    <form action="/city/edit?id=<?= $id ?>" method="POST">
        <h1>Editar</h1>
        <input type="text" name="name" value="<?= $name ?>" required />
        <button>Salvar</button>
    </form>
</div>
