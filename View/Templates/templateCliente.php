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
            <a href="index.php?modulo=passagemController&metodo=Abrir">
                <input type="submit" value="Comprar Passagens"></a>
                <input type="submit" value="Minhas Passagens">
            </div>
        </div>
        <div class="direita">
            <?php echo $conteudo ?>
        </div>
    </div>
</body>

</html>