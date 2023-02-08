<?php

namespace App\Traits;

use App\Models\V1\Role;

trait roleTrait
{
    use userTrait;

    public function getRoleName(int $id = null)
    {
        $id = $id ?? $this->getAuthenticatedUserId();

        return Role::where('id', $id)->first(['name'])->name;
    }
}
