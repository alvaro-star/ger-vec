<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
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
        $veiculo = Veiculo::where('placa', $dados['placa'])->first();
        $revisao->veiculo_id =  $veiculo->id;
        $revisao->pessoa_id = $veiculo->pessoa_id;
        $revisao->save();

        return response()->json($revisao, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Revisao $reviso)
    {
        $reviso->veiculo = Veiculo::find($reviso->veiculo_id);
        return response()->json($reviso, 200);
    }

    public function revisoesByVeiculo($id, Request $request)
    {
        $pageable = new PageInput($request);
        $query = Revisao::where('veiculo_id', $id)->orderBy('data', 'desc');
        $response = $query->getByPageable($pageable);
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRevisaoRequest $request, Revisao $reviso)
    {
        $reviso->fill($request->validated());
        $reviso->save();
        return response()->json($reviso, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revisao $reviso)
    {
        $reviso->delete();
        return response()->json([], 204);
    }
}
