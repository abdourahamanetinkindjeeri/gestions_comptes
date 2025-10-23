<?php

namespace App\Traits;

use App\Enums\ResponseStatus;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponser
{
    protected function successResponse(mixed $data = null, string $message = "Opération réussie", int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->formatResponse(ResponseStatus::SUCCESS, $data, $message, $code);
    }

    protected function errorResponse(string $message = "Erreur interne", int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->formatResponse(ResponseStatus::ECHEC, null, $message, $code);
    }

    protected function sendResponse(
        mixed $data,
        string $message = "Opération réussie",
        int $code = Response::HTTP_OK,
        ResponseStatus $status = ResponseStatus::SUCCESS
    ): JsonResponse {
        return $this->formatResponse($status, $data, $message, $code);
    }

    private function formatResponse(ResponseStatus $status, mixed $data, string $message, int $code): JsonResponse
    {
        // Si $data est une collection paginée
        if ($data instanceof LengthAwarePaginator) {
            $pagination = [
                'currentPage' => $data->currentPage(),
                'totalPages' => $data->lastPage(),
                'totalItems' => $data->total(),
                'itemsPerPage' => $data->perPage(),
                'hasNext' => $data->hasMorePages(),
                'hasPrevious' => $data->currentPage() > 1,
            ];

            $links = [
                'self' => $data->url($data->currentPage()),
                'next' => $data->nextPageUrl(),
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
            ];

            return response()->json([
                'status' => $status->value,
                'http_code' => $code,
                'message' => $message,
                'data' => $data->items(), // seulement les éléments de la page
                'pagination' => $pagination,
                'links' => $links
            ], $code);
        }

        // Si $data est une collection simple
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        return response()->json([
            'status' => $status->value,
            'http_code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
