-- Função: index(Request $request)
-- Descrição: Exibe as revisões com filtros de pessoa, veículo e intervalo de datas
SELECT revisoes.*, veiculos.placa AS veiculo
FROM alvaro_vargas_alvarez.revisoes
LEFT JOIN alvaro_vargas_alvarez.veiculos ON veiculos.id = revisoes.veiculo_id
WHERE (:pessoa_id IS NULL OR revisoes.pessoa_id = :pessoa_id)
  AND (:veiculo_id IS NULL OR revisoes.veiculo_id = :veiculo_id)
  AND (:data_start IS NULL OR revisoes.data >= :data_start)
  AND (:data_end IS NULL OR revisoes.data <= :data_end)
ORDER BY :sort :direction
LIMIT :limit OFFSET :offset;

-- Função: store(StoreRevisaoRequest $request)
-- Descrição: Cadastra uma nova revisão para o veículo
INSERT INTO alvaro_vargas_alvarez.revisoes (data, quilometragem, valor_total, veiculo_id, pessoa_id)
VALUES (:data, :quilometragem, :valor_total, :veiculo_id, :pessoa_id);

-- Função: countRevisoesByPessoa(Request $request)
-- Descrição: Contabiliza as revisões de uma pessoa, retornando dados de cache de revisões
SELECT pessoas.*, cache_revisaos.last_revisao, cache_revisaos.avg_revisoes AS media
FROM alvaro_vargas_alvarez.pessoas
LEFT JOIN alvaro_vargas_alvarez.cache_revisaos ON pessoas.id = cache_revisaos.pessoa_id
WHERE (:query IS NULL OR UPPER(pessoas.nome) LIKE :query || '%')
  AND (:query_numbers IS NULL OR pessoas.cpf LIKE :query_numbers || '%')
ORDER BY :sort :direction
LIMIT :limit OFFSET :offset;

-- Função: show(Revisao $reviso)
-- Descrição: Exibe os dados de uma revisão específica
SELECT revisoes.*, veiculos.placa AS veiculo
FROM alvaro_vargas_alvarez.revisoes
LEFT JOIN alvaro_vargas_alvarez.veiculos ON veiculos.id = revisoes.veiculo_id
WHERE revisoes.id = :id;

-- Função: update(UpdateRevisaoRequest $request, Revisao $reviso)
-- Descrição: Atualiza uma revisão existente
UPDATE alvaro_vargas_alvarez.revisoes
SET data = :data, quilometragem = :quilometragem, valor_total = :valor_total, veiculo_id = :veiculo_id, pessoa_id = :pessoa_id
WHERE id = :id;

-- Função: destroy(Revisao $reviso)
-- Descrição: Remove uma revisão e decrementa as contagens de revisões nos veículos e nas pessoas
SELECT 1
FROM alvaro_vargas_alvarez.revisoes
WHERE id = :id
LIMIT 1;

-- Decrementa as revisões no veículo
UPDATE alvaro_vargas_alvarez.veiculos
SET n_revisoes = n_revisoes - 1
WHERE id = :veiculo_id;

-- Decrementa as revisões na pessoa
UPDATE alvaro_vargas_alvarez.pessoas
SET n_revisoes = n_revisoes - 1
WHERE id = :pessoa_id;

-- Remove a revisão
DELETE FROM alvaro_vargas_alvarez.revisoes
WHERE id = :id;
