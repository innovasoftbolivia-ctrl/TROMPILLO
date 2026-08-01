<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_vuelos_insert(
    IN p_numero_vuelo VARCHAR(15),
    IN p_ruta_id BIGINT,
    IN p_origen_id BIGINT,
    IN p_destino_id BIGINT,
    IN p_aeronave_id BIGINT,
    IN p_piloto_id BIGINT,
    IN p_copiloto_id BIGINT,
    IN p_tipo VARCHAR(20),
    IN p_salida_programada DATETIME,
    IN p_llegada_programada DATETIME,
    IN p_salida_real DATETIME,
    IN p_llegada_real DATETIME,
    IN p_asientos_disponibles INT,
    IN p_precio DECIMAL(12,2),
    IN p_estado VARCHAR(20),
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO vuelos (
        numero_vuelo,
        ruta_id,
        origen_id,
        destino_id,
        aeronave_id,
        piloto_id,
        copiloto_id,
        tipo,
        salida_programada,
        llegada_programada,
        salida_real,
        llegada_real,
        asientos_disponibles,
        precio,
        estado,
        observaciones,
        created_at,
        updated_at
    ) VALUES (
        p_numero_vuelo,
        p_ruta_id,
        p_origen_id,
        p_destino_id,
        p_aeronave_id,
        p_piloto_id,
        p_copiloto_id,
        p_tipo,
        p_salida_programada,
        p_llegada_programada,
        p_salida_real,
        p_llegada_real,
        p_asientos_disponibles,
        p_precio,
        p_estado,
        p_observaciones,
        NOW(),
        NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_vuelos_update(
    IN p_id BIGINT,
    IN p_numero_vuelo VARCHAR(15),
    IN p_ruta_id BIGINT,
    IN p_origen_id BIGINT,
    IN p_destino_id BIGINT,
    IN p_aeronave_id BIGINT,
    IN p_piloto_id BIGINT,
    IN p_copiloto_id BIGINT,
    IN p_tipo VARCHAR(20),
    IN p_salida_programada DATETIME,
    IN p_llegada_programada DATETIME,
    IN p_salida_real DATETIME,
    IN p_llegada_real DATETIME,
    IN p_asientos_disponibles INT,
    IN p_precio DECIMAL(12,2),
    IN p_estado VARCHAR(20),
    IN p_observaciones TEXT
)
BEGIN
    UPDATE vuelos SET
        numero_vuelo = p_numero_vuelo,
        ruta_id = p_ruta_id,
        origen_id = p_origen_id,
        destino_id = p_destino_id,
        aeronave_id = p_aeronave_id,
        piloto_id = p_piloto_id,
        copiloto_id = p_copiloto_id,
        tipo = p_tipo,
        salida_programada = p_salida_programada,
        llegada_programada = p_llegada_programada,
        salida_real = p_salida_real,
        llegada_real = p_llegada_real,
        asientos_disponibles = p_asientos_disponibles,
        precio = p_precio,
        estado = p_estado,
        observaciones = p_observaciones,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_vuelos_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM vuelos WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vuelos_delete');
    }
};
