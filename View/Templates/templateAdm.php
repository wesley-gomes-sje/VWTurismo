<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VWTurismo</title>
    <link rel="stylesheet" href="./CSS/menu.css">
    <link rel="shortcut icon" href="./assets/icone" />
</head>

<body>
    <div class="container">
        <div class="esquerda">
            <div class="opcoes">
                <a href="index.php?modulo=onibusController&metodo=Abrir">
                    <input type="submit" value="Cadastrar Onibus"></a>
                <a href="index.php?modulo=cidadeController&metodo=Abrir">
                    <input type="submit" value="Cadastrar Cidade"> </a>
                <a href="index.php?modulo=trajetoController&metodo=Abrir">
                <input type="submit" value="Cadastrar Trajetos"> </a>
                <input type="submit" value="Controle Passagens">
                <a href="index.php?modulo=clienteController&metodo=listCliente">
                <input type="submit" value="Controle Clientes"> </a>
            </div>
        </div>
        <div class="direita">
            <?php echo $conteudo ?>
        </div>
    </div>
</body>

</html>