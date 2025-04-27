-- Função: index
-- Variações dependendo da presença de filtros (pessoa_id, veiculo_id, data_start, data_end)
SELECT * FROM alvaro_vargas_alvarez.revisoes
-- Filtros aplicáveis condicionalmente:
-- WHERE pessoa_id = ?
-- AND veiculo_id = ?
-- AND data BETWEEN ? AND ?
-- AND data >= ?
-- AND data <= ?
ORDER BY data DESC
LIMIT ? OFFSET ?;

-- Função: store
SELECT * FROM alvaro_vargas_alvarez.veiculos
WHERE placa = ?
LIMIT 1;

UPDATE alvaro_vargas_alvarez.veiculos
SET n_revisoes = n_revisoes + 1
WHERE id = ?;

UPDATE alvaro_vargas_alvarez.pessoas
SET n_revisoes = n_revisoes + 1
WHERE id = ?;

INSERT INTO alvaro_vargas_alvarez.revisoes (...campos...)
VALUES (...valores...);

-- Função: countRevisoesByPessoa
-- Busca por nome (opcional):
SELECT * FROM alvaro_vargas_alvarez.pessoas
WHERE nome LIKE ?
ORDER BY n_revisoes DESC
LIMIT ? OFFSET ?;

-- Consulta agregada por pessoa_id, considerando as diferenças entre datas de revisões
SELECT
    pessoa_id,
    MAX(data) AS last_revisao,
    AVG(diferenca_segundos) AS media_diferenca
FROM (
    SELECT
        pessoa_id,
        data,
        EXTRACT(EPOCH FROM (data::timestamp - LAG(data::timestamp) OVER (PARTITION BY pessoa_id ORDER BY data))) AS diferenca_segundos
    FROM alvaro_vargas_alvarez.revisoes
    WHERE pessoa_id IN (?, ?, ...)
) AS sub
GROUP BY pessoa_id;

-- Função: show
SELECT * FROM alvaro_vargas_alvarez.veiculos
WHERE id = ?
LIMIT 1;

-- Função: revisoesByVeiculo
SELECT * FROM alvaro_vargas_alvarez.revisoes
WHERE veiculo_id = ?
ORDER BY data DESC
LIMIT ? OFFSET ?;

-- Função: update
UPDATE alvaro_vargas_alvarez.revisoes
SET campo1 = ?, campo2 = ?, ...
WHERE id = ?;

-- Função: destroy
UPDATE alvaro_vargas_alvarez.veiculos
SET n_revisoes = n_revisoes - 1
WHERE id = ?;

UPDATE alvaro_vargas_alvarez.pessoas
SET n_revisoes = n_revisoes - 1
WHERE id = ?;

DELETE FROM alvaro_vargas_alvarez.revisoes
WHERE id = ?;