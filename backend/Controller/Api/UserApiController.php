<?php

namespace App\Controller\Api;

use App\Model\User;

class UserApiController extends ApiController
{
    public function __construct(private ?User $user = null)
    {
        $this->user = $user ?? new User();
    }

    public function index(): void
    {
        $this->success($this->user->showCustomers());
    }

    public function show(string $id): void
    {
        $all   = $this->user->all();
        $found = array_values(array_filter($all, fn($u) => ($u['id'] ?? null) == $id));

        if (!$found) {
            $this->error('Usuário não encontrado.', 404);
            return;
        }

        $this->success($found[0]);
    }
}
