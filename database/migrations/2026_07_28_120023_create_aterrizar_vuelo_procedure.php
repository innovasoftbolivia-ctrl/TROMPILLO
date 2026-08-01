<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Procedure transaccional para ATERRIZAR un vuelo:
 *  - Exige que el vuelo esté 'en_vuelo'.
 *  - Registra la llegada real y valida que no sea anterior a la salida real.
 *  - Deja el vuelo en estado 'aterrizado'.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aterrizar_vuelo');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_aterrizar_vuelo(
                IN p_vuelo_id     BIGINT,
                IN p_llegada_real DATETIME
            )
            BEGIN
                DECLARE v_estado_vuelo VARCHAR(20);
                DECLARE v_salida_real  DATETIME;
                DECLARE v_llegada      DATETIME;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                SELECT estado, salida_real
                  INTO v_estado_vuelo, v_salida_real
                  FROM vuelos WHERE id = p_vuelo_id FOR UPDATE;

                IF v_estado_vuelo IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vuelo no existe.';
                END IF;
                IF v_estado_vuelo <> 'en_vuelo' THEN
                    SIGNAL SQLSTATE '45000'
                      SET MESSAGE_TEXT = 'El vuelo debe estar en vuelo para poder aterrizar.';
                END IF;

                SET v_llegada = COALESCE(p_llegada_real, NOW());

                IF v_salida_real IS NOT NULL AND v_llegada < v_salida_real THEN
                    SIGNAL SQLSTATE '45000'
                      SET MESSAGE_TEXT = 'La llegada real no puede ser anterior a la salida real.';
                END IF;

                UPDATE vuelos
                   SET estado = 'aterrizado',
                       llegada_real = v_llegada,
                       updated_at = NOW()
                 WHERE id = p_vuelo_id;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aterrizar_vuelo');
    }
};
