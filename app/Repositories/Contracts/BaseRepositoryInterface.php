<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all(int $page = 1, int $limit = 10);

    public function find(int|string $id);

    public function create(array $data);

    public function update(int|string $id, array $data);

    public function delete(int|string $id);
}
