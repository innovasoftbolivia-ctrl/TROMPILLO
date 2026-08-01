<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Opción A: al confirmar una reserva se genera automáticamente su VENTA
 * (con los boletos como detalles) y se trasladan los pagos a la venta.
 * La venta pasa a ser la única fuente de verdad del dinero.
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
                DECLARE v_estado      VARCHAR(20);
                DECLARE v_persona_id  BIGINT;
                DECLARE v_total       DECIMAL(12,2);
                DECLARE v_pagado      DECIMAL(12,2);
                DECLARE v_numero      VARCHAR(20);

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                SELECT estado INTO v_estado FROM reservas WHERE id = p_reserva_id FOR UPDATE;
                IF v_estado IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La reserva no existe.';
                END IF;
                IF v_estado = 'cancelada' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede confirmar una reserva cancelada.';
                END IF;

                -- Total (fuente: boletos de la reserva) y persona del titular
                SELECT COALESCE(SUM(precio), 0) INTO v_total FROM boletos WHERE reserva_id = p_reserva_id;

                SELECT pa.persona_id INTO v_persona_id
                  FROM reservas r LEFT JOIN pasajeros pa ON pa.id = r.pasajero_id
                 WHERE r.id = p_reserva_id;

                -- Confirmar la reserva y dejar su total sincronizado con los boletos
                UPDATE reservas SET estado = 'confirmada', total = v_total, updated_at = NOW()
                 WHERE id = p_reserva_id;

                -- ¿Ya tiene venta?
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
                END IF;

                -- Trasladar los pagos de la reserva a la venta (fuente de verdad del dinero)
                UPDATE pagos SET venta_id = p_venta_id, updated_at = NOW()
                 WHERE reserva_id = p_reserva_id AND venta_id IS NULL;

                -- Si lo pagado cubre el total, marcar la venta como pagada
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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_confirmar_reserva');
    }
};
