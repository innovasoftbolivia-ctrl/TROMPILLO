<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Dos procedures transaccionales de operación:
 *  - sp_checkin_boleto: hace el check-in de un boleto validando reglas.
 *  - sp_cerrar_vuelo:   cierra el vuelo (despega): estado 'en_vuelo' + salida real,
 *                       y marca los boletos como 'usado' (con check-in) o 'no_show'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------- Check-in de un boleto ----------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_checkin_boleto');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_checkin_boleto(IN p_boleto_id BIGINT)
            BEGIN
                DECLARE v_estado_boleto VARCHAR(20);
                DECLARE v_checkin       TINYINT;
                DECLARE v_vuelo_id      BIGINT;
                DECLARE v_estado_vuelo  VARCHAR(20);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                SELECT estado, checkin, vuelo_id
                  INTO v_estado_boleto, v_checkin, v_vuelo_id
                  FROM boletos WHERE id = p_boleto_id FOR UPDATE;

                IF v_vuelo_id IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El boleto no existe.';
                END IF;
                IF v_estado_boleto = 'cancelado' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El boleto está cancelado.';
                END IF;
                IF v_checkin = 1 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El pasajero ya tiene check-in.';
                END IF;

                SELECT estado INTO v_estado_vuelo FROM vuelos WHERE id = v_vuelo_id;
                IF v_estado_vuelo NOT IN ('programado', 'confirmado', 'abordando') THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vuelo no admite check-in en su estado actual.';
                END IF;

                UPDATE boletos SET checkin = 1, updated_at = NOW() WHERE id = p_boleto_id;

                COMMIT;
            END
            SQL
        );

        // ---------- Cerrar (despegar) un vuelo ----------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cerrar_vuelo');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_cerrar_vuelo(
                IN  p_vuelo_id    BIGINT,
                IN  p_salida_real DATETIME,
                OUT p_usados      INT,
                OUT p_no_show     INT
            )
            BEGIN
                DECLARE v_estado_vuelo VARCHAR(20);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                SELECT estado INTO v_estado_vuelo FROM vuelos WHERE id = p_vuelo_id FOR UPDATE;
                IF v_estado_vuelo IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vuelo no existe.';
                END IF;
                IF v_estado_vuelo <> 'abordando' THEN
                    SIGNAL SQLSTATE '45000'
                      SET MESSAGE_TEXT = 'El vuelo debe estar en abordando para cerrarse (despegar).';
                END IF;

                -- Boletos con check-in => usados; sin check-in => no-show (se respetan los cancelados).
                UPDATE boletos SET estado = 'usado', updated_at = NOW()
                 WHERE vuelo_id = p_vuelo_id AND checkin = 1
                   AND estado <> 'cancelado' COLLATE utf8mb4_unicode_ci;

                UPDATE boletos SET estado = 'no_show', updated_at = NOW()
                 WHERE vuelo_id = p_vuelo_id AND checkin = 0
                   AND estado <> 'cancelado' COLLATE utf8mb4_unicode_ci;

                SELECT COUNT(*) INTO p_usados
                  FROM boletos WHERE vuelo_id = p_vuelo_id
                   AND estado = 'usado' COLLATE utf8mb4_unicode_ci;
                SELECT COUNT(*) INTO p_no_show
                  FROM boletos WHERE vuelo_id = p_vuelo_id
                   AND estado = 'no_show' COLLATE utf8mb4_unicode_ci;

                UPDATE vuelos
                   SET estado = 'en_vuelo',
                       salida_real = COALESCE(p_salida_real, NOW()),
                       updated_at = NOW()
                 WHERE id = p_vuelo_id;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_checkin_boleto');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cerrar_vuelo');
    }
};
