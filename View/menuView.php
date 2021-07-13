<?php
class menuView
{
    public function __construct()
    { }
    //DIRECIONAMENTO PARA TELAS
    public function telaAdm()
    {
        $conteudo = '<img src="./assets/logo.PNG" class="img">';
        include './View/Templates/templateAdm.php';
    }
    public function telaCliente()
    {
        $conteudo = '<img src="./assets/logo.PNG"> ';
        include './View/Templates/templateCliente.php';
    }


    //fORMULARIO DE SALVAR ONIBUS
    public function salvOnibus($mensagem, $data)
    {
        $conteudo = '<div class="FormEsquerda formbase">
        <form action="index.php?modulo=onibusController&metodo=salvOnibus" method="POST">
            <h1>Cadastrar</h1>
            ' . "{$mensagem}" . '<br>
            <input type="text" name="nomeOnibus" placeholder="Digite o nome do onibus" required />
            <input type="text" name="placa" placeholder="Digite a plca" required />
            <button>Salvar</button>
        </form>
    </div>
    <div class="FormDireita formbase">
    <h1>Listagem</h1>';
        $table = '<table>';
        $trH = '<tr>
            <th> Onibus </th>
             <th> Placa </th>
         </tr>';
        $conteudo .= $table . $trH;
        foreach ($data as $linha) {
            $listagem =  '<tr>';
            $listagem .= '<td>' . $linha['nomeOnibus'] . '</td>' .  '<td>' . $linha['placa'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        };
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }


    //fORMULARIO DE SALVAR CIDADES
    public function salvCidade($mensagem, $data)
    {
        $conteudo = '<div class="FormEsquerda formbase">
        <form action="index.php?modulo=cidadeController&metodo=salvCidade" method="POST">
        <h1>Cadastrar</h1>
            ' . "{$mensagem}" . '<br>
            <input type="text" name="nomeCidade" placeholder="Digite o nome da Cidade" required />
            <button>Salvar</button>
        </form>
    </div>
    <div class="FormDireita formbase">
    <h1>Listagem</h1>';
        $table = '<table class="cidades">';
        $trH = '<tr>
        <th> Cidades </th>
     </tr>';
        $conteudo .= $table . $trH;
        foreach ($data as $linha) {
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['nomeCidade'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }
    //fORMULARIO DE SALVAR TRAJETOS
    public function salvTrajeto($mensagem, $dataBD, $dataCidades)
    {
        $opcao = $this->listCidade($dataCidades);
        $conteudo = '<div class="FormEsquerda formbase">
            <form action="index.php?modulo=trajetoController&metodo=salvTrajeto" method="POST">
            <h1>Cadastrar</h1>
            ' . "{$mensagem}" . '<br>
            <select name="cidadeOrigem" placeholder="Cidade Origem" required>
            ' . $opcao . '
            </select>
            <select name="cidadeDestino" placeholder="Cidade Destino" required>
            ' . $opcao . '
            </select>
            <input id="distancia" type="number" name="distancia" placeholder="Distancia em KM" required />
            <button>Salvar</button>
            </form>
             </div>
             <div class="FormDireita formbase">
             <h1>Listagem</h1>';
             $table = '<table>';
             $trH = '<tr>
             <th> Origem </th>
             <th> Destino </th>
             <th> Distancia </th>
          </tr>';
          $conteudo .=$table . $trH;
          foreach($dataBD as $linha){
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['cidadeOrigem'] . '</td>' . '<td>' . $linha['cidadeDestino'] . '</td>' . '<td>' . $linha['distancia'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
          }
          $conteudo .= '</table>';
          $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }
    //LISTAGEM DAS OPÇÕES
    public function listCidade($dataCidades)
    {
        $opcao = '';
        foreach ($dataCidades as $linha) {
            $opcao .=  '<option value="' . $linha['idCidade'] . '">' . $linha['nomeCidade'] . '</option>';
        }
        return $opcao;
    }


    //COMPRAR PASSAGEM
    







































    //LISTAGEM DE CLIENTES
    public function listCliente($data)
    {
        $conteudo = '<div class="FormDireita formbase">
        <h1>Listagem</h1>';
        $table = '<table>';
        $trH = '<tr>
            <th> Clientes </th>
            <th> E-mail </th>
         </tr>';
        $conteudo .= $table . $trH;
        foreach ($data as $linha) {
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['nomeUsuario'] . '</td>' . '<td>' . $linha['emailUsuario'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }
}
