<?php

namespace App\Http\Controllers;

use App\Services\CompteService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

/**
 * @OA\Info(
 *     title="API de Gestion des Comptes",
 *     version="1.0.0",
 *     description="API pour la gestion des comptes bancaires"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8001/api",
 *     description="Serveur de développement"
 * )
 *
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes bancaires"
 * )
 */
class CompteController extends Controller
{
    use ApiResponser;

    protected CompteService $compteService;

    public function __construct(CompteService $compteService)
    {
        $this->compteService = $compteService;
    }

    /**
     * @OA\Get(
     *     path="/comptes",
     *     tags={"Comptes"},
     *     summary="Liste paginée des comptes",
     *     description="Récupère une liste paginée de tous les comptes avec possibilité de filtrage",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="http_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Liste des comptes récupérée avec succès"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
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

        // dd($comptes);

        return $this->successResponse($comptes, "Liste des comptes récupérée avec succès");
    }

    /**
     * @OA\Get(
     *     path="/comptes/{id}",
     *     tags={"Comptes"},
     *     summary="Affiche un compte spécifique",
     *     description="Récupère les détails d'un compte par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du compte",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="http_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Compte récupéré avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="http_code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Compte introuvable")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/comptes",
     *     tags={"Comptes"},
     *     summary="Crée un nouveau compte",
     *     description="Crée un nouveau compte bancaire",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numero_compte", "type", "solde_initial", "devise", "client_id"},
     *             @OA\Property(property="numero_compte", type="string", example="CB001234567890"),
     *             @OA\Property(property="type", type="string", enum={"cheque", "epargne", "courant"}, example="cheque"),
     *             @OA\Property(property="solde_initial", type="number", format="decimal", example=1000.00),
     *             @OA\Property(property="devise", type="string", example="FCFA"),
     *             @OA\Property(property="statut", type="string", example="actif"),
     *             @OA\Property(property="client_id", type="string", example="uuid-client"),
     *             @OA\Property(property="metadata", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="http_code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Compte créé avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="http_code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $compte = $this->compteService->create($request->all());
        return $this->successResponse($compte, "Compte créé avec succès", 201);
    }

    /**
     * @OA\Put(
     *     path="/comptes/{id}",
     *     tags={"Comptes"},
     *     summary="Met à jour un compte existant",
     *     description="Met à jour les informations d'un compte existant",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du compte",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="numero_compte", type="string", example="CB001234567890"),
     *             @OA\Property(property="type", type="string", enum={"cheque", "epargne", "courant"}, example="cheque"),
     *             @OA\Property(property="solde_initial", type="number", format="decimal", example=1000.00),
     *             @OA\Property(property="devise", type="string", example="FCFA"),
     *             @OA\Property(property="statut", type="string", example="actif"),
     *             @OA\Property(property="client_id", type="string", example="uuid-client"),
     *             @OA\Property(property="metadata", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="http_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Compte mis à jour avec succès"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="http_code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Impossible de mettre à jour le compte")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/comptes/{id}",
     *     tags={"Comptes"},
     *     summary="Supprime un compte",
     *     description="Supprime un compte existant (soft delete)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du compte",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="http_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Compte supprimé avec succès"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="http_code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Impossible de supprimer le compte")
     *         )
     *     )
     * )
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

/**
 * @OA\Schema(
 *     schema="Compte",
 *     type="object",
 *     title="Compte",
 *     description="Modèle représentant un compte bancaire",
 *     @OA\Property(property="id", type="string", format="uuid", description="Identifiant unique du compte"),
 *     @OA\Property(property="numero_compte", type="string", description="Numéro de compte généré automatiquement"),
 *     @OA\Property(property="type", type="string", enum={"cheque", "epargne", "courant"}, description="Type de compte"),
 *     @OA\Property(property="solde_initial", type="number", format="decimal", description="Solde de départ à la création du compte"),
 *     @OA\Property(property="devise", type="string", description="Devise du compte", example="FCFA"),
 *     @OA\Property(property="statut", type="string", description="Statut du compte", example="actif"),
 *     @OA\Property(property="client_id", type="string", format="uuid", description="ID du client titulaire"),
 *     @OA\Property(property="metadata", type="object", nullable=true, description="Données additionnelles"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de dernière modification"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, description="Date de suppression (soft delete)")
 * )
 *
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     title="Pagination",
 *     description="Informations de pagination",
 *     @OA\Property(property="currentPage", type="integer", description="Page actuelle"),
 *     @OA\Property(property="totalPages", type="integer", description="Nombre total de pages"),
 *     @OA\Property(property="totalItems", type="integer", description="Nombre total d'éléments"),
 *     @OA\Property(property="itemsPerPage", type="integer", description="Nombre d'éléments par page"),
 *     @OA\Property(property="hasNext", type="boolean", description="Indique s'il y a une page suivante"),
 *     @OA\Property(property="hasPrevious", type="boolean", description="Indique s'il y a une page précédente")
 * )
 */
