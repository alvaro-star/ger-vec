-- Função: index(Request $request)
-- Descrição: Lista pessoas com filtro por nome, sexo e paginação (via getByPageable)
SELECT *
FROM alvaro_vargas_alvarez.pessoas
WHERE (:query IS NULL OR nome LIKE :query || '%')
  AND (:sexo IS NULL OR is_masculino = (:sexo = 'M'))
  AND (:query_numbers IS NULL OR cpf LIKE :query_numbers || '%' OR celular LIKE :query_numbers || '%')
ORDER BY :sort :direction
LIMIT :limit OFFSET :offset;

-- Função: statistics()
-- Descrição: Retorna estatísticas agregadas das pessoas por sexo e no total
SELECT
    is_masculino,
    SUM(n_veiculos) AS n_veiculos,
    MIN(n_veiculos) AS min_veiculos,
    MAX(n_veiculos) AS max_veiculos,
    SUM(n_revisoes) AS n_revisoes,
    MIN(n_revisoes) AS min_revisoes,
    MAX(n_revisoes) AS max_revisoes,
    COUNT(*) AS n_elementos,
    SUM(EXTRACT(YEAR FROM AGE(NOW(), nascimento))) AS soma_idades,
    MIN(EXTRACT(YEAR FROM AGE(NOW(), nascimento))) AS min_idade,
    MAX(EXTRACT(YEAR FROM AGE(NOW(), nascimento))) AS max_idade
FROM alvaro_vargas_alvarez.pessoas
GROUP BY is_masculino;

-- Função: countVeiculosBySexo()
-- Descrição: Retorna a soma total de veículos agrupada por sexo
SELECT
    SUM(n_veiculos) AS total,
    is_masculino
FROM alvaro_vargas_alvarez.pessoas
GROUP BY is_masculino
ORDER BY total;

-- Função: store(StorePessoaRequest $request)
-- Descrição: Cadastra uma nova pessoa
INSERT INTO pessoas (nome, cpf, celular, nascimento, is_masculino, n_veiculos, n_revisoes)
VALUES (:nome, :cpf, :celular, :nascimento, :is_masculino, :n_veiculos, :n_revisoes);

-- Função: show(Pessoa $pessoa)
-- Descrição: Retorna os dados de uma pessoa específica
SELECT *
FROM alvaro_vargas_alvarez.pessoas
WHERE id = :id;

-- Função: update(UpdatePessoaRequest $request, Pessoa $pessoa)
-- Descrição: Atualiza os dados de uma pessoa existente
UPDATE pessoas
SET nome = :nome, cpf = :cpf, celular = :celular, nascimento = :nascimento, is_masculino = :is_masculino, n_veiculos = :n_veiculos, n_revisoes = :n_revisoes
WHERE id = :id;

-- Função: destroy(Pessoa $pessoa)
-- Descrição: Remove uma pessoa, desde que não existam veículos ou revisões associadas
-- Primeiro verifica se existem veículos ou revisões associadas
SELECT 1
FROM alvaro_vargas_alvarez.veiculos
WHERE pessoa_id = :pessoa_id
LIMIT 1;

SELECT 1
FROM alvaro_vargas_alvarez.revisoes
WHERE pessoa_id = :pessoa_id
LIMIT 1;

-- Se não existirem veículos nem revisões, realiza a exclusão
DELETE FROM alvaro_vargas_alvarez.pessoas
WHERE id = :pessoa_id;
