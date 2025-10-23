<?php

namespace App\Repositories;

use App\Models\Compte;
use App\Repositories\BaseRepository;

class CompteRepository extends BaseRepository
{
    /**
     * Champs autorisés pour la recherche textuelle.
     */
    protected array $searchable = ['numero_compte', 'type', 'devise'];

    /**
     * Champs autorisés pour le tri.
     */
    protected array $sortable = ['numero_compte', 'type', 'solde_initial', 'statut', 'created_at', 'updated_at'];

    public function __construct(Compte $model)
    {
        parent::__construct($model);
    }
}
