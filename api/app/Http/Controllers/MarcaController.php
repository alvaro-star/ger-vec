<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Marca;
use App\Http\Requests\StoreMarcaRequest;
use App\Http\Requests\UpdateMarcaRequest;
use App\Models\Veiculo;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    private array $sorter = ['nome', 'ano_fundacao', 'pais'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageable = new PageInput($request);
        $query = $pageable->getQuery();
        $queryBuilder = Marca::query();

        if (!in_array($pageable->getSort(), $this->sorter)) {
            $pageable->setSort('nome');
            $pageable->setDirection('asc');
        }

        $queryBuilder->orderBy($pageable->getSort(), $pageable->getDirection());

        if ($query !== '') {
            $queryBuilder
                ->whereRaw('UPPER(nome) LIKE ?', ['%' . strtoupper($query) . '%'])
                ->orWhereRaw('UPPER(pais) LIKE ?', ['%' . strtoupper($query) . '%']);
        }

        $response = $queryBuilder->getByPageable($pageable);
        return response()->json($response, 200);
    }

    public function groupByPais(){
        
        $queryBuilder =  Marca::query();    
        $response = $queryBuilder->selectRaw("pais, COUNT(*) as total")
            ->groupBy('pais')
            ->orderBy('total', 'desc')
            ->get();
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarcaRequest $request)
    {
        $data = $request->validated();
        $marca = new Marca();
        $marca->fill($data);
        $marca->save();
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */

    public function show(Marca $marca)
    {
        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarcaRequest $request, Marca $marca)
    {
        $data = $request->validated();
        $marca->fill($data);

        $errors = $marca->uniqueKeyIsOcuped('nome', $marca->nome);

        if (count($errors) > 0) {
            return response()->json([
                'message' => 'Houve erros no formulario',
                'errors' => ['nome' => $errors]
            ], 422);
        }

        $marca->save();
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        $veiculos = Veiculo::where('marca_id', $marca->id)->first();

        if ($veiculos) {
            return response()->json([
                'message' => 'Não é possível excluir esta marca. Existem veículos associados a ela.'
            ], 400);
        }

        $marca->delete();
        return response()->json([], 204);
    }
}
