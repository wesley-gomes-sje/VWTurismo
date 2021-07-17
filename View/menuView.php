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
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;">';
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
        $table = '<table>';
        $trH = '<tr>
        <th> Cidades </th>
        <th> Ações </th>
     </tr>';
        $conteudo .= $table . $trH;
        foreach ($data as $linha) {
            $listagem = '<tr>';
            $listagem .= '<td style="width:80px">' . $linha['nomeCidade'] . '</td>'.'<td>'.'<a href="index.php?modulo=cidadeController&metodo=editar&id='.$linha['idCidade'] .'">
            <button style="width:100%" type="button" >Editar</button></a>' . '</td>' 
            .'<td style="width:70px">'.'<a href="index.php?modulo=cidadeController&metodo=excluir&id='.$linha['idCidade'] . '">
            <button style="width:100%" type="button">Excluir</button></a>' . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }



    
    //EDITAR A CIDADE FORM ABRE
        public function editarCidade($dados){
            $idCidade=$dados[0]['idCidade'];
            $nomeCidade=$dados[0]['nomeCidade'];
            $conteudo='<div class="FormEsquerda formbase">
            <form action="index.php?modulo=cidadeController&metodo=editarCidade&id='.$idCidade.'" method="POST">
            <h1>Editar</h1>
            <input type="text" name="nomeCidade" value=' . "{$nomeCidade}" . ' required />
            <button>Salvar</button>
            </form>
            </div>';
            include './View/Templates/templateAdm.php';
        }





    //fORMULARIO DE SALVAR TRAJETOS
    public function salvTrajeto($mensagem, $dataBD, $dataCidades)
    {
        $cidade = $this->listCidade($dataCidades);
        $conteudo = '<div class="FormEsquerda formbase"style="height: auto">
            <form action="index.php?modulo=trajetoController&metodo=salvTrajeto" method="POST">
            <h1>Cadastrar</h1>
            ' . "{$mensagem}" . '<br>
            <select name="cidadeOrigem" placeholder="Cidade Origem" required>
            ' . $cidade . '
            </select>
            <select name="cidadeDestino" placeholder="Cidade Destino" required>
            ' . $cidade . '
            </select>
            <input id="distancia" type="number" name="distancia" placeholder="Distancia em KM" required />
            <input id="preco" type="number" name="preco" placeholder="R$" required /> 
            <button style="margin-bottom:40px; margin-top:20px">Salvar</button>
            </form>
             </div>
             <div class="FormDireita formbase">
             <h1>Listagem</h1>';
             $table = '<table>';
             $trH = '<tr>
             <th> Origem </th>
             <th> Destino </th>
             <th> DistanciaKm </th>
             <th> Preço </th>
          </tr>';
          $conteudo .=$table . $trH;
          foreach($dataBD as $linha){
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['cidadeOrigem'] . '</td>' . '<td>' . $linha['cidadeDestino'] . '</td>' . '<td>' . $linha['distancia'] . '</td>' .'<td>' .'R$ ' . $linha['preco'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
          }
          $conteudo .= '</table>';
          $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }
  


    //COMPRAR PASSAGEM
    public function salvPassagem($mensagem, $dataCidades, $onibusDB,$passagemDB){
        $onibus = $this->listOnibus($onibusDB);
        $cidade = $this->listCidade($dataCidades);
        $conteudo = '<div class="FormPassagem formbase" style="height: auto">
        <form action="index.php?modulo=passagemController&metodo=salvPassagem" method="POST">
        <h1>Comprar</h1>
        ' . "{$mensagem}" . '<br>
        <select name="onibus" placeholder="Onibus" required>
        ' . $onibus . '
        </select>
        <select name="cidadeOrigem" placeholder="Cidade Origem" required>
            ' . $cidade . '
            </select>
            <select name="cidadeDestino" placeholder="Cidade Destino" required>
            ' . $cidade . '
            </select>
        <input id="dataPassagem" type="date" name="data" placeholder="Data da Viagem" style="width: 262.5px"/>
        <button style="margin-bottom:15px; margin-top:2px">Comprar</button>
        </form>
        </div>
        <div class="FormDireita formbase" style="width: auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;">';
        $trH = '<tr >
        <th> Data </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $conteudo .=$table . $trH;
        foreach($passagemDB as $linha){
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['data'] . '</td>' . '<td>' . $linha['cidadeOrigem'] . '</td>' . '<td>' . $linha['cidadeDestino'] . '</td>' .'<td>' .  $linha['distancia'] . '</td>' . '<td>' .'R$ ' . $linha['preco'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateCliente.php';
    }




    //LISTAGEM DE PASSAGENS CLIENTE
    public function listPassagem($dataPassagem){
        $conteudo = '<div class="FormDireita formbase" style="width: auto;height:auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;margin-bottom:20px">';
        $trH = '<tr >
        <th> Data </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Onibus </th>
        <th> Placa </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $conteudo .=$table . $trH;
        foreach($dataPassagem as $linha){
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['data'] . '</td>' . '<td>' . $linha['cidadeOrigem'] . '</td>' . '<td>' . $linha['cidadeDestino'] . '</td>' .'<td>' .$linha['nomeOnibus'] . '</td>' .'<td>' .$linha['placa'] . '</td>' .  '<td>' .  $linha['distancia'] . '</td>' . '<td>' .'R$ ' . $linha['preco'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateCliente.php';
    }



        //LISTAR TODAS AS PASSAGENS
    public function listTudo($dataTudo){
        $conteudo = '<div class="FormDireita formbase" style="width: auto;height:auto">
        <h1>Verificar</h1>';
        $table = '<table class="tabelaVerifica" style="margin: 0px 35px;margin-bottom:20px">';
        $trH = '<tr >
        <th> Data </th>
        <th> Cliente </th>
        <th> Origem </th>
        <th> Destino </th>
        <th> Onibus </th>
        <th> Placa </th>
        <th> Distancia KM</th>
        <th> Preço </th>
        </tr>';
        $conteudo .=$table . $trH;
        foreach($dataTudo as $linha){
            $listagem = '<tr>';
            $listagem .= '<td>' . $linha['data'] . '</td>' .'<td>' . $linha['nomeUsuario'] . '</td>' .  '<td>' . $linha['cidadeOrigem'] . '</td>' . '<td>' . $linha['cidadeDestino'] . '</td>' .'<td>' .$linha['nomeOnibus'] . '</td>' .'<td>' .$linha['placa'] . '</td>' .  '<td>' .  $linha['distancia'] . '</td>' . '<td>' .'R$ ' . $linha['preco'] . '</td>';
            $listagem .= '</tr>';
            $conteudo .= $listagem;
        }
        $conteudo .= '</table>';
        $conteudo .= '</div>';
        include './View/Templates/templateAdm.php';
    }



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




      //LISTAGEM DAS OPÇÕES
  public function listCidade($dataCidades)
  {
      $cidade = '';
      foreach ($dataCidades as $linha) {
          $cidade .=  '<option value="' . $linha['idCidade'] . '">' . $linha['nomeCidade'] . '</option>';
      }
      return $cidade;
  }
  public function listOnibus($onibusDB){
      $onibus = '';
      foreach ($onibusDB as $linha){
        $onibus .= '<option value="' . $linha['idOnibus'] . '">' .$linha['nomeOnibus'] . '</option>';
      }
      return $onibus;
  }
}
