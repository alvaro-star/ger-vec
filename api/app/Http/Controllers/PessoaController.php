<?php

namespace App\Http\Controllers;

use App\DTOS\PageInput;
use App\Models\Pessoa;
use App\Http\Requests\StorePessoaRequest;
use App\Http\Requests\UpdatePessoaRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageable = new PageInput($request);

        $query = $request->query('query') ?? '';
        $sexo = $request->query('sexo') ?? '';

        if (!in_array($sexo, ['M', 'F']))
            $sexo = '';


        $queryBuilder = Pessoa::orderBy('nome', 'asc');

        if ($query !== '')
            $queryBuilder->where('nome', 'like', $query . '%');

        if ($sexo !== '')
            $queryBuilder->where('is_masculino', $sexo === 'M');

        $response = $queryBuilder->getByPageable($pageable);
        return response()->json($response, 200);
    }

    public function statistics()
    {
        $result = Pessoa::selectRaw('is_masculino,
        SUM(n_veiculos) as n_veiculos,
        SUM(n_revisoes) as n_revisoes,
        COUNT(*) as n_elementos,
        SUM(idade) as soma_idades')
            ->groupBy('is_masculino')
            ->get();


        $response['M'] = ['n_veiculos' => 0, 'n_revisoes' => 0, 'n_elementos' => 0, 'soma_idades' => 0];
        $response['F'] = ['n_veiculos' => 0, 'n_revisoes' => 0, 'n_elementos' => 0, 'soma_idades' => 0];
        $response['Ambos'] = ['n_veiculos' => 0, 'n_revisoes' => 0, 'n_elementos' => 0, 'soma_idades' => 0];

        foreach ($result as $item) {
            $key  = $this->getSexoKey($item->is_masculino);
            $response[$key]['n_veiculos'] += $item->n_veiculos;
            $response[$key]['n_revisoes'] += $item->n_revisoes;
            $response[$key]['n_elementos'] += $item->n_elementos;
            $response[$key]['soma_idades'] += $item->soma_idades;
        }


        $response['Ambos']['n_veiculos'] = $response['M']['n_veiculos'] + $response['F']['n_veiculos'];
        $response['Ambos']['n_revisoes'] = $response['M']['n_revisoes'] + $response['F']['n_revisoes'];
        $response['Ambos']['n_elementos'] = $response['M']['n_elementos'] + $response['F']['n_elementos'];
        $response['Ambos']['soma_idades'] = $response['M']['soma_idades'] + $response['F']['soma_idades'];


        foreach (['M', 'F', 'Ambos'] as $key) {
            if ($response[$key]['n_elementos'] > 0) {
                $response[$key]['media_veiculos'] = round($response[$key]['n_veiculos'] / $response[$key]['n_elementos'], 2);
                $response[$key]['media_revisoes'] = round($response[$key]['n_revisoes'] / $response[$key]['n_elementos'], 2);
                $response[$key]['media_idades'] = round($response[$key]['soma_idades'] / $response[$key]['n_elementos'], 2);
            }
        }

        return response()->json($response, 200);
    }

    private function getSexoKey($is_masculino)
    {
        return $is_masculino ? 'M' : 'F';
    }

    /**
     * List n_veiculos by sexo.
     */
    public function countVeiculosBySexo()
    {
        return Pessoa::selectRaw('SUM(n_veiculos) as total, is_masculino')
            ->groupBy('is_masculino')
            ->orderBy('total')
            ->get();
    }


    /**
     * Store a newly created resource in storage.
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
