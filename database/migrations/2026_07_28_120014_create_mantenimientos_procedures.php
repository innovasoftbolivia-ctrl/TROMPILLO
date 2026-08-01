<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_mantenimientos_insert(
    IN p_aeronave_id BIGINT,
    IN p_tecnico_id BIGINT,
    IN p_tipo VARCHAR(20),
    IN p_descripcion TEXT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE,
    IN p_horas_vuelo_aeronave DECIMAL(10,1),
    IN p_costo DECIMAL(12,2),
    IN p_estado VARCHAR(20)
)
BEGIN
    INSERT INTO mantenimientos (
        aeronave_id,
        tecnico_id,
        tipo,
        descripcion,
        fecha_inicio,
        fecha_fin,
        horas_vuelo_aeronave,
        costo,
        estado,
        created_at,
        updated_at
    ) VALUES (
        p_aeronave_id,
        p_tecnico_id,
        p_tipo,
        p_descripcion,
        p_fecha_inicio,
        p_fecha_fin,
        p_horas_vuelo_aeronave,
        p_costo,
        p_estado,
        NOW(),
        NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_mantenimientos_update(
    IN p_id BIGINT,
    IN p_aeronave_id BIGINT,
    IN p_tecnico_id BIGINT,
    IN p_tipo VARCHAR(20),
    IN p_descripcion TEXT,
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE,
    IN p_horas_vuelo_aeronave DECIMAL(10,1),
    IN p_costo DECIMAL(12,2),
    IN p_estado VARCHAR(20)
)
BEGIN
    UPDATE mantenimientos SET
        aeronave_id = p_aeronave_id,
        tecnico_id = p_tecnico_id,
        tipo = p_tipo,
        descripcion = p_descripcion,
        fecha_inicio = p_fecha_inicio,
        fecha_fin = p_fecha_fin,
        horas_vuelo_aeronave = p_horas_vuelo_aeronave,
        costo = p_costo,
        estado = p_estado,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_mantenimientos_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM mantenimientos WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_mantenimientos_delete');
    }
};
