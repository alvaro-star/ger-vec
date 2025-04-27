-- Função: index(Request $request)
-- Descrição: Lista pessoas com filtro por nome, sexo e paginação (via getByPageable)

-- Supondo que os parâmetros sejam:
-- :query = prefixo do nome (ex: 'Ana')
-- :sexo = 'M' ou 'F'
-- :limit = quantidade por página
-- :offset = deslocamento
SELECT *
FROM alvaro_vargas_alvarez.pessoas
WHERE (:query IS NULL OR nome LIKE :query || '%')
  AND (:sexo IS NULL OR is_masculino = (:sexo = 'M'))
ORDER BY nome ASC
LIMIT :limit OFFSET :offset;


-- Função: statistics()
-- Descrição: Estatísticas agrupadas por sexo

SELECT
    is_masculino,
    SUM(n_veiculos) AS n_veiculos,
    MIN(n_veiculos) AS min_veiculos,
    MAX(n_veiculos) AS max_veiculos,

    SUM(n_revisoes) AS n_revisoes,
    MIN(n_revisoes) AS min_revisoes,
    MAX(n_revisoes) AS max_revisoes,

    COUNT(*) AS n_elementos,
    SUM(idade) AS soma_idades,
    MIN(idade) AS min_idade,
    MAX(idade) AS max_idade
FROM alvaro_vargas_alvarez.pessoas
GROUP BY is_masculino;

-- Função: countVeiculosBySexo()
-- Descrição: Soma de veículos agrupada por sexo

SELECT
    SUM(n_veiculos) AS total,
    is_masculino
FROM alvaro_vargas_alvarez.pessoas
GROUP BY is_masculino
ORDER BY total;
