<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_pasajeros_insert(
                IN p_nombres VARCHAR(255),
                IN p_apellidos VARCHAR(255),
                IN p_tipo_documento VARCHAR(10),
                IN p_numero_documento VARCHAR(30),
                IN p_fecha_nacimiento DATE,
                IN p_nacionalidad VARCHAR(255),
                IN p_telefono VARCHAR(30),
                IN p_email VARCHAR(255),
                IN p_peso_kg DECIMAL(5,2),
                IN p_contacto_emergencia VARCHAR(255),
                IN p_telefono_emergencia VARCHAR(30)
            )
            BEGIN
                INSERT INTO pasajeros (
                    nombres,
                    apellidos,
                    tipo_documento,
                    numero_documento,
                    fecha_nacimiento,
                    nacionalidad,
                    telefono,
                    email,
                    peso_kg,
                    contacto_emergencia,
                    telefono_emergencia,
                    created_at,
                    updated_at
                ) VALUES (
                    p_nombres,
                    p_apellidos,
                    p_tipo_documento,
                    p_numero_documento,
                    p_fecha_nacimiento,
                    p_nacionalidad,
                    p_telefono,
                    p_email,
                    p_peso_kg,
                    p_contacto_emergencia,
                    p_telefono_emergencia,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_update');
        DB::unprepared("
            CREATE PROCEDURE sp_pasajeros_update(
                IN p_id BIGINT,
                IN p_nombres VARCHAR(255),
                IN p_apellidos VARCHAR(255),
                IN p_tipo_documento VARCHAR(10),
                IN p_numero_documento VARCHAR(30),
                IN p_fecha_nacimiento DATE,
                IN p_nacionalidad VARCHAR(255),
                IN p_telefono VARCHAR(30),
                IN p_email VARCHAR(255),
                IN p_peso_kg DECIMAL(5,2),
                IN p_contacto_emergencia VARCHAR(255),
                IN p_telefono_emergencia VARCHAR(30)
            )
            BEGIN
                UPDATE pasajeros SET
                    nombres = p_nombres,
                    apellidos = p_apellidos,
                    tipo_documento = p_tipo_documento,
                    numero_documento = p_numero_documento,
                    fecha_nacimiento = p_fecha_nacimiento,
                    nacionalidad = p_nacionalidad,
                    telefono = p_telefono,
                    email = p_email,
                    peso_kg = p_peso_kg,
                    contacto_emergencia = p_contacto_emergencia,
                    telefono_emergencia = p_telefono_emergencia,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_pasajeros_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM pasajeros WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pasajeros_delete');
    }
};
