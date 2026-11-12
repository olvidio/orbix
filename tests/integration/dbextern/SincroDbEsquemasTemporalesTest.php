<?php

declare(strict_types=1);

namespace Tests\integration\dbextern;

use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\SincroDB;
use src\dbextern\infrastructure\persistence\postgresql\OdbcDlListasRepository;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Posibles personas de Orbix en otras dl para una persona de las listas: recorre
 * los esquemas de pg_stat_user_tables, donde una tabla temporal aparece con
 * esquema pg_temp_N; no debe tomarlo por una dl.
 */
final class SincroDbEsquemasTemporalesTest extends myTest
{
    public function test_posibles_orbix_otras_dl_ignora_los_esquemas_temporales(): void
    {
        $apellido = GlobalPdo::get('oDBP')->query("SELECT apellido1 FROM global.personas WHERE situacion = 'A' AND id_tabla = 'n' ORDER BY id_nom LIMIT 1")->fetchColumn();
        if ($apellido === false) {
            $this->markTestSkipped('No hay numerarios activos');
        }
        $bdu = $this->createMock(PersonaBDU::class);
        $bdu->method('getApellido1_sinprep')->willReturn((string) $apellido);
        $repoBdu = $this->createMock(PersonaBDURepositoryInterface::class);
        $repoBdu->method('findById')->willReturn($bdu);
        /** @var SincroDB $sincro */
        $sincro = $GLOBALS['container']->make(SincroDB::class, [
            'personaBDURepository' => $repoBdu,
            // Sin BD de Listas en desarrollo (su constructor hace exit si no conecta).
            'dlListasRepository' => $this->createMock(OdbcDlListasRepository::class),
        ]);
        $sincro->setTipo_persona('n');

        $oDBR = GlobalPdo::get('oDBR');
        $oDBR->exec('CREATE TEMP TABLE IF NOT EXISTS tmp_sincro_temporales (x int)');
        $oDBR->exec('ANALYZE tmp_sincro_temporales');
        try {
            $posibles = $sincro->posiblesOrbixOtrasDl(1);
            $esquemas = array_unique(array_merge([], ...array_map(static fn (array $l): array => array_column($l, 'esquema'), $posibles)));
            $this->assertIsArray($posibles);
            foreach ($esquemas as $e) {
                $this->assertStringStartsNotWith('pg_', $e);
            }
        } finally {
            $oDBR->exec('DROP TABLE IF EXISTS tmp_sincro_temporales');
        }
    }
}
