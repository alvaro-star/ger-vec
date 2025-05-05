<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Revisao;
use App\Http\Requests\StoreRevisaoRequest;
use App\Http\Requests\UpdateRevisaoRequest;
use App\Models\Pessoa;
use App\Models\Veiculo;
use App\Utils\Redis\Entity\CacheEntityRequest;
use App\Utils\TransformData;
use Illuminate\Http\Request;

class RevisaoController extends Controller
{
    private array $sorter = ['data', 'veiculo', 'quilometragem', 'valor_total'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Revisao::loadCacheInfo($request, ['data_start', 'data_end', 'pessoa_id', 'veiculo_id'], ['revisoes']);

        $pageable = new PageInput($request);

        $data_start = $request->query('data_start');
        $data_start = TransformData::stringToDateTime($data_start);
        $data_end = $request->query('data_end');
        $data_end = TransformData::stringToDateTime($data_end);


        $queryBuilder = Revisao::query();

        if (!in_array($pageable->getSort(), $this->sorter)) {
            $pageable->setSort('data');
            $pageable->setDirection('desc');
        }

        $queryBuilder->orderBy($pageable->getSort(), $pageable->getDirection());

        $queryBuilder->leftJoin('veiculos', 'veiculos.id', '=', 'revisoes.veiculo_id')
            ->select('revisoes.*', 'veiculos.placa as veiculo');

        $pessoa_id = $request->query('pessoa_id') ?? '';
        $pessoa_id = TransformData::stringInteger($pessoa_id, 0);
        $veiculo_id = $request->query('veiculo_id') ?? '';
        $veiculo_id = TransformData::stringInteger($veiculo_id, 0);

        if ($pessoa_id > 0)
            $queryBuilder->where('revisoes.pessoa_id', $pessoa_id);
        if ($veiculo_id > 0)
            $queryBuilder->where('revisoes.veiculo_id', $veiculo_id);


        if ($data_start !== null && $data_end !== null) {
            $queryBuilder->whereBetween('data', [$data_start, $data_end]);
        } else if ($data_start !== null) {
            $queryBuilder->where('data', '>=', $data_start);
        } else if ($data_end !== null) {
            $queryBuilder->where('data', '<=', $data_end);
        }

        $response = $queryBuilder->getByPageable($pageable);
        return response()->json($response, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRevisaoRequest $request)
    {
        $dados = $request->validated();
        $revisao = new Revisao();
        $veiculo = Veiculo::where('placa', $dados['placa'])->first();

        $revisao->fill($dados);
        $revisao->veiculo_id =  $veiculo->id;
        $revisao->pessoa_id = $veiculo->pessoa_id;
        $revisao->save();

        $veiculo->n_revisoes++;
        $veiculo->pessoa->n_revisoes++;

        $veiculo->pessoa->save();
        $veiculo->save();

        $watcher = new CacheEntityRequest('revisoes');
        $watcher->alterNElements();

        return response()->json($revisao, 201);
    }

    public function countRevisoesByPessoa(Request $request)
    {
        Revisao::loadCacheInfo($request, ['query'], ['revisoes', 'pessoas']);

        $pageable = new PageInput($request);
        $query = $pageable->getQuery();

        $queryBuilder = Pessoa::query()
            ->leftJoin('cache_revisaos', 'pessoas.id', '=', 'cache_revisaos.pessoa_id')
            ->select('pessoas.*', 'cache_revisaos.last_revisao', 'cache_revisaos.avg_revisoes as media');

        if (!in_array($pageable->getSort(), ['nome', 'cpf', 'n_revisoes'])) {
            $pageable->setSort('n_revisoes');
            $pageable->setDirection('desc');
        }

        $queryBuilder->orderBy($pageable->getSort(), $pageable->getDirection());

        $query_numbers = TransformData::extractNumbers($query);
        if ($query !== '') {
            $queryBuilder->whereRaw('UPPER(nome) LIKE ?', ['%' . strtoupper($query) . '%']);
            if ($query_numbers != '')
                $queryBuilder->orWhereRaw('cpf LIKE ?', ['%' . $query_numbers . '%']);
        }

        $response =  $queryBuilder->getByPageable($pageable);

        foreach ($response->content as $item) {
            $mediaSegundos = $item->media ?? null;
            $item->avg_tempo_revisoes = $mediaSegundos !== null ? round($mediaSegundos / 86400, 2) : 0;
            $item->last_revisao = $item->last_revisao ?? '';
        }

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Revisao $reviso)
    {
        $reviso['veiculo'] = Veiculo::find($reviso->veiculo_id);
        return response()->json($reviso, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRevisaoRequest $request, Revisao $reviso)
    {
        $data = $request->validated();
        $reviso->fill($data);
        $reviso->save();

        $watcher = new CacheEntityRequest('revisoes');
        $watcher->updateElement();
        return response()->json($reviso, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revisao $reviso)
    {
        Veiculo::find($reviso->veiculo_id)->decrement('n_revisoes');
        Pessoa::find($reviso->pessoa_id)->decrement('n_revisoes');
        $reviso->delete();

        $watcher = new CacheEntityRequest('revisoes');
        $watcher->alterNElements();
        return response()->json([], 204);
    }
}
