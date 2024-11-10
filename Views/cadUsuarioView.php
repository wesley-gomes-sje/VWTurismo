<?php
class cadUsuarioView
{

    public function __construct()
    { 

    }
  
    public function formLogin(string $message){
        $content = "<form action='/login' method='POST'>
        <h1>Login</h1><br>
        {$message}
        <input type='text' name='email' id='email' placeholder='Login' required><br>
        <input type='password' name='password' id='password' placeholder='Senha' required> <br>
        <button>Entrar</button><br>
        <a href='/user/register'> Ainda não é cliente?<strong>Cadastre-se!</strong>
    </form>";
    include './Views/Templates/templateUser.php';
    }

    public function formRegister(string $message)
    {
        $content = "
        <form action='/register' method='POST'> 
         <h1>Cadastro</h1><br>
         {$message}
            <input type='text' name='name' id='name' placeholder='Digite o seu nome' required>
            <input type='email' name='email' id='email'  placeholder='Digite o seu email'required><br>
            <input type='password' name='password' id='password' placeholder='Digite uma senha'required> <br>
            <input type='password' name='confirm_password' id='confirmPassword' placeholder='Confirme a senha'required> <br>
            <button>Registrar</button><br>
            <a href='/login'><strong>Voltar</strong>
        </form>
    ";
        include './Views/Templates/templateUser.php';
    }
    
}
