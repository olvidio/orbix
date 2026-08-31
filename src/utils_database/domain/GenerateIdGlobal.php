<?php

namespace src\utils_database\domain;

use src\shared\infrastructure\GlobalPdo;

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
        $config = self::tableConfig();
        if (!isset($config[$tabla])) {
            throw new \Exception("Tabla no reconocida para generar ID: $tabla");
        }

        $idx = $config[$tabla]['idx'];
        $schema = self::schemaForLookup($tabla, $r_dl);

        $oDbl = GlobalPdo::get('oDBPC');

        $stmt = $oDbl->prepare('SELECT id FROM public.db_idschema WHERE schema = :schema');
        if ($stmt === false) {
            throw new \Exception("Error consultando ID para el esquema: $schema");
        }
        $stmt->execute(['schema' => $schema]);
        $n = $stmt->fetchColumn();

        if ($n === false) {
            throw new \Exception("No se encontró ID para el esquema: $schema");
        }

        return self::composeId($n, $idx, $id_auto);
    }

    /**
     * Esquema cuyo id de `db_idschema` se usa como prefijo.
     * `p_de_paso_ex` → restov (-1001) / restof (-2001);
     * `a_actividades_ex` → resto (-3001).
     */
    public static function schemaForLookup(string $tabla, string $r_dl): string
    {
        $config = self::tableConfig();
        if (!isset($config[$tabla])) {
            throw new \Exception("Tabla no reconocida para generar ID: $tabla");
        }
        $fijo = $config[$tabla]['schema_fijo'] ?? '';
        if ($fijo === '') {
            return $r_dl;
        }
        // Personas de paso: restov (-1001) o restof (-2001) según sv/sf.
        if ($fijo === 'restov' && ($r_dl === 'restov' || $r_dl === 'restof')) {
            return $r_dl;
        }

        return $fijo;
    }

    /**
     * Concatena id de esquema + índice de tabla + id_auto (p. ej. -1001, 6, 1351 → -100161351).
     */
    public static function composeId(int|string $schemaId, int $idx, int $idAuto): int
    {
        if ($idx === 0) {
            return (int)((string) $schemaId . $idAuto);
        }

        return (int)((string) $schemaId . $idx . $idAuto);
    }

    /**
     * @return array<string, array{db: string, seq: string, idx: int, schema_fijo?: string}>
     */
    private static function tableConfig(): array
    {
        return [
            'a_actividades_dl' => ['db' => 'comun', 'seq' => 'a_actividades_dl_id_auto_seq', 'idx' => 0],
            'a_actividades_ex' => ['db' => 'comun', 'seq' => 'a_actividades_ex_id_auto_seq', 'idx' => 0, 'schema_fijo' => 'resto'],

            'p_numerarios' => ['db' => 'svf', 'seq' => 'p_numerarios_id_auto_seq', 'idx' => 1],
            'p_agregados' => ['db' => 'svf', 'seq' => 'p_agregados_id_auto_seq', 'idx' => 2],
            'p_supernumerarios' => ['db' => 'svf', 'seq' => 'p_supernumerarios_id_auto_seq', 'idx' => 3],
            'p_sssc' => ['db' => 'svf', 'seq' => 'p_sssc_id_auto_seq', 'idx' => 4],
            'p_nax' => ['db' => 'svf', 'seq' => 'p_nax_id_auto_seq', 'idx' => 5],
            'p_de_paso_ex' => ['db' => 'svf', 'seq' => 'p_de_paso_ex_id_auto_seq', 'idx' => 6, 'schema_fijo' => 'restov'],

            'u_centros_dl' => ['db' => 'svf', 'seq' => 'u_centros_dl_id_auto_seq', 'idx' => 8],
            'u_centros_ex' => ['db' => 'svf', 'seq' => 'u_centros_ex_id_auto_seq', 'idx' => 8],
            'u_dir_ctr_ex' => ['db' => 'svf', 'seq' => 'u_dir_ctr_ex_id_auto_seq', 'idx' => 8],
            'u_dir_ctr_dl' => ['db' => 'svf', 'seq' => 'u_dir_ctr_dl_id_auto_seq', 'idx' => 8],

            'u_cdc_dl' => ['db' => 'comun', 'seq' => 'u_cdc_dl_id_auto_seq', 'idx' => 9],
            'u_cdc_ex' => ['db' => 'comun', 'seq' => 'u_cdc_ex_id_auto_seq', 'idx' => 9],
            'u_dir_cdc_ex' => ['db' => 'comun', 'seq' => 'u_dir_cdc_ex_id_auto_seq', 'idx' => 9],
            'u_dir_cdc_dl' => ['db' => 'comun', 'seq' => 'u_dir_cdc_dl_id_auto_seq', 'idx' => 9],
        ];
    }

}
