<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VWTurismo</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="shortcut icon" href="../assets/icone" />
</head>

<body>
    <div class="container">
        <div class="esquerda">
        <img style="margin-top: 50px;" src="https://img.icons8.com/wired/64/000000/bus.png"/>
        <p>Seja bem vindo</p>
            <div class="opcoes">
            <a href="/tickets/open">
                <input type="submit" value="Comprar Passagens"></a>
                <a href="/tickets/all">
                <input type="submit" value="Minhas Passagens"></a>
            </div>
            <a href="/logout">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                    width="50" height="50"
                    viewBox="0 0 172 172"
                    style=" fill:#000000;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,172v-172h172v172z" fill="none"></path><g fill="#ffffff"><path d="M85.94625,6.82625c-1.89469,0.04031 -3.41312,1.59906 -3.38625,3.49375v75.68c-0.01344,1.23625 0.63156,2.39188 1.70656,3.02344c1.075,0.61813 2.39187,0.61813 3.46687,0c1.075,-0.63156 1.72,-1.78719 1.70656,-3.02344v-75.68c0.01344,-0.92719 -0.34937,-1.8275 -1.00781,-2.48594c-0.65844,-0.65844 -1.55875,-1.02125 -2.48594,-1.00781zM62.08125,10.80375c-0.43,-0.01344 -0.86,0.05375 -1.24969,0.18813c-31.36313,10.52156 -53.95156,40.15125 -53.95156,75.00812c0,43.65844 35.46156,79.12 79.12,79.12c43.65844,0 79.12,-35.46156 79.12,-79.12c0,-34.85687 -22.58844,-64.48656 -53.95156,-75.00812c-1.80062,-0.60469 -3.74906,0.36281 -4.35375,2.16344c-0.60469,1.80062 0.36281,3.74906 2.17688,4.36719c28.60844,9.59438 49.24844,36.59031 49.24844,68.4775c0,39.93625 -32.30375,72.24 -72.24,72.24c-39.93625,0 -72.24,-32.30375 -72.24,-72.24c0,-31.88719 20.64,-58.88312 49.24844,-68.4775c1.59906,-0.52406 2.59344,-2.09625 2.35156,-3.7625c-0.22844,-1.65281 -1.6125,-2.9025 -3.27875,-2.95625z"></path></g></g></svg></a>
            <p style="color: white;">Sair</p>
        </div>
        
        <div class="direita">
            <?php echo $content ?>
        </div>
    </div>
    <script type="text/javascript" src="./Views/Templates/travardata.js"></script>
</body>
</html>