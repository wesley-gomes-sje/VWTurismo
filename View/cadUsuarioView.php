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
        <input type='text' name='email' id='email' placeholder='Login' required><br>
        <input type='password' name='password' id='password' placeholder='Senha' required> <br>
        <button>Entrar</button><br>
        <a href='index.php?modulo=userController&metodo=fillFields'> Ainda não é cliente?<strong>Cadastre-se!</strong>
    </form>";
    include './View/Templates/usuarioTemplate.php';
    }

    public function formulario(string $mensagem)
    {
        $conteudo = "
        <form action='index.php?modulo=userController&metodo=register' method='POST'> 
         <h1>Cadastro</h1><br>
         {$mensagem}
            <input type='text' name='name' id='name' placeholder='Digite o seu nome' required>
            <input type='email' name='email' id='email'  placeholder='Digite o seu email'required><br>
            <input type='password' name='password' id='password' placeholder='Digite uma senha'required> <br>
            <input type='password' name='confirmPassword' id='confirmPassword' placeholder='Confirme a senha'required> <br>
            <button>Registrar</button><br>
            <a href='index.php'><strong>Voltar</strong>
        </form>
    ";
        include './View/Templates/usuarioTemplate.php';
    }
    
}
