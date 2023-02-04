<?php

namespace App\RepositoryInterface;

interface UserRepositoryInterface
{
    public function all($data = null);

    public function show($data);

    public function store($request, $data);

    public function update($request, $user);

    public function delete($user);
}
