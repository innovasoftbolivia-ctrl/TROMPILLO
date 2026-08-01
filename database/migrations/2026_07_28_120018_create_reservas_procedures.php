<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ----- reservas -----
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_reservas_insert(
                IN p_codigo VARCHAR(10),
                IN p_vuelo_id BIGINT,
                IN p_usuario_id BIGINT,
                IN p_pasajero_id BIGINT,
                IN p_estado VARCHAR(20),
                IN p_total DECIMAL(12,2),
                IN p_fecha_reserva DATETIME,
                IN p_notas TEXT
            )
            BEGIN
                INSERT INTO reservas (
                    codigo,
                    vuelo_id,
                    usuario_id,
                    pasajero_id,
                    estado,
                    total,
                    fecha_reserva,
                    notas,
                    created_at,
                    updated_at
                ) VALUES (
                    p_codigo,
                    p_vuelo_id,
                    p_usuario_id,
                    p_pasajero_id,
                    p_estado,
                    p_total,
                    p_fecha_reserva,
                    p_notas,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_update');
        DB::unprepared("
            CREATE PROCEDURE sp_reservas_update(
                IN p_id BIGINT,
                IN p_codigo VARCHAR(10),
                IN p_vuelo_id BIGINT,
                IN p_usuario_id BIGINT,
                IN p_pasajero_id BIGINT,
                IN p_estado VARCHAR(20),
                IN p_total DECIMAL(12,2),
                IN p_fecha_reserva DATETIME,
                IN p_notas TEXT
            )
            BEGIN
                UPDATE reservas SET
                    codigo = p_codigo,
                    vuelo_id = p_vuelo_id,
                    usuario_id = p_usuario_id,
                    pasajero_id = p_pasajero_id,
                    estado = p_estado,
                    total = p_total,
                    fecha_reserva = p_fecha_reserva,
                    notas = p_notas,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_reservas_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM reservas WHERE id = p_id;
            END
        ");

        // ----- boletos -----
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_boletos_insert(
                IN p_numero_boleto VARCHAR(20),
                IN p_reserva_id BIGINT,
                IN p_pasajero_id BIGINT,
                IN p_vuelo_id BIGINT,
                IN p_asiento VARCHAR(5),
                IN p_precio DECIMAL(12,2),
                IN p_equipaje_kg DECIMAL(5,2),
                IN p_checkin TINYINT(1),
                IN p_estado VARCHAR(20)
            )
            BEGIN
                INSERT INTO boletos (
                    numero_boleto,
                    reserva_id,
                    pasajero_id,
                    vuelo_id,
                    asiento,
                    precio,
                    equipaje_kg,
                    checkin,
                    estado,
                    created_at,
                    updated_at
                ) VALUES (
                    p_numero_boleto,
                    p_reserva_id,
                    p_pasajero_id,
                    p_vuelo_id,
                    p_asiento,
                    p_precio,
                    p_equipaje_kg,
                    p_checkin,
                    p_estado,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_update');
        DB::unprepared("
            CREATE PROCEDURE sp_boletos_update(
                IN p_id BIGINT,
                IN p_numero_boleto VARCHAR(20),
                IN p_reserva_id BIGINT,
                IN p_pasajero_id BIGINT,
                IN p_vuelo_id BIGINT,
                IN p_asiento VARCHAR(5),
                IN p_precio DECIMAL(12,2),
                IN p_equipaje_kg DECIMAL(5,2),
                IN p_checkin TINYINT(1),
                IN p_estado VARCHAR(20)
            )
            BEGIN
                UPDATE boletos SET
                    numero_boleto = p_numero_boleto,
                    reserva_id = p_reserva_id,
                    pasajero_id = p_pasajero_id,
                    vuelo_id = p_vuelo_id,
                    asiento = p_asiento,
                    precio = p_precio,
                    equipaje_kg = p_equipaje_kg,
                    checkin = p_checkin,
                    estado = p_estado,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_boletos_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM boletos WHERE id = p_id;
            END
        ");

        // ----- pagos -----
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_pagos_insert(
                IN p_reserva_id BIGINT,
                IN p_monto DECIMAL(12,2),
                IN p_metodo VARCHAR(30),
                IN p_estado VARCHAR(20),
                IN p_referencia VARCHAR(255),
                IN p_fecha_pago DATETIME
            )
            BEGIN
                INSERT INTO pagos (
                    reserva_id,
                    monto,
                    metodo,
                    estado,
                    referencia,
                    fecha_pago,
                    created_at,
                    updated_at
                ) VALUES (
                    p_reserva_id,
                    p_monto,
                    p_metodo,
                    p_estado,
                    p_referencia,
                    p_fecha_pago,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_update');
        DB::unprepared("
            CREATE PROCEDURE sp_pagos_update(
                IN p_id BIGINT,
                IN p_reserva_id BIGINT,
                IN p_monto DECIMAL(12,2),
                IN p_metodo VARCHAR(30),
                IN p_estado VARCHAR(20),
                IN p_referencia VARCHAR(255),
                IN p_fecha_pago DATETIME
            )
            BEGIN
                UPDATE pagos SET
                    reserva_id = p_reserva_id,
                    monto = p_monto,
                    metodo = p_metodo,
                    estado = p_estado,
                    referencia = p_referencia,
                    fecha_pago = p_fecha_pago,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_pagos_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM pagos WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reservas_delete');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_boletos_delete');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pagos_delete');
    }
};
