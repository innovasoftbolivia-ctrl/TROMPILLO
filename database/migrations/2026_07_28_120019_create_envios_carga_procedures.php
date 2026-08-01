<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_insert');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_envios_carga_insert(
    IN p_guia VARCHAR(20),
    IN p_vuelo_id BIGINT,
    IN p_remitente VARCHAR(255),
    IN p_remitente_documento VARCHAR(30),
    IN p_destinatario VARCHAR(255),
    IN p_destinatario_documento VARCHAR(30),
    IN p_descripcion TEXT,
    IN p_peso_kg DECIMAL(8,2),
    IN p_valor_declarado DECIMAL(12,2),
    IN p_costo_envio DECIMAL(12,2),
    IN p_estado VARCHAR(20)
)
BEGIN
    INSERT INTO envios_carga (
        guia,
        vuelo_id,
        remitente,
        remitente_documento,
        destinatario,
        destinatario_documento,
        descripcion,
        peso_kg,
        valor_declarado,
        costo_envio,
        estado,
        created_at,
        updated_at
    ) VALUES (
        p_guia,
        p_vuelo_id,
        p_remitente,
        p_remitente_documento,
        p_destinatario,
        p_destinatario_documento,
        p_descripcion,
        p_peso_kg,
        p_valor_declarado,
        p_costo_envio,
        p_estado,
        NOW(),
        NOW()
    );
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_update');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_envios_carga_update(
    IN p_id BIGINT,
    IN p_guia VARCHAR(20),
    IN p_vuelo_id BIGINT,
    IN p_remitente VARCHAR(255),
    IN p_remitente_documento VARCHAR(30),
    IN p_destinatario VARCHAR(255),
    IN p_destinatario_documento VARCHAR(30),
    IN p_descripcion TEXT,
    IN p_peso_kg DECIMAL(8,2),
    IN p_valor_declarado DECIMAL(12,2),
    IN p_costo_envio DECIMAL(12,2),
    IN p_estado VARCHAR(20)
)
BEGIN
    UPDATE envios_carga
    SET
        guia = p_guia,
        vuelo_id = p_vuelo_id,
        remitente = p_remitente,
        remitente_documento = p_remitente_documento,
        destinatario = p_destinatario,
        destinatario_documento = p_destinatario_documento,
        descripcion = p_descripcion,
        peso_kg = p_peso_kg,
        valor_declarado = p_valor_declarado,
        costo_envio = p_costo_envio,
        estado = p_estado,
        updated_at = NOW()
    WHERE id = p_id;
END
SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_delete');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_envios_carga_delete(
    IN p_id BIGINT
)
BEGIN
    DELETE FROM envios_carga WHERE id = p_id;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_envios_carga_delete');
    }
};
