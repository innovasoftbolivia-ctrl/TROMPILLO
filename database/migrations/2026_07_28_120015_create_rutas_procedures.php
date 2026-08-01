<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_rutas_insert(
                IN p_origen_id BIGINT,
                IN p_destino_id BIGINT,
                IN p_distancia_km INT,
                IN p_duracion_estimada_min INT,
                IN p_precio_base DECIMAL(12,2),
                IN p_activa TINYINT(1)
            )
            BEGIN
                INSERT INTO rutas (
                    origen_id,
                    destino_id,
                    distancia_km,
                    duracion_estimada_min,
                    precio_base,
                    activa,
                    created_at,
                    updated_at
                ) VALUES (
                    p_origen_id,
                    p_destino_id,
                    p_distancia_km,
                    p_duracion_estimada_min,
                    p_precio_base,
                    p_activa,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_update');
        DB::unprepared("
            CREATE PROCEDURE sp_rutas_update(
                IN p_id BIGINT,
                IN p_origen_id BIGINT,
                IN p_destino_id BIGINT,
                IN p_distancia_km INT,
                IN p_duracion_estimada_min INT,
                IN p_precio_base DECIMAL(12,2),
                IN p_activa TINYINT(1)
            )
            BEGIN
                UPDATE rutas SET
                    origen_id = p_origen_id,
                    destino_id = p_destino_id,
                    distancia_km = p_distancia_km,
                    duracion_estimada_min = p_duracion_estimada_min,
                    precio_base = p_precio_base,
                    activa = p_activa,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_rutas_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM rutas WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rutas_delete');
    }
};
