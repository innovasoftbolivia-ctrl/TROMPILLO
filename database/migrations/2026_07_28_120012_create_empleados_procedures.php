<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_empleados_insert(
    IN p_nombres VARCHAR(255),
    IN p_apellidos VARCHAR(255),
    IN p_tipo_documento VARCHAR(10),
    IN p_numero_documento VARCHAR(30),
    IN p_cargo VARCHAR(20),
    IN p_telefono VARCHAR(30),
    IN p_email VARCHAR(255),
    IN p_fecha_nacimiento DATE,
    IN p_fecha_contratacion DATE,
    IN p_salario DECIMAL(12,2),
    IN p_activo TINYINT(1)
)
BEGIN
    INSERT INTO empleados (
        nombres, apellidos, tipo_documento, numero_documento, cargo,
        telefono, email, fecha_nacimiento, fecha_contratacion, salario, activo,
        created_at, updated_at
    ) VALUES (
        p_nombres, p_apellidos, p_tipo_documento, p_numero_documento, p_cargo,
        p_telefono, p_email, p_fecha_nacimiento, p_fecha_contratacion, p_salario, p_activo,
        NOW(), NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_empleados_update(
    IN p_id BIGINT,
    IN p_nombres VARCHAR(255),
    IN p_apellidos VARCHAR(255),
    IN p_tipo_documento VARCHAR(10),
    IN p_numero_documento VARCHAR(30),
    IN p_cargo VARCHAR(20),
    IN p_telefono VARCHAR(30),
    IN p_email VARCHAR(255),
    IN p_fecha_nacimiento DATE,
    IN p_fecha_contratacion DATE,
    IN p_salario DECIMAL(12,2),
    IN p_activo TINYINT(1)
)
BEGIN
    UPDATE empleados SET
        nombres = p_nombres,
        apellidos = p_apellidos,
        tipo_documento = p_tipo_documento,
        numero_documento = p_numero_documento,
        cargo = p_cargo,
        telefono = p_telefono,
        email = p_email,
        fecha_nacimiento = p_fecha_nacimiento,
        fecha_contratacion = p_fecha_contratacion,
        salario = p_salario,
        activo = p_activo,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_empleados_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM empleados WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_empleados_delete');
    }
};
