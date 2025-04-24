<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Veiculo;
use App\Http\Requests\StoreVeiculoRequest;
use App\Http\Requests\UpdateVeiculoRequest;
use App\Models\Pessoa;
use App\Models\Revisao;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageable = new PageInput($request);
        $sort = $request->query('sort') ?? ''; // Obtém o critério de ordenação

        $queryBuilder = Veiculo::join('pessoas', 'veiculos.pessoa_id', '=', 'pessoas.id');

        if (!empty($sort) && $sort == 'Proprietario') {
            $queryBuilder->orderBy('pessoas.nome', 'asc'); // Ordena por nome do proprietário
        }

        $response = $queryBuilder->getByPageable($pageable); // Aplica paginação

        return response()->json($response, 200);
    }

    // Exibe veículos ordenados por nome do proprietário
    public function findAllOrderByPessoa(Request $request)
    {
        $pageable = new PageInput($request);
        $response = Veiculo::join('pessoas', 'veiculos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome', 'asc')
            ->getByPageable($pageable);

        return response()->json($response, 200);
    }

    // Exibe a quantidade de veículos agrupados por marca
    public function nVeiculosGroupByMarca(Request $request)
    {
        return Veiculo::selectRaw('marca, COUNT(*) as total')
            ->groupBy('marca')
            ->orderBy('total', 'desc')
            ->get();
    }

    // Exibe a quantidade de veículos agrupados por sexo do proprietário e marca
    public function nVeiculosBySexoAndMarca(Request $request)
    {
        return Veiculo::join('pessoas', 'veiculos.pessoa_id', '=', 'pessoas.id')
            ->selectRaw('pessoas.is_masculino, veiculos.marca, COUNT(*) as total')
            ->groupBy('pessoas.is_masculino', 'veiculos.marca')
            ->orderBy('pessoas.is_masculino', 'desc')
            ->orderBy('total', 'desc')
            ->get();
    }

    // Exibe a quantidade de revisões agrupadas por marca de veículo
    public function nRevisoesGroupByMarca(Request $request)
    {
        return Veiculo::selectRaw('marca, SUM(n_revisoes) as total')
            ->groupBy('marca')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVeiculoRequest $request)
    {
        $data = $request->validated();
        $veiculo = new Veiculo();
        $veiculo->fill($data);
        $veiculo->pessoa_id = $data["pessoa_id"];
        $veiculo->save();

        $pessoa = Pessoa::find($data["pessoa_id"]);
        $pessoa->n_veiculos++;
        $pessoa->save();

        return response()->json($veiculo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Veiculo $veiculo)
    {
        $veiculo->pessoa = Pessoa::find($veiculo->pessoa_id);
        return response()->json($veiculo, 200);
    }

    // Exibe veículos de uma pessoa específica, com possibilidade de busca por placa
    public function veiculosByPessoa($id, Request $request)
    {
        $pageable = new PageInput($request);
        $search = $request->query('query');

        $query = Veiculo::where('pessoa_id', $id);

        if (!empty($search)) {
            $query->where('placa', 'like', '%' . $search . '%');
        }

        $response = $query->getByPageable($pageable);
        return response()->json($response, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVeiculoRequest $request, Veiculo $veiculo)
    {

        $veiculo_com_placa = Veiculo::where('placa', $request->placa)->first();
        if ($veiculo_com_placa != null && $veiculo_com_placa->id != $veiculo->id) {
            return response()->json([
                'message' => 'Erros no formulario',
                'errors' => [
                    'placa' => ['A placa já esta cadastrada']
                ]
            ], 422);
        }

        $veiculo->fill($request->validated());
        $veiculo->save();
        return response()->json($veiculo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veiculo $veiculo)
    {
        $revisoes = Revisao::where('veiculo_id', $veiculo->id)->limit(1)->get();

        if ($revisoes->isNotEmpty()) {
            return response()->json([
                'message' => 'Não é possível excluir este veículo. Existem revisões associadas a ele.'
            ], 400);
        }

        $pessoa_id = $veiculo->pessoa_id;
        $veiculo->delete();

        Pessoa::find($pessoa_id)?->decrement('n_veiculos');

        return response()->json([], 204);
    }
}
