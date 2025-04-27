-- Função: index(Request $request)
-- Busca veículos, com join na tabela de pessoas, e possível ordenação por nome do proprietário
-- O método getByPageable aplica paginação via LIMIT e OFFSET
SELECT veiculos.*
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
ORDER BY pessoas.nome ASC -- (se sort == 'Proprietario')
LIMIT {pageSize} OFFSET {offset};

-- Função: findAllOrderByPessoa(Request $request)
-- Lista veículos ordenados pelo nome do proprietário com paginação
SELECT veiculos.*
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
ORDER BY pessoas.nome ASC
LIMIT {pageSize} OFFSET {offset};

-- Função: nVeiculosGroupByMarca(Request $request)
-- Agrupa e conta veículos por marca
SELECT marca, COUNT(*) AS total
FROM alvaro_vargas_alvarez.veiculos
GROUP BY marca
ORDER BY total DESC;

-- Função: nVeiculosBySexoAndMarca(Request $request)
-- Agrupa veículos por sexo do proprietário e marca
SELECT pessoas.is_masculino, veiculos.marca, COUNT(*) AS total
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
GROUP BY pessoas.is_masculino, veiculos.marca
ORDER BY pessoas.is_masculino DESC, total DESC;

-- Função: nRevisoesGroupByMarca(Request $request)
-- Soma as revisões por marca de veículo
SELECT marca, SUM(n_revisoes) AS total
FROM alvaro_vargas_alvarez.veiculos
GROUP BY marca
ORDER BY total DESC;

-- Função: store(StoreVeiculoRequest $request)
-- Insere novo veículo e incrementa contador de veículos na pessoa
INSERT INTO alvaro_vargas_alvarez.veiculos (col1, col2, ..., pessoa_id) VALUES (..., {pessoa_id});

SELECT * FROM alvaro_vargas_alvarez.pessoas WHERE id = {pessoa_id};

UPDATE alvaro_vargas_alvarez.pessoas
SET n_veiculos = n_veiculos + 1
WHERE id = {pessoa_id};

-- Função: show(Veiculo $veiculo)
-- Retorna veículo com dados da pessoa
SELECT * FROM alvaro_vargas_alvarez.veiculos WHERE id = {veiculo_id};
SELECT * FROM alvaro_vargas_alvarez.pessoas WHERE id = {pessoa_id};

-- Função: veiculosByPessoa($id, Request $request)
-- Lista veículos de uma pessoa com busca por placa e paginação
SELECT *
FROM alvaro_vargas_alvarez.veiculos
WHERE pessoa_id = {id}
  AND placa ILIKE '%{query}%' -- caso exista busca
LIMIT {pageSize} OFFSET {offset};

-- Função: update(UpdateVeiculoRequest $request, Veiculo $veiculo)
-- Atualiza o veículo se não houver outro com a mesma placa
SELECT * FROM alvaro_vargas_alvarez.veiculos WHERE placa = '{placa}' LIMIT 1;

UPDATE alvaro_vargas_alvarez.veiculos
SET col1 = val1, col2 = val2, ...
WHERE id = {veiculo_id};

-- Função: destroy(Veiculo $veiculo)
-- Deleta veículo se não tiver revisões associadas
SELECT * FROM alvaro_vargas_alvarez.revisoes
WHERE veiculo_id = {veiculo_id}
LIMIT 1;

DELETE FROM alvaro_vargas_alvarez.veiculos WHERE id = {veiculo_id};

UPDATE alvaro_vargas_alvarez.pessoas
SET n_veiculos = n_veiculos - 1
WHERE id = {pessoa_id};
