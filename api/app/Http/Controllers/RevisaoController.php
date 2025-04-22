<?php

namespace App\Http\Controllers;

use App\Helpers\DTOS\PageInput;
use App\Models\Revisao;
use App\Http\Requests\StoreRevisaoRequest;
use App\Http\Requests\UpdateRevisaoRequest;
use App\Models\Veiculo;
use Illuminate\Http\Request;

class RevisaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageable = new PageInput($request);
        $response = Revisao::findAllByPageable($pageable);
        return response()->json($response, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRevisaoRequest $request)
    {
        $dados = $request->validated();
        $revisao = new Revisao();
        $revisao->fill($dados);
        $revisao->veiculo_id = $dados->veiculo_id;
        $revisao->save();

        return response()->json($revisao, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Revisao $revisao)
    {
        return response()->json($revisao, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRevisaoRequest $request, Revisao $revisao)
    {
        $revisao->fill($request->validated());
        $revisao->save();

        return response()->json($revisao, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revisao $revisao)
    {
        $revisao->delete();
        return response()->json([], 204);
    }
}
