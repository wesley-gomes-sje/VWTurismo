<form action='/login/login' method='POST'>
    <h1>Login</h1><br>
    <?= $message ?>
    <input type='text' name='email' id='email' placeholder='Login' required><br>
    <input type='password' name='password' id='password' placeholder='Senha' required><br>
    <button>Entrar</button><br>
    <a href='/user/register'>Ainda não é cliente? <strong>Cadastre-se!</strong></a>
</form>
