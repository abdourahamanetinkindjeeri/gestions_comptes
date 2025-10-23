<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Classe de service générique pour centraliser la logique commune
 * à toutes les entités manipulées par un repository.
 */
abstract class BaseService
{
    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Récupère toutes les ressources avec pagination.
     */
    public function getAll(int $page = 1, int $limit = 10): LengthAwarePaginator
    {
        return $this->repository->all($page, $limit);
    }

    /**
     * Trouve une ressource par son identifiant.
     */
    public function getById(int|string $id): Model
    {
        return $this->repository->find($id);
    }

    /**
     * Crée une nouvelle ressource.
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Met à jour une ressource existante.
     */
    public function update(int|string $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Supprime une ressource.
     */
    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }
}
