<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * Injection du modèle Eloquent.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Retourne toutes les ressources avec pagination.
     *
     * @param int $page  Numéro de page (par défaut 1)
     * @param int $limit Nombre d'éléments par page (par défaut 10)
     * @return LengthAwarePaginator
     */
    public function all(int $page = 1, int $limit = 10): LengthAwarePaginator
    {
        return $this->model->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Trouve une ressource par son identifiant.
     */
    public function find(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Crée une nouvelle ressource.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Met à jour une ressource existante.
     */
    public function update(int|string $id, array $data): Model
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }

    /**
     * Supprime une ressource.
     */
    public function delete(int|string $id): bool
    {
        $model = $this->find($id);
        return $model->delete();
    }
}
