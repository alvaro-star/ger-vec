<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Pessoa;
use App\Http\Requests\StorePessoaRequest;
use App\Http\Requests\UpdatePessoaRequest;
use App\Models\Revisao;
use App\Models\Veiculo;
use App\Utils\TransformData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador responsável por gerenciar os dados da entidade Pessoa.
 */
class PessoaController extends Controller
{
    protected array $sorter = ['nome', 'celular', 'cpf', 'idade'];
    /**
     * Lista os registros de pessoas com suporte a paginação, filtro por nome e sexo.
     */
    public function index(Request $request)
    {

        $pageable = new PageInput($request);
        $query = $pageable->getQuery();


        $sexo = $request->query('sexo') ?? '';
        if (!in_array($sexo, ['M', 'F']))
            $sexo = '';

        $queryBuilder =  null;
        if (in_array($pageable->getSort(), $this->sorter))
            $queryBuilder = Pessoa::orderBy($pageable->getSort(), $pageable->getDirection());
        else
            $queryBuilder = Pessoa::orderBy('nome', 'asc');


        $query_numbers = TransformData::extractNumbers($query);
        if ($query !== '')
            $queryBuilder->whereRaw('LOWER(nome) LIKE ?', ['%' . strtolower($query) . '%'])
                ->orWhereRaw('cpf LIKE ?', ['%' . $query_numbers . '%'])
                ->orWhereRaw('celular LIKE ?', ['%' . $query_numbers . '%']);;

        if ($sexo !== '')
            $queryBuilder->where('is_masculino', $sexo === 'M');

        $response = $queryBuilder->getByPageable($pageable);
        return response()->json($response, 200);
    }

