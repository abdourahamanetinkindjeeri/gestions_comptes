<?php

namespace App\Http\Controllers;

use App\Services\CompteService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class CompteController extends Controller
{
    use ApiResponser; // ⚡️ On utilise le trait ici

    protected CompteService $compteService;

    public function __construct(CompteService $compteService)
    {
        $this->compteService = $compteService;
    }

    /**
     * Liste paginée des comptes
     */
    public function index(Request $request)
    {
        $comptes = $this->compteService->getAll(
            page: (int) $request->get('page', 1),
            limit: (int) $request->get('limit', 10),
            // type: $request->get('type'),      // optionnel : filtrage par type
            // statut: $request->get('statut'),  // optionnel : filtrage par statut
            // search: $request->get('search'),  // optionnel : recherche par titulaire ou numéro
            // sort: $request->get('sort', 'dateCreation'),
            // order: $request->get('order', 'desc')
        );

        // ⚡️ Utilisation du trait pour la réponse
        return $this->successResponse($comptes, "Liste des comptes récupérée avec succès");
    }

    /**
     * Affiche un compte spécifique
     */
    public function show(int|string $id)
    {
        $compte = $this->compteService->getById($id);

        if (!$compte) {
            return $this->errorResponse("Compte introuvable", 404);
        }

        return $this->successResponse($compte, "Compte récupéré avec succès");
    }

    /**
     * Crée un nouveau compte
     */
    public function store(Request $request)
    {
        $compte = $this->compteService->create($request->all());
        return $this->successResponse($compte, "Compte créé avec succès", 201);
    }

    /**
     * Met à jour un compte existant
     */
    public function update(Request $request, int|string $id)
    {
        $compte = $this->compteService->update($id, $request->all());

        if (!$compte) {
            return $this->errorResponse("Impossible de mettre à jour le compte", 404);
        }

        return $this->successResponse($compte, "Compte mis à jour avec succès");
    }

    /**
     * Supprime un compte
     */
    public function destroy(int|string $id)
    {
        $deleted = $this->compteService->delete($id);

        if (!$deleted) {
            return $this->errorResponse("Impossible de supprimer le compte", 404);
        }

        return $this->successResponse(null, "Compte supprimé avec succès");
    }
}
