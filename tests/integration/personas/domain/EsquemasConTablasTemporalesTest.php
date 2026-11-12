<?php

declare(strict_types=1);

namespace Tests\integration\personas\domain;

use src\personas\domain\Trasladar;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Los listados de esquemas salen de pg_stat_user_tables, donde una tabla
 * temporal (de esta u otra sesión) aparece con esquema pg_temp_N. Buscar en qué
 * esquemas está una persona no debe tomarlo por una dl.
 */
final class EsquemasConTablasTemporalesTest extends myTest
{
    public function test_trasladar_get_esquemas_ignora_los_esquemas_temporales(): void
    {
        $mio = ConfigGlobal::mi_id_schema();
        $id = GlobalPdo::get('oDBP')->query(
            "SELECT p.id_nom FROM global.personas p WHERE p.id_schema = $mio AND p.situacion = 'A' AND p.id_tabla = 'n'
             AND NOT EXISTS (SELECT 1 FROM global.personas o WHERE o.id_nom = p.id_nom AND o.id_schema <> $mio)
             ORDER BY p.id_nom LIMIT 1"
        )->fetchColumn();
        if ($id === false) {
            $this->markTestSkipped('No hay numerarios activos en mi esquema');
        }
        $oDBR = GlobalPdo::get('oDBR');
        $pathAntes = (string) $oDBR->query('SHOW search_path')->fetchColumn();
        $oDBR->exec('CREATE TEMP TABLE IF NOT EXISTS tmp_esquemas_temporales (x int)');
        $oDBR->exec('ANALYZE tmp_esquemas_temporales');
        try {
            $esquemas = $GLOBALS['container']->get(Trasladar::class)->getEsquemas((int) $id, 'n');
            $this->assertSame([ConfigGlobal::mi_region_dl()], array_column($esquemas, 'schemaname'));
            $this->assertSame($pathAntes, (string) $oDBR->query('SHOW search_path')->fetchColumn());
        } finally {
            $oDBR->exec('DROP TABLE IF EXISTS tmp_esquemas_temporales');
        }
    }
}
