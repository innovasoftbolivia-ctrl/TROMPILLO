<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Procedure transaccional para DESPACHAR un vuelo:
 *  - Asigna aeronave + piloto + copiloto.
 *  - Valida reglas de negocio en el servidor (estado del vuelo, aeronave activa,
 *    cupo de pasajeros y peso y balance) y aborta con ROLLBACK + mensaje si falla.
 *  - Si todo es válido, deja el vuelo en estado 'abordando' y recalcula asientos.
 *
 * Los mensajes de error se emiten con SIGNAL SQLSTATE '45000'; Laravel los recibe
 * como QueryException y el controlador muestra el motivo al usuario.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_despachar_vuelo');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_despachar_vuelo(
                IN p_vuelo_id    BIGINT,
                IN p_aeronave_id BIGINT,
                IN p_piloto_id   BIGINT,
                IN p_copiloto_id BIGINT
            )
            BEGIN
                DECLARE v_estado_vuelo    VARCHAR(20);
                DECLARE v_estado_aeronave VARCHAR(20);
                DECLARE v_cap_pax         INT;
                DECLARE v_cap_carga       DECIMAL(8,2);
                DECLARE v_peso_vacio      DECIMAL(8,2);
                DECLARE v_mtow            DECIMAL(8,2);
                DECLARE v_num_pax         INT DEFAULT 0;
                DECLARE v_peso_pax        DECIMAL(12,2) DEFAULT 0;
                DECLARE v_peso_equipaje   DECIMAL(12,2) DEFAULT 0;
                DECLARE v_peso_carga      DECIMAL(12,2) DEFAULT 0;
                DECLARE v_payload         DECIMAL(12,2);
                DECLARE v_max_payload     DECIMAL(12,2);
                DECLARE v_msg             VARCHAR(255);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- 1) Vuelo existe y está en un estado despachable
                SELECT estado INTO v_estado_vuelo FROM vuelos WHERE id = p_vuelo_id FOR UPDATE;
                IF v_estado_vuelo IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vuelo no existe.';
                END IF;
                IF v_estado_vuelo NOT IN ('programado', 'confirmado', 'retrasado') THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El vuelo no se puede despachar en su estado actual.';
                END IF;

                -- 2) Aeronave existe y está activa
                SELECT estado, capacidad_pasajeros, capacidad_carga_kg, peso_vacio_kg, peso_maximo_despegue_kg
                  INTO v_estado_aeronave, v_cap_pax, v_cap_carga, v_peso_vacio, v_mtow
                  FROM aeronaves WHERE id = p_aeronave_id;
                IF v_estado_aeronave IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La aeronave no existe.';
                END IF;
                IF v_estado_aeronave <> 'activa' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La aeronave no está activa (en mantenimiento o fuera de servicio).';
                END IF;

                -- 3) Tripulación
                IF p_piloto_id IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debe asignar un piloto.';
                END IF;
                IF p_copiloto_id IS NOT NULL AND p_copiloto_id = p_piloto_id THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El copiloto debe ser distinto del piloto.';
                END IF;

                -- 4) Cupo: pasajeros (boletos) vs capacidad de la aeronave
                SELECT COUNT(*), COALESCE(SUM(equipaje_kg), 0)
                  INTO v_num_pax, v_peso_equipaje
                  FROM boletos WHERE vuelo_id = p_vuelo_id;
                IF v_num_pax > v_cap_pax THEN
                    SIGNAL SQLSTATE '45000'
                      SET MESSAGE_TEXT = 'Cupo excedido: hay más pasajeros que asientos disponibles en la aeronave.';
                END IF;

                -- 5) Peso y balance: pasajeros + equipaje + carga vs carga útil máxima
                SELECT COALESCE(SUM(pa.peso_kg), 0) INTO v_peso_pax
                  FROM boletos b JOIN pasajeros pa ON pa.id = b.pasajero_id
                  WHERE b.vuelo_id = p_vuelo_id;

                SELECT COALESCE(SUM(peso_kg), 0) INTO v_peso_carga
                  FROM envios_carga WHERE vuelo_id = p_vuelo_id;

                SET v_payload = v_peso_pax + v_peso_equipaje + v_peso_carga;
                SET v_max_payload = CASE
                        WHEN v_mtow IS NOT NULL AND v_peso_vacio IS NOT NULL THEN v_mtow - v_peso_vacio
                        ELSE v_cap_carga
                    END;

                IF v_max_payload IS NOT NULL AND v_payload > v_max_payload THEN
                    SET v_msg = CONCAT('Peso excedido: carga util ', v_payload,
                                       ' kg supera el maximo de ', v_max_payload, ' kg.');
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_msg;
                END IF;

                -- OK: despachar
                UPDATE vuelos
                   SET aeronave_id = p_aeronave_id,
                       piloto_id = p_piloto_id,
                       copiloto_id = p_copiloto_id,
                       asientos_disponibles = GREATEST(v_cap_pax - v_num_pax, 0),
                       estado = 'abordando',
                       updated_at = NOW()
                 WHERE id = p_vuelo_id;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_despachar_vuelo');
    }
};
