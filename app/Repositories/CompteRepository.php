<?php

namespace App\Repositories;

use App\Models\Compte;
use App\Repositories\BaseRepository;

class CompteRepository extends BaseRepository
{
    public function  __construct(Compte $model)
    {
        parent::__construct($model);
    }
}
