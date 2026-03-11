<?php

namespace App\Controller\Api;

use App\Model\User;

class UserApiController extends ApiController
{
    public function index(): void
    {
        $users = (new User())->showCustomers();
        $this->success($users);
    }

    public function show(string $id): void
    {
        $user = new User($id);
        $all  = $user->all();

        $found = array_values(array_filter($all, fn($u) => $u['id'] ?? null == $id));

        if (!$found) {
            $this->error('Usuário não encontrado.', 404);
            return;
        }

        $this->success($found[0]);
    }
}