    /**
     * Retorna estatísticas agregadas das pessoas por sexo e no total.
     */
    public function statistics()
    {
        $result = Pessoa::selectRaw('
            is_masculino,
            SUM(n_veiculos) as n_veiculos,
            MIN(n_veiculos) as min_veiculos,
            MAX(n_veiculos) as max_veiculos,

            SUM(n_revisoes) as n_revisoes,
            MIN(n_revisoes) as min_revisoes,
            MAX(n_revisoes) as max_revisoes,

            COUNT(*) as n_elementos,
            SUM(idade) as soma_idades,
            MIN(idade) as min_idade,
            MAX(idade) as max_idade
        ')
            ->groupBy('is_masculino')
            ->get();

        // Inicializa os dados estatísticos com valores padrões
        $initStats = fn() => [
            'n_veiculos' => 0,
            'min_veiculos' => PHP_INT_MAX,
            'max_veiculos' => PHP_INT_MIN,
            'n_revisoes' => 0,
            'min_revisoes' => PHP_INT_MAX,
            'max_revisoes' => PHP_INT_MIN,
            'n_elementos' => 0,
            'soma_idades' => 0,
            'min_idade' => PHP_INT_MAX,
            'max_idade' => PHP_INT_MIN
        ];

        $response = [
            'M' => $initStats(),
            'F' => $initStats(),
            'Ambos' => $initStats()
        ];

        // Acumula os dados por sexo
        foreach ($result as $item) {
            $key = $this->getSexoKey($item->is_masculino);

            $response[$key]['n_veiculos'] += $item->n_veiculos;
            $response[$key]['min_veiculos'] = min($response[$key]['min_veiculos'], $item->min_veiculos);
            $response[$key]['max_veiculos'] = max($response[$key]['max_veiculos'], $item->max_veiculos);

            $response[$key]['n_revisoes'] += $item->n_revisoes;
            $response[$key]['min_revisoes'] = min($response[$key]['min_revisoes'], $item->min_revisoes);
            $response[$key]['max_revisoes'] = max($response[$key]['max_revisoes'], $item->max_revisoes);

            $response[$key]['n_elementos'] += $item->n_elementos;
            $response[$key]['soma_idades'] += $item->soma_idades;
            $response[$key]['min_idade'] = min($response[$key]['min_idade'], $item->min_idade);
            $response[$key]['max_idade'] = max($response[$key]['max_idade'], $item->max_idade);
        }

        // Calcula os totais agregados
        foreach (['n_veiculos', 'n_revisoes', 'n_elementos', 'soma_idades'] as $campo) {
            $response['Ambos'][$campo] = $response['M'][$campo] + $response['F'][$campo];
        }

        // Cálculo de mínimos e máximos agregados
        foreach (['min_veiculos', 'min_revisoes', 'min_idade'] as $campo) {
            $response['Ambos'][$campo] = min($response['M'][$campo], $response['F'][$campo]);
        }

        foreach (['max_veiculos', 'max_revisoes', 'max_idade'] as $campo) {
            $response['Ambos'][$campo] = max($response['M'][$campo], $response['F'][$campo]);
        }

        // Calcula as médias por grupo
        foreach (['M', 'F', 'Ambos'] as $key) {
            if ($response[$key]['n_elementos'] > 0) {
                $response[$key]['media_veiculos'] = round($response[$key]['n_veiculos'] / $response[$key]['n_elementos'], 2);
                $response[$key]['media_revisoes'] = round($response[$key]['n_revisoes'] / $response[$key]['n_elementos'], 2);
                $response[$key]['media_idades'] = round($response[$key]['soma_idades'] / $response[$key]['n_elementos'], 2);
            }
        }

        return response()->json($response, 200);
    }

    /**
     * Auxiliar para converter is_masculino em 'M' ou 'F'.
     */
    private function getSexoKey($is_masculino)
    {
        return $is_masculino ? 'M' : 'F';
    }

    /**
     * Retorna a soma total de veículos agrupada por sexo.
     */
    public function countVeiculosBySexo()
    {
        $response = Pessoa::selectRaw('SUM(n_veiculos) as total, is_masculino')
            ->groupBy('is_masculino')
            ->orderBy('total')
            ->get();
        return response()->json($response, 200);
    }

    /**
     * Cadastra uma nova pessoa no banco de dados.
     */
    public function store(StorePessoaRequest $request)
    {
        $pessoa = new Pessoa;
        $pessoa->fill($request->validated());
        $pessoa->is_masculino = $request->sexo === 'M';
        $pessoa->save();

        return response()->json($pessoa, 201);
    }

    /**
     * Retorna os dados de uma pessoa específica.
     */
    public function show(Pessoa $pessoa)
    {
        return response()->json($pessoa, 200);
    }

    /**
     * Atualiza os dados de uma pessoa existente.
     */
    public function update(UpdatePessoaRequest $request, Pessoa $pessoa)
    {
        $pessoa->fill($request->validated());
        $pessoa_with_cpf = Pessoa::where('cpf', $request->cpf)->first();
        if ($pessoa_with_cpf != null && $pessoa_with_cpf->id != $pessoa->id) {
            return response()->json([
                'message' => 'Erros no formulario',
                'errors' => ['cpf' => ['O cpf já está cadastrado']]
            ], 422);
        }

        $pessoa->is_masculino = $request->sexo === 'M';
        $pessoa->save();

        return response()->json($pessoa, 200);
    }

    /**
     * Remove uma pessoa, desde que não existam veículos ou revisões associadas.
     */
    public function destroy(Pessoa $pessoa)
    {
        $veiculos = Veiculo::where('pessoa_id', $pessoa->id)->limit(1)->get();
        $revisoes = Revisao::where('pessoa_id', $pessoa->id)->limit(1)->get();

        if ($veiculos->isNotEmpty() || $revisoes->isNotEmpty()) {
            return response()->json([
                'message' => 'Não é possível excluir. Existem veículos ou revisões associadas a esta pessoa.'
            ], 400);
        }

        $pessoa->delete();
        return response()->json([], 204);
    }
}
