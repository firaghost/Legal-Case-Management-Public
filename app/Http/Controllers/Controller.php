<?php

namespace App\Http\Controllers;

use App\Traits\LogsActions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, LogsActions;

    /**
     * The number of items to show per page for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Get the number of items to show per page.
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        return request('per_page', $this->perPage);
    }

    /**
     * Get the sort parameters from the request.
     *
     * @return array
     */
    protected function getSortParams(): array
    {
        $sort = request('sort', 'id');
        $direction = request('direction', 'asc');

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'asc';
        }

        return [$sort, $direction];
    }

    /**
     * Return a successful JSON response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, string $message = '', int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $status
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = '', int $status = 400, array $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Return a not found JSON response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFound(string $message = 'Resource not found')
    {
        return $this->error($message, 404);
    }

    /**
     * Return an unauthorized JSON response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorized(string $message = 'Unauthorized')
    {
        return $this->error($message, 401);
    }

    /**
     * Return a forbidden JSON response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden(string $message = 'Forbidden')
    {
        return $this->error($message, 403);
    }
}






