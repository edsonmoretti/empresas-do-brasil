<?php

namespace App\Http\Controllers\Api;

use App\Models\Empresa;
use App\Models\Socio;
use App\Traits\PageableTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocioController extends ApiController
{

    use PageableTrait;

    /**
     * Display a listing of the resource in JSON format with Pagination.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $builder = Socio::filter($request->all());
        $response = $this->paginate($request, $builder);
        return response()->json($response);
    }
}
