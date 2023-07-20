<?php

namespace App\Repositories;

use App\Models\User;

class UsersRepository extends AbstractRepository
{
    public function __construct(User $userModel)
    {
        parent::__construct($userModel);
    }
}
