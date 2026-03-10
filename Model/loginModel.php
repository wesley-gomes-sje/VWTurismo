<?php

use App\Database\Connection;

class Login
{
    private $email;
    private $password;
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::getInstance();
    }

    public function getEmail()        { return $this->email; }
    public function setEmail($email)  { $this->email = $email; }

    public function getPassword()           { return $this->password; }
    public function setPassword($password)  { $this->password = $password; }

    /**
     * Verifica credenciais e retorna os dados do usuário ou false.
     * Não gerencia sessão — responsabilidade do controller.
     *
     * @return array|false  Array com id, email, profile, name ou false se inválido.
     */
    public function login($email, $password)
    {
        try {
            $sql = $this->pdo->prepare('SELECT * FROM users WHERE email = :email;');
            $sql->bindValue(':email', $email);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $user = $sql->fetchAll(PDO::FETCH_ASSOC)[0];

                if (password_verify($password, $user['password'])) {
                    return $user;
                }
            }

            error_log('Login::login — usuário ou senha incorretos.');
            return false;
        } catch (PDOException $e) {
            error_log('Login::login — ' . $e->getMessage());
            return false;
        }
    }
}
