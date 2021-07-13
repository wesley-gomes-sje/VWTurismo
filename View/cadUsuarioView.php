<?php
class cadUsuarioView
{

    public function __construct()
    { 

    }
  
    public function formLogin(string $mensagem){
        $conteudo = "<form action='index.php?modulo=loginController&metodo=login' method='POST'>
        <h1>Entrar</h1><br>
        {$mensagem}
        <input type='text' name='emailUsuario' id='emailUsuario' placeholder='Login' required><br>
        <input type='password' name='senha' id='senha' placeholder='Senha' required> <br>
        <button>Entrar</button><br>
        <a href='index.php?modulo=usuarioController&metodo=preencheCampos'> Ainda não é cliente?<strong>Cadastre-se!</strong>
    </form>";
    include './View/Templates/usuarioTemplate.php';
    }

    public function formulario(string $mensagem)
    {
        $conteudo = "
        <form action='index.php?modulo=usuarioController&metodo=cadUsuario' method='POST'> 
         <h1>Cadastro</h1><br>
         {$mensagem}
            <input type='text' name='nomeUsuario' id='nomeUsuario' placeholder='Digite o seu nome' required>
            <input type='email' name='emailUsuario' id='emailUsuario'  placeholder='Digite o seu email'required><br>
            <input type='password' name='senha' id='senha' placeholder='Digite uma senha'required> <br>
            <input type='password' name='senhaConfirma' id='senhaConfirma' placeholder='Confirme a senha'required> <br>
            <button>Registrar</button><br>
            <a href='index.php'><strong>Voltar</strong>
        </form>
    ";
        include './View/Templates/usuarioTemplate.php';
    }
    
}
