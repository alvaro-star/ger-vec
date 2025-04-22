<?php

namespace App\Http\Controllers;

use App\Helpers\DTOS\PageInput;
use App\Models\Pessoa;
use App\Http\Requests\StorePessoaRequest;
use App\Http\Requests\UpdatePessoaRequest;

use Illuminate\Http\Request;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageable = new PageInput($request);
        $response = Pessoa::findAllByPageable($pageable);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePessoaRequest $request)
    {
        $pessoa = new Pessoa;
        $pessoa->fill($request->validated()); // já validado
        $pessoa->is_masculino = $request->sexo === 'M';
        $pessoa->save();

        return response()->json($pessoa, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pessoa $pessoa)
    {
        return response()->json($pessoa, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePessoaRequest $request, Pessoa $pessoa)
    {
        $pessoa->fill($request->validated()); // já validado
        $pessoa->is_masculino = $request->sexo === 'M';
        $pessoa->save();

        return response()->json($pessoa, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pessoa $pessoa)
    {
        $pessoa->delete();
        return response()->json([], 204);
    }
}
