<?php

namespace src\utils_database\domain;

class GenerateIdGlobal
{

    /**
     * Genera un ID personalizado basado en la lógica de secuencias de PostgreSQL
     *
     * @param string $tabla El nombre de la tabla (ej: 'a_actividades_dl')
     * @param string $r_dl El nombre del esquema (ej: 'public', 'delegacion')
     * @return int El ID generado
     * @throws \Exception Si la tabla no está configurada
     */
    public static function generateIdGlobal(string $r_dl, string $tabla, int $id_auto): int
    {
        // 1. Configuración de tablas, secuencias e índices (idx)
        // Si 'schema_fijo' no está definido, se usará la variable $r_dl
        $config = [
            'a_actividades_dl' => ['db' => 'comun', 'seq' => 'a_actividades_dl_id_auto_seq', 'idx' => 0],
            'a_actividades_ex' => ['db' => 'comun', 'seq' => 'a_actividades_ex_id_auto_seq', 'idx' => 0, 'schema_fijo' => 'resto'],

            'p_numerarios' => ['db' => 'svf', 'seq' => 'p_numerarios_id_auto_seq', 'idx' => 1],
            'p_agregados' => ['db' => 'svf', 'seq' => 'p_agregados_id_auto_seq', 'idx' => 2],
            'p_supernumerarios' => ['db' => 'svf', 'seq' => 'p_supernumerarios_id_auto_seq', 'idx' => 3],
            'p_sssc' => ['db' => 'svf', 'seq' => 'p_sssc_id_auto_seq', 'idx' => 4],
            'p_nax' => ['db' => 'svf', 'seq' => 'p_nax_id_auto_seq', 'idx' => 5],
            'p_de_paso_ex' => ['db' => 'svf', 'seq' => 'p_de_paso_ex_id_auto_seq', 'idx' => 6],

            'u_centros_dl' => ['db' => 'svf', 'seq' => 'u_centros_dl_id_auto_seq', 'idx' => 8],
            'u_centros_ex' => ['db' => 'svf', 'seq' => 'u_centros_ex_id_auto_seq', 'idx' => 8],
            'u_dir_ctr_ex' => ['db' => 'svf', 'seq' => 'u_dir_ctr_ex_id_auto_seq', 'idx' => 8],
            'u_dir_ctr_dl' => ['db' => 'svf', 'seq' => 'u_dir_ctr_dl_id_auto_seq', 'idx' => 8],

            'u_cdc_dl' => ['db' => 'comun', 'seq' => 'u_cdc_dl_id_auto_seq', 'idx' => 9],
            'u_cdc_ex' => ['db' => 'comun', 'seq' => 'u_cdc_ex_id_auto_seq', 'idx' => 9],
            'u_dir_cdc_ex' => ['db' => 'comun', 'seq' => 'u_dir_cdc_ex_id_auto_seq', 'idx' => 9],
            'u_dir_cdc_dl' => ['db' => 'comun', 'seq' => 'u_dir_cdc_dl_id_auto_seq', 'idx' => 9],
        ];

        if (!isset($config[$tabla])) {
            throw new \Exception("Tabla no reconocida para generar ID: $tabla");
        }

        // 2. Preparar datos de la secuencia
        $info = $config[$tabla];
        $idx = $info['idx'];

        $oDbl = $GLOBALS['oDBPC'];

        // 4. Obtener el ID del esquema (variable 'n' en el SQL original)
        $sqlSchema = "SELECT id FROM public.db_idschema WHERE schema = '$r_dl'";
        $stmt = $oDbl->query($sqlSchema);
        // Asumiendo fetchColumn() o similar para obtener un solo valor escalar
        $n = $stmt->fetchColumn();

        if ($n === false) {
            throw new \Exception("No se encontró ID para el esquema: $r_dl");
        }

        // 5. Retornar el ID formateado
        if ($idx === 0) {
            return (int)($n . $id_auto);
        }
        return (int)($n . $idx . $id_auto);
    }

}