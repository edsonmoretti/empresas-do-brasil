<?php

namespace App\Http\Controllers\Api;

use App\Models\Estabelecimento;
use App\Traits\PageableTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstabelecimentoController extends ApiController
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
        $builder = Estabelecimento::filter($request->all());
        $builder->with('municipio');
        $response = $this->paginate($request, $builder);
        return response()->json($response);
    }
}
