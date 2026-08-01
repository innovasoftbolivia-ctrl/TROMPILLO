<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mejora de sp_confirmar_reserva: si la reserva no tiene boletos, crea uno
 * para el titular usando el PRECIO DEL VUELO (por defecto). Así, al confirmar
 * una reserva simple, la venta/factura toman el precio del pasaje automáticamente
 * (ya no queda en 0).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_confirmar_reserva');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_confirmar_reserva(
                IN  p_reserva_id BIGINT,
                IN  p_usuario_id BIGINT,
                OUT p_venta_id   BIGINT
            )
            BEGIN
                DECLARE v_estado       VARCHAR(20);
                DECLARE v_persona_id   BIGINT;
                DECLARE v_total        DECIMAL(12,2);
                DECLARE v_pagado       DECIMAL(12,2);
                DECLARE v_numero       VARCHAR(20);
                DECLARE v_nboletos     INT;
                DECLARE v_vuelo_id     BIGINT;
                DECLARE v_pasajero_id  BIGINT;
                DECLARE v_precio       DECIMAL(12,2);
                DECLARE v_codigo       VARCHAR(10);
                DECLARE v_total_manual DECIMAL(12,2);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                SELECT estado, vuelo_id, pasajero_id, codigo, total
                  INTO v_estado, v_vuelo_id, v_pasajero_id, v_codigo, v_total_manual
                  FROM reservas WHERE id = p_reserva_id FOR UPDATE;

                IF v_estado IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La reserva no existe.';
                END IF;
                IF v_estado = 'cancelada' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede confirmar una reserva cancelada.';
                END IF;

                -- Si la reserva no tiene boletos, crear uno para el titular con el precio del vuelo
                SELECT COUNT(*) INTO v_nboletos FROM boletos WHERE reserva_id = p_reserva_id;
                IF v_nboletos = 0 AND v_pasajero_id IS NOT NULL THEN
                    SELECT precio INTO v_precio FROM vuelos WHERE id = v_vuelo_id;
                    IF v_precio IS NOT NULL THEN
                        INSERT INTO boletos
                            (numero_boleto, reserva_id, pasajero_id, vuelo_id, precio, equipaje_kg, checkin, estado, created_at, updated_at)
                        VALUES
                            (CONCAT(v_codigo, '-1'), p_reserva_id, v_pasajero_id, v_vuelo_id, v_precio, 0, 0, 'emitido', NOW(), NOW());
                    END IF;
                END IF;

                -- Total = suma de boletos; si aún es 0, usar el total manual de la reserva
                SELECT COALESCE(SUM(precio), 0) INTO v_total FROM boletos WHERE reserva_id = p_reserva_id;
                IF v_total = 0 AND v_total_manual > 0 THEN
                    SET v_total = v_total_manual;
                END IF;

                SELECT pa.persona_id INTO v_persona_id
                  FROM reservas r LEFT JOIN pasajeros pa ON pa.id = r.pasajero_id
                 WHERE r.id = p_reserva_id;

                UPDATE reservas SET estado = 'confirmada', total = v_total, updated_at = NOW()
                 WHERE id = p_reserva_id;

                SET p_venta_id = NULL;
                SELECT id INTO p_venta_id FROM ventas WHERE reserva_id = p_reserva_id LIMIT 1;

                IF p_venta_id IS NULL THEN
                    SELECT CONCAT('V-', LPAD(COALESCE(MAX(id), 0) + 1, 5, '0')) INTO v_numero FROM ventas;

                    INSERT INTO ventas
                        (numero, persona_id, usuario_id, reserva_id, fecha, subtotal, descuento, total, estado, created_at, updated_at)
                    VALUES
                        (v_numero, v_persona_id, p_usuario_id, p_reserva_id, NOW(), v_total, 0, v_total, 'pendiente', NOW(), NOW());

                    SET p_venta_id = LAST_INSERT_ID();

                    INSERT INTO venta_detalles
                        (venta_id, boleto_id, descripcion, cantidad, precio_unitario, subtotal, created_at, updated_at)
                    SELECT p_venta_id, b.id, CONCAT('Boleto ', b.numero_boleto), 1, b.precio, b.precio, NOW(), NOW()
                      FROM boletos b WHERE b.reserva_id = p_reserva_id;
                ELSE
                    -- Mantener el total de la venta sincronizado con los boletos
                    UPDATE ventas SET subtotal = v_total, total = v_total - descuento, updated_at = NOW()
                     WHERE id = p_venta_id;
                END IF;

                UPDATE pagos SET venta_id = p_venta_id, updated_at = NOW()
                 WHERE reserva_id = p_reserva_id AND venta_id IS NULL;

                SELECT COALESCE(SUM(monto), 0) INTO v_pagado
                  FROM pagos WHERE venta_id = p_venta_id
                   AND estado = 'pagado' COLLATE utf8mb4_unicode_ci;

                IF v_total > 0 AND v_pagado >= v_total THEN
                    UPDATE ventas SET estado = 'pagada', updated_at = NOW() WHERE id = p_venta_id;
                END IF;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        // Se mantiene la versión mejorada; el rollback vuelve a dejar el procedure sin cambios.
    }
};
