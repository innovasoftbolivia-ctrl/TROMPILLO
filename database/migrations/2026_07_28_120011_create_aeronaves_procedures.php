<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_insert');
        DB::unprepared("
            CREATE PROCEDURE sp_aeronaves_insert(
                IN p_matricula VARCHAR(10),
                IN p_modelo VARCHAR(255),
                IN p_fabricante VARCHAR(255),
                IN p_ano_fabricacion INT,
                IN p_capacidad_pasajeros INT,
                IN p_capacidad_carga_kg DECIMAL(8,2),
                IN p_peso_vacio_kg DECIMAL(8,2),
                IN p_peso_maximo_despegue_kg DECIMAL(8,2),
                IN p_autonomia_km INT,
                IN p_velocidad_crucero_kmh INT,
                IN p_horas_vuelo_totales DECIMAL(10,1),
                IN p_fecha_ultima_revision DATE,
                IN p_fecha_proxima_revision DATE,
                IN p_estado VARCHAR(20)
            )
            BEGIN
                INSERT INTO aeronaves (
                    matricula,
                    modelo,
                    fabricante,
                    ano_fabricacion,
                    capacidad_pasajeros,
                    capacidad_carga_kg,
                    peso_vacio_kg,
                    peso_maximo_despegue_kg,
                    autonomia_km,
                    velocidad_crucero_kmh,
                    horas_vuelo_totales,
                    fecha_ultima_revision,
                    fecha_proxima_revision,
                    estado,
                    created_at,
                    updated_at
                ) VALUES (
                    p_matricula,
                    p_modelo,
                    p_fabricante,
                    p_ano_fabricacion,
                    p_capacidad_pasajeros,
                    p_capacidad_carga_kg,
                    p_peso_vacio_kg,
                    p_peso_maximo_despegue_kg,
                    p_autonomia_km,
                    p_velocidad_crucero_kmh,
                    p_horas_vuelo_totales,
                    p_fecha_ultima_revision,
                    p_fecha_proxima_revision,
                    p_estado,
                    NOW(),
                    NOW()
                );
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_update');
        DB::unprepared("
            CREATE PROCEDURE sp_aeronaves_update(
                IN p_id BIGINT,
                IN p_matricula VARCHAR(10),
                IN p_modelo VARCHAR(255),
                IN p_fabricante VARCHAR(255),
                IN p_ano_fabricacion INT,
                IN p_capacidad_pasajeros INT,
                IN p_capacidad_carga_kg DECIMAL(8,2),
                IN p_peso_vacio_kg DECIMAL(8,2),
                IN p_peso_maximo_despegue_kg DECIMAL(8,2),
                IN p_autonomia_km INT,
                IN p_velocidad_crucero_kmh INT,
                IN p_horas_vuelo_totales DECIMAL(10,1),
                IN p_fecha_ultima_revision DATE,
                IN p_fecha_proxima_revision DATE,
                IN p_estado VARCHAR(20)
            )
            BEGIN
                UPDATE aeronaves SET
                    matricula = p_matricula,
                    modelo = p_modelo,
                    fabricante = p_fabricante,
                    ano_fabricacion = p_ano_fabricacion,
                    capacidad_pasajeros = p_capacidad_pasajeros,
                    capacidad_carga_kg = p_capacidad_carga_kg,
                    peso_vacio_kg = p_peso_vacio_kg,
                    peso_maximo_despegue_kg = p_peso_maximo_despegue_kg,
                    autonomia_km = p_autonomia_km,
                    velocidad_crucero_kmh = p_velocidad_crucero_kmh,
                    horas_vuelo_totales = p_horas_vuelo_totales,
                    fecha_ultima_revision = p_fecha_ultima_revision,
                    fecha_proxima_revision = p_fecha_proxima_revision,
                    estado = p_estado,
                    updated_at = NOW()
                WHERE id = p_id;
            END
        ");

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_delete');
        DB::unprepared("
            CREATE PROCEDURE sp_aeronaves_delete(
                IN p_id BIGINT
            )
            BEGIN
                DELETE FROM aeronaves WHERE id = p_id;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_update');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_aeronaves_delete');
    }
};
