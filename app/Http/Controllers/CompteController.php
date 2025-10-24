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
 *     url="https://gestions-comptes.onrender.com",
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
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de compte (cheque, epargne, courant)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"cheque", "epargne", "courant"})
     *     ),
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Statut du compte",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche globale dans les comptes",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Champ de tri",
     *         required=false,
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordre de tri (asc ou desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
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
        $filters = $request->all(); // récupère tous les query params
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);

        $comptes = $this->compteService->getAll($filters, $page, $limit);

        return $this->successResponse($comptes, "Liste des comptes récupérée avec succès");
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
