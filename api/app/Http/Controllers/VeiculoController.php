<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Veiculo;
use App\Http\Requests\StoreVeiculoRequest;
use App\Http\Requests\UpdateVeiculoRequest;
use App\Models\Marca;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Redis;
use App\Models\Revisao;
use App\Utils\Redis\CacheRequest;
use App\Utils\Redis\Entity\CacheEntityRequest;
use App\Utils\TransformData;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{

    protected array $sorter = ['proprietario', 'placa', 'renavam', 'ano', 'marca', 'modelo'];

    public function index(Request $request)
    {
        Veiculo::loadCacheInfo($request, ['query', 'pessoa_id', 'marca_id'], ['veiculos', 'pessoas']);
        $pageable = new PageInput($request);

        $queryBuilder = Veiculo::join('pessoas', 'veiculos.pessoa_id', '=', 'pessoas.id')
            ->leftJoin('marcas', 'veiculos.marca_id', '=', 'marcas.id')
            ->select('veiculos.*', 'pessoas.nome as proprietario', 'marcas.nome as marca');

        if (!in_array($pageable->getSort(), $this->sorter)) {
            $pageable->setSort('placa');
            $pageable->setDirection('asc');
        }

        $queryBuilder->orderBy($pageable->getSort(), $pageable->getDirection());

        $pessoa_id = TransformData::stringInteger($request->query('pessoa_id'), 0);
        if ($pessoa_id > 0) $queryBuilder->where('pessoas.id', $pessoa_id);

        $marca_id = TransformData::stringInteger($request->query('marca_id'), 0);
        if ($marca_id > 0) $queryBuilder->where('marcas.id', $marca_id);

        $query = strtoupper($pageable->getQuery());
        if ($query != '') {
            $queryBuilder->whereRaw('UPPER(placa) LIKE ?', ['%' . $query . '%'])
                ->orWhereRaw('UPPER(pessoas.nome) LIKE ?', ['%' . $query . '%'])
                ->orWhereRaw('UPPER(renavam) LIKE ?', ['%' . $query . '%'])
                ->orWhereRaw('UPPER(marcas.nome) LIKE ?', ['%' . $query . '%'])
                ->orWhereRaw('UPPER(modelo) LIKE ?', ['%' . $query . '%']);
        }


        $response = $queryBuilder->getByPageable($pageable);
        return response()->json($response, 200)->header('Cache-Control', 'public, max-age=2');
    }

    public function listPlacas(Request $request)
    {
        $cache = new CacheRequest($request);
        $cache->loadFromCache();
        $placas = null;
        if ($cache->getObject() == null) {
            $placas = Veiculo::select('placa')->orderBy('placa')->get();
            $placas = $placas->map(function ($veiculo) {
                return $veiculo->placa;
            });
            $cache->setObject($placas);
            $cache->saveCache(1800);
        } else {
            $placas = $cache->getObject();
        }
        return response()->json($placas)->header('Cache-Control', 'public, max-age=3');
    }

    // Exibe a quantidade de veículos agrupados por marca
    public function nVeiculosGroupByMarca()
    {
        $response = Veiculo::join('marcas', 'veiculos.marca_id', '=', 'marcas.id')
            ->selectRaw('marcas.nome as marca, COUNT(*) as total')
            ->groupBy('marcas.id')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($response, 200);
    }

    // Exibe a quantidade de veículos agrupados por sexo do proprietário e marca
    public function nVeiculosBySexoAndMarca()
    {
        $response = Veiculo::join('pessoas', 'veiculos.pessoa_id', '=', 'pessoas.id')
            ->leftJoin('marcas', 'veiculos.marca_id', '=', 'marcas.id')
            ->selectRaw('pessoas.is_masculino, marcas.nome as marca, COUNT(*) as total')
            ->groupBy('pessoas.is_masculino', 'marcas.nome')
            ->orderBy('pessoas.is_masculino', 'desc')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($response, 200);
    }

    // Exibe a quantidade de revisões agrupadas por marca de veículo
    public function nRevisoesGroupByMarca()
    {
        $response =  Veiculo::join('marcas', 'veiculos.marca_id', '=', 'marcas.id')
            ->selectRaw('marcas.nome as marca, SUM(n_revisoes) as total')
            ->groupBy('marcas.id')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json($response, 200);
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
        $veiculo->marca_id = $data["marca_id"];

        $veiculo->save();
        $pessoa = Pessoa::find($data["pessoa_id"]);
        $pessoa->n_veiculos++;
        $pessoa->save();

        $watcher = new CacheEntityRequest('veiculos');
        $watcher->alterNElements();
        return response()->json($veiculo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Veiculo $veiculo)
    {
        $veiculo['pessoa'] = Pessoa::find($veiculo->pessoa_id);
        $veiculo['marca'] = Marca::find($veiculo->marca_id);
        return response()->json($veiculo, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVeiculoRequest $request, Veiculo $veiculo)
    {
        $errorsPlaca = $veiculo->uniqueKeyIsOcuped('placa', $request->placa);
        $errorsRenavam = $veiculo->uniqueKeyIsOcuped('renavam', $request->renavam);
        $errors = [];

        if (count($errorsPlaca)  > 0)
            $errors['placa'] = $errorsPlaca;

        if (count($errorsRenavam)  > 0)
            $errors['renavam'] = $errorsRenavam;

        if (count($errors) > 0)
            return response()->json([
                'message' => 'Erros no formulario',
                'errors' => $errors
            ], 422);

        $veiculo->fill($request->validated());
        $veiculo->save();

        $watcher = new CacheEntityRequest('veiculos');
        $watcher->updateElement();

        return response()->json($veiculo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veiculo $veiculo)
    {
        $revisao = Revisao::where('veiculo_id', $veiculo->id)->first();

        if ($revisao) {
            return response()->json([
                'message' => 'Não é possível excluir este veículo. Existem revisões associadas a ele.'
            ], 400);
        }

        $pessoa_id = $veiculo->pessoa_id;

        $veiculo->delete();

        Pessoa::find($pessoa_id)?->decrement('n_veiculos');

        $watcher = new CacheEntityRequest('veiculos');
        $watcher->alterNElements();
        return response()->json([], 204);
    }
}
