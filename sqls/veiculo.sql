-- Função: index(Request $request)
-- Descrição: Lista veículos com filtros de placa, renavam, modelo, marca e proprietário, incluindo paginação
SELECT veiculos.*, pessoas.nome AS proprietario, marcas.nome AS marca
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
LEFT JOIN alvaro_vargas_alvarez.marcas ON veiculos.marca_id = marcas.id
WHERE (:pessoa_id IS NULL OR pessoas.id = :pessoa_id)
  AND (:marca_id IS NULL OR marcas.id = :marca_id)
  AND (:query IS NULL OR UPPER(placa) LIKE :query || '%')
  AND (:query IS NULL OR UPPER(pessoas.nome) LIKE :query || '%')
  AND (:query IS NULL OR UPPER(renavam) LIKE :query || '%')
  AND (:query IS NULL OR UPPER(marcas.nome) LIKE :query || '%')
  AND (:query IS NULL OR UPPER(modelo) LIKE :query || '%')
ORDER BY :sort :direction
LIMIT :limit OFFSET :offset;

-- Função: nVeiculosGroupByMarca()
-- Descrição: Exibe a quantidade de veículos agrupados por marca
SELECT marcas.nome AS marca, COUNT(*) AS total
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.marcas ON veiculos.marca_id = marcas.id
GROUP BY marcas.id
ORDER BY total DESC;

-- Função: nVeiculosBySexoAndMarca()
-- Descrição: Exibe a quantidade de veículos agrupados por sexo do proprietário e marca
SELECT pessoas.is_masculino, marcas.nome AS marca, COUNT(*) AS total
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
LEFT JOIN alvaro_vargas_alvarez.marcas ON veiculos.marca_id = marcas.id
GROUP BY pessoas.is_masculino, marcas.nome
ORDER BY pessoas.is_masculino DESC, total DESC;

-- Função: nRevisoesGroupByMarca()
-- Descrição: Exibe a quantidade de revisões agrupadas por marca de veículo
SELECT marcas.nome AS marca, SUM(n_revisoes) AS total
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.marcas ON veiculos.marca_id = marcas.id
GROUP BY marcas.id
ORDER BY total DESC;

-- Função: store(StoreVeiculoRequest $request)
-- Descrição: Cadastra um novo veículo
INSERT INTO alvaro_vargas_alvarez.veiculos (placa, renavam, modelo, pessoa_id, marca_id)
VALUES (:placa, :renavam, :modelo, :pessoa_id, :marca_id);

-- Função: show(Veiculo $veiculo)
-- Descrição: Retorna os dados de um veículo específico
SELECT veiculos.*, pessoas.nome AS proprietario, marcas.nome AS marca
FROM alvaro_vargas_alvarez.veiculos
JOIN alvaro_vargas_alvarez.pessoas ON veiculos.pessoa_id = pessoas.id
LEFT JOIN alvaro_vargas_alvarez.marcas ON veiculos.marca_id = marcas.id
WHERE veiculos.id = :id;

-- Função: update(UpdateVeiculoRequest $request, Veiculo $veiculo)
-- Descrição: Atualiza os dados de um veículo
UPDATE alvaro_vargas_alvarez.veiculos
SET placa = :placa, renavam = :renavam, modelo = :modelo, pessoa_id = :pessoa_id, marca_id = :marca_id
WHERE id = :id;

-- Função: destroy(Veiculo $veiculo)
-- Descrição: Remove um veículo, desde que não existam revisões associadas
-- Primeiro verifica se existem revisões associadas
SELECT 1
FROM alvaro_vargas_alvarez.revisoes
WHERE veiculo_id = :veiculo_id
LIMIT 1;

-- Se não existirem revisões, realiza a exclusão
DELETE FROM alvaro_vargas_alvarez.veiculos
WHERE id = :veiculo_id;

-- Atualiza a quantidade de veículos do proprietário após a exclusão
UPDATE alvaro_vargas_alvarez.pessoas
SET n_veiculos = n_veiculos - 1
WHERE id = :pessoa_id;
