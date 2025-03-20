<?php

namespace App\Http\Controllers\Api;

use App\Models\Empresa;
use App\Traits\PageableTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresaController extends ApiController
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
        $builder = Empresa::filter($request->all());
        // apenas empresas com estabelecimentos
        $builder = $builder->has('estabelecimento');
//        $builder = $builder->has('socios');
        $builder->with(['estabelecimento', 'qsa']);
        $response = $this->paginate($request, $builder);
        return response()->json($response);
    }
}
