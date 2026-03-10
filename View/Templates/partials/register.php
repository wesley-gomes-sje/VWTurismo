<form action='/user/register' method='POST'>
    <h1>Cadastro</h1><br>
    <?= $message ?>
    <input type='text' name='name' id='name' placeholder='Digite o seu nome' required>
    <input type='email' name='email' id='email' placeholder='Digite o seu email' required><br>
    <input type='password' name='password' id='password' placeholder='Digite uma senha' required><br>
    <input type='password' name='confirmPassword' id='confirmPassword' placeholder='Confirme a senha' required><br>
    <button>Registrar</button><br>
    <a href='/login'><strong>Voltar</strong></a>
</form>
