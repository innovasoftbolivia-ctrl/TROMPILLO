<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Procedure transaccional: crea una reserva + sus boletos (desde un array JSON)
 * + un pago opcional, en UNA sola llamada atómica.
 *
 * Ventajas reales frente a hacerlo con varios INSERT desde PHP:
 *  - Atomicidad garantizada por el motor (START TRANSACTION + HANDLER con ROLLBACK).
 *  - El total de la reserva se calcula en el servidor a partir de los boletos.
 *  - Se descuentan los asientos del vuelo en la misma transacción.
 *  - Un solo viaje a la base de datos (menos latencia).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_crear_reserva_completa');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_crear_reserva_completa(
                IN  p_codigo          VARCHAR(10),
                IN  p_vuelo_id        BIGINT,
                IN  p_usuario_id      BIGINT,
                IN  p_pasajero_id     BIGINT,
                IN  p_estado          VARCHAR(20),
                IN  p_fecha_reserva   DATETIME,
                IN  p_notas           TEXT,
                IN  p_boletos         JSON,
                IN  p_pago_monto      DECIMAL(12,2),
                IN  p_pago_metodo     VARCHAR(30),
                IN  p_pago_estado     VARCHAR(20),
                IN  p_pago_referencia VARCHAR(255),
                OUT p_reserva_id      BIGINT
            )
            BEGIN
                DECLARE v_total DECIMAL(12,2) DEFAULT 0;
                DECLARE v_boletos INT DEFAULT 0;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- Total y cantidad a partir de los boletos recibidos (JSON)
                SELECT COALESCE(SUM(jt.precio), 0), COUNT(*)
                  INTO v_total, v_boletos
                  FROM JSON_TABLE(p_boletos, '$[*]' COLUMNS (
                        precio DECIMAL(12,2) PATH '$.precio'
                  )) AS jt;

                -- 1) Cabecera de la reserva (total calculado en el servidor)
                INSERT INTO reservas
                    (codigo, vuelo_id, usuario_id, pasajero_id, estado, total, fecha_reserva, notas, created_at, updated_at)
                VALUES
                    (p_codigo, p_vuelo_id, p_usuario_id, p_pasajero_id, p_estado, v_total,
                     COALESCE(p_fecha_reserva, NOW()), p_notas, NOW(), NOW());

                SET p_reserva_id = LAST_INSERT_ID();

                -- 2) Boletos (uno por cada elemento del JSON)
                INSERT INTO boletos
                    (numero_boleto, reserva_id, pasajero_id, vuelo_id, asiento, precio, equipaje_kg, checkin, estado, created_at, updated_at)
                SELECT
                    CONCAT(p_codigo, '-', jt.rn),
                    p_reserva_id,
                    jt.pasajero_id,
                    p_vuelo_id,
                    jt.asiento,
                    jt.precio,
                    COALESCE(jt.equipaje_kg, 0),
                    0,
                    'emitido',
                    NOW(), NOW()
                FROM JSON_TABLE(p_boletos, '$[*]' COLUMNS (
                        rn          FOR ORDINALITY,
                        pasajero_id BIGINT        PATH '$.pasajero_id',
                        asiento     VARCHAR(5)    PATH '$.asiento',
                        precio      DECIMAL(12,2) PATH '$.precio',
                        equipaje_kg DECIMAL(5,2)  PATH '$.equipaje_kg'
                )) AS jt;

                -- 3) Pago (si se registró un monto)
                IF p_pago_monto IS NOT NULL AND p_pago_monto > 0 THEN
                    INSERT INTO pagos
                        (reserva_id, monto, metodo, estado, referencia, fecha_pago, created_at, updated_at)
                    VALUES
                        (p_reserva_id, p_pago_monto, p_pago_metodo,
                         COALESCE(p_pago_estado, 'pagado'), p_pago_referencia, NOW(), NOW(), NOW());
                END IF;

                -- 4) Descontar asientos del vuelo
                UPDATE vuelos
                   SET asientos_disponibles = GREATEST(asientos_disponibles - v_boletos, 0),
                       updated_at = NOW()
                 WHERE id = p_vuelo_id;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_crear_reserva_completa');
    }
};
