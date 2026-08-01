<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_pilotos_insert(
    IN p_empleado_id BIGINT,
    IN p_licencia_numero VARCHAR(30),
    IN p_tipo_licencia VARCHAR(10),
    IN p_horas_vuelo DECIMAL(10,1),
    IN p_vencimiento_licencia DATE,
    IN p_vencimiento_medico DATE,
    IN p_habilitaciones VARCHAR(255)
)
BEGIN
    INSERT INTO pilotos (
        empleado_id,
        licencia_numero,
        tipo_licencia,
        horas_vuelo,
        vencimiento_licencia,
        vencimiento_medico,
        habilitaciones,
        created_at,
        updated_at
    ) VALUES (
        p_empleado_id,
        p_licencia_numero,
        p_tipo_licencia,
        p_horas_vuelo,
        p_vencimiento_licencia,
        p_vencimiento_medico,
        p_habilitaciones,
        NOW(),
        NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_pilotos_update(
    IN p_id BIGINT,
    IN p_empleado_id BIGINT,
    IN p_licencia_numero VARCHAR(30),
    IN p_tipo_licencia VARCHAR(10),
    IN p_horas_vuelo DECIMAL(10,1),
    IN p_vencimiento_licencia DATE,
    IN p_vencimiento_medico DATE,
    IN p_habilitaciones VARCHAR(255)
)
BEGIN
    UPDATE pilotos SET
        empleado_id = p_empleado_id,
        licencia_numero = p_licencia_numero,
        tipo_licencia = p_tipo_licencia,
        horas_vuelo = p_horas_vuelo,
        vencimiento_licencia = p_vencimiento_licencia,
        vencimiento_medico = p_vencimiento_medico,
        habilitaciones = p_habilitaciones,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_pilotos_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM pilotos WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pilotos_delete');
    }
};
