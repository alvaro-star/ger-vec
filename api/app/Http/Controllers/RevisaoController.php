<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Revisao;
use App\Http\Requests\StoreRevisaoRequest;
use App\Http\Requests\UpdateRevisaoRequest;
use App\Models\Pessoa;
use App\Models\Veiculo;
use App\Utils\TransformData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevisaoController extends Controller
{
    private array $sorter = ['data', 'veiculo', 'quilometragem', 'valor_total'];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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

        $queryBuilder->join('veiculos', 'veiculos.id', '=', 'revisoes.veiculo_id')
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

        return response()->json($revisao, 201);
    }

    public function countRevisoesByPessoa(Request $request)
    {
        // Realiza uma busca basica com base nos filtros das pessoas
        $pageable = new PageInput($request);
        $query = $pageable->getQuery();

        $queryBuilder = Pessoa::query();

        if (!in_array($pageable->getSort(), ['nome', 'cpf', 'n_revisoes'])) {
            $pageable->setSort('n_revisoes');
            $pageable->setDirection('desc');
        }
        $queryBuilder->orderBy($pageable->getSort(), $pageable->getDirection());


        $query_numbers = TransformData::extractNumbers($query);
        if ($query !== '')
            $queryBuilder->whereRaw('UPPER(nome) LIKE ?', ['%' . strtoupper($query) . '%'])
                ->orWhereRaw('cpf LIKE ?', ['%' . $query_numbers . '%']);
        $response =  $queryBuilder->getByPageable($pageable);


        // Com base nos resultados previos realiza uma busca da ultima revisao feita feita e da media das revisoes
        // Isto obriga ao banco de dados a realizar os calculos necessarios para apenas estas 10 pessoas
        $pessoaIds = $response->content->pluck('id')->toArray();

        $placeholders = implode(',', array_fill(0, count($pessoaIds), '?'));

        $query = "SELECT
            pessoa_id,
            MAX(data) AS last_revisao,
            AVG(diferenca_segundos) AS media_diferenca
        FROM (
            SELECT
                pessoa_id,
                data,
                EXTRACT(EPOCH FROM (data::timestamp - LAG(data::timestamp) OVER (PARTITION BY pessoa_id ORDER BY data))) AS diferenca_segundos
            FROM alvaro_vargas_alvarez.revisoes
            WHERE pessoa_id IN ($placeholders)
        ) AS sub
        GROUP BY pessoa_id;";

        $result = DB::select($query, $pessoaIds);


        //Com base nos resultados, a seguir se agrupam estes dados da segunda consulta como atributos
        // de cada objeto da primeira lista
        $mapIdData = [];
        foreach ($result as $item) {
            $key = $this->generateHashKey($item->pessoa_id);
            $mapIdData[$key] = ['media' => $item->media_diferenca, 'last_revisao' => $item->last_revisao];
        }

        foreach ($response->content as $item) {
            $key = $this->generateHashKey($item->id);

            if (isset($mapIdData[$key])) {
                $mediaSegundos = $mapIdData[$key]['media'] ?? null;
                $item->avg_tempo_revisoes = $mediaSegundos !== null ? round($mediaSegundos / 86400, 2) : 0;
                $item->last_revisao = $mapIdData[$key]['last_revisao'] ?? '';
            } else {
                $item->avg_tempo_revisoes = 0;
                $item->last_revisao = '';
            }
        }

        return response()->json($response, 200);
    }

    private function generateHashKey($int): string
    {
        return 'KEY' . strval($int);
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
        return response()->json([], 204);
    }
}
