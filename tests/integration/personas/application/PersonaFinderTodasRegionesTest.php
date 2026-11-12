<?php

declare(strict_types=1);

namespace Tests\integration\personas\application;

use src\personas\application\services\PersonaFinderService;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Buscar una persona activa en todos los esquemas de la BD (lo usa el envío de
 * certificados emitidos): la encuentra en el esquema de su dl, no en los demás,
 * no confunde los esquemas de tablas temporales (pg_temp_N) con una dl, y deja
 * el search_path de las conexiones como estaba.
 */
final class PersonaFinderTodasRegionesTest extends myTest
{
    public function test_encuentra_la_persona_en_el_esquema_de_su_dl_y_restaura_el_search_path(): void
    {
        $mio = ConfigGlobal::mi_id_schema();
        $id = GlobalPdo::get('oDBP')->query(
            "SELECT p.id_nom FROM global.personas p WHERE p.id_schema = $mio AND p.situacion = 'A'
             AND NOT EXISTS (SELECT 1 FROM global.personas o WHERE o.id_nom = p.id_nom AND o.id_schema <> $mio)
             ORDER BY p.id_nom LIMIT 1"
        )->fetchColumn();
        if ($id === false) {
            $this->markTestSkipped('No hay personas activas en mi esquema');
        }
        $pathDe = static fn (string $con): string => (string) GlobalPdo::get($con)->query('SHOW search_path')->fetchColumn();
        $antes = [$pathDe('oDB'), $pathDe('oDBR')];
        // Una tabla temporal aparece en pg_stat_user_tables con esquema pg_temp_N.
        GlobalPdo::get('oDBR')->exec('CREATE TEMP TABLE IF NOT EXISTS tmp_finder_todas_regiones (x int)');
        GlobalPdo::get('oDBR')->exec('ANALYZE tmp_finder_todas_regiones');

        try {
            $resultados = $GLOBALS['container']->get(PersonaFinderService::class)->buscarEnTodasRegiones((int) $id);

            $this->assertSame([ConfigGlobal::mi_region_dl()], array_column($resultados, 'esquema'));
            $this->assertSame((int) $id, $resultados[0]['persona']->getId_nom());
            $this->assertSame($antes, [$pathDe('oDB'), $pathDe('oDBR')]);
            $this->assertSame([], $GLOBALS['container']->get(PersonaFinderService::class)->buscarEnTodasRegiones(999999981));
        } finally {
            GlobalPdo::get('oDBR')->exec('DROP TABLE IF EXISTS tmp_finder_todas_regiones');
        }
    }
}
