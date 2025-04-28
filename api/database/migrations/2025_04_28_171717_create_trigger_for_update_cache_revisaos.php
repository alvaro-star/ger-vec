<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_cache_revisaos_on_update()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.data IS DISTINCT FROM OLD.data OR TG_OP = \'INSERT\' THEN
                    PERFORM 1 FROM cache_revisaos WHERE pessoa_id = NEW.pessoa_id;

                    -- Se o registro não existir, criar um novo registro na tabela cache_revisaos
                    IF NOT FOUND THEN
                        INSERT INTO cache_revisaos (pessoa_id, avg_revisoes, last_revisao)
                        VALUES (NEW.pessoa_id, CAST(0 AS numeric(20, 2)), NEW.data); -- Casteando o valor 0 para o tipo numeric
                    END IF;

                    -- Atualizar os dados em cache_revisaos com base na pessoa_id
                    UPDATE cache_revisaos
                    SET last_revisao = (
                        SELECT MAX(data)
                        FROM revisoes
                        WHERE pessoa_id = NEW.pessoa_id
                    ), avg_revisoes = (
                        SELECT CAST(AVG(diferenca_segundos) AS numeric(15, 5)) -- Casteando o valor médio
                        FROM (
                            SELECT EXTRACT(EPOCH FROM (data::timestamp - LAG(data::timestamp) OVER (PARTITION BY pessoa_id ORDER BY data))) AS diferenca_segundos
                            FROM revisoes
                            WHERE pessoa_id = NEW.pessoa_id
                        ) AS subquery
                    )
                    WHERE pessoa_id = NEW.pessoa_id;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Criar o trigger que será chamado após a inserção ou atualização na tabela revisoes
        DB::unprepared('
            CREATE TRIGGER update_cache_revisaos_after_update
            AFTER INSERT OR UPDATE OF data ON revisoes
            FOR EACH ROW
            EXECUTE FUNCTION update_cache_revisaos_on_update();
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_cache_revisaos_after_update ON revisoes;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_cache_revisaos_on_update;');
    }
};
