<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_aeropuertos_insert(
    IN p_codigo_oaci VARCHAR(4),
    IN p_codigo_iata VARCHAR(3),
    IN p_nombre VARCHAR(255),
    IN p_ciudad VARCHAR(255),
    IN p_departamento VARCHAR(255),
    IN p_pais VARCHAR(255),
    IN p_tipo VARCHAR(20),
    IN p_latitud DECIMAL(10,7),
    IN p_longitud DECIMAL(10,7),
    IN p_elevacion_pies INT,
    IN p_longitud_pista_m INT,
    IN p_superficie_pista VARCHAR(20),
    IN p_activo TINYINT(1)
)
BEGIN
    INSERT INTO aeropuertos (
        codigo_oaci, codigo_iata, nombre, ciudad, departamento, pais, tipo,
        latitud, longitud, elevacion_pies, longitud_pista_m, superficie_pista, activo,
        created_at, updated_at
    ) VALUES (
        p_codigo_oaci, p_codigo_iata, p_nombre, p_ciudad, p_departamento, p_pais, p_tipo,
        p_latitud, p_longitud, p_elevacion_pies, p_longitud_pista_m, p_superficie_pista, p_activo,
        NOW(), NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_aeropuertos_update(
    IN p_id BIGINT,
    IN p_codigo_oaci VARCHAR(4),
    IN p_codigo_iata VARCHAR(3),
    IN p_nombre VARCHAR(255),
    IN p_ciudad VARCHAR(255),
    IN p_departamento VARCHAR(255),
    IN p_pais VARCHAR(255),
    IN p_tipo VARCHAR(20),
    IN p_latitud DECIMAL(10,7),
    IN p_longitud DECIMAL(10,7),
    IN p_elevacion_pies INT,
    IN p_longitud_pista_m INT,
    IN p_superficie_pista VARCHAR(20),
    IN p_activo TINYINT(1)
)
BEGIN
    UPDATE aeropuertos SET
        codigo_oaci = p_codigo_oaci,
        codigo_iata = p_codigo_iata,
        nombre = p_nombre,
        ciudad = p_ciudad,
        departamento = p_departamento,
        pais = p_pais,
        tipo = p_tipo,
        latitud = p_latitud,
        longitud = p_longitud,
        elevacion_pies = p_elevacion_pies,
        longitud_pista_m = p_longitud_pista_m,
        superficie_pista = p_superficie_pista,
        activo = p_activo,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_aeropuertos_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM aeropuertos WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeropuertos_delete');
    }
};
