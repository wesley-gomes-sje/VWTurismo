<?php

class SeedAdminUser
{
    public static function run($pdo)
    {
        $email = 'admin@vwturismo.com';

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo "Seed: usuário admin já existe, pulando.\n";
            return;
        }

        $password = password_hash('admin123', PASSWORD_BCRYPT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, profile, status)
             VALUES (?, ?, ?, 'admin', 1)"
        );
        $stmt->execute(['Admin', $email, $password]);

        echo "Seed: usuário admin criado com sucesso.\n";
        echo "  E-mail: {$email}\n";
        echo "  Senha:  admin123\n";
    }
}
