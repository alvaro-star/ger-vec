<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Revisao;
use App\Http\Requests\StoreRevisaoRequest;
use App\Http\Requests\UpdateRevisaoRequest;

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
    public function show(Revisao $reviso)
    {
        return response()->json($reviso, 200);
    }

    public function revisoesByVeiculo($id, Request $request)
    {
        $pageable = new PageInput($request);
        $search = $request->query('query');

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
