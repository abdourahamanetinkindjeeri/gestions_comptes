<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * Champs autorisés pour la recherche textuelle.
     * (À redéfinir dans les repositories enfants si besoin)
     */
    protected array $searchable = [];

    /**
     * Champs autorisés pour le tri.
     */
    protected array $sortable = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Retourne les ressources avec pagination, filtres, recherche et tri.
     */
    public function all(array $filters = [], int $page = 1, int $limit = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // 🔹 Appliquer les filtres simples (ex: type, statut)
        foreach ($filters as $key => $value) {
            if (in_array($key, ['page', 'limit', 'search', 'sort', 'order'])) {
                continue;
            }

            if ($value !== null && $value !== '' && Schema::hasColumn($this->model->getTable(), $key)) {
                $query->where($key, $value);
            }
        }

        // 🔹 Recherche globale (si $this->searchable est défini)
        if (!empty($filters['search']) && !empty($this->searchable)) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'LIKE', "%$search%");
                }
            });
        }

        // 🔹 Tri
        $sort = $filters['sort'] ?? 'id';
        $order = strtolower($filters['order'] ?? 'desc');

        if (
            (Schema::hasColumn($this->model->getTable(), $sort) || in_array($sort, $this->sortable))
            && in_array($order, ['asc', 'desc'])
        ) {
            $query->orderBy($sort, $order);
        }

        // 🔹 Pagination
        $limit = min($limit, 100);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): ?Model
    {
        $model = $this->find($id);
        if (!$model) {
            return null;
        }
        $model->update($data);
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $model = $this->find($id);
        return $model ? $model->delete() : false;
    }
}
