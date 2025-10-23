<?php

namespace App\Services;

use App\Repositories\CompteRepository;

class CompteService extends BaseService
{
    public function __construct(CompteRepository $repository)
    {
        parent::__construct($repository);
    }
}
