<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use Tests\myTest;

/**
 * Test-guardia del entorno: con `WEBDIR=pruebas` **todas** las conexiones de la suite van a las BD
 * `pruebas-*` (clones que se pueden regenerar); sin él, ninguna.
 *
 * `tools/qa/phpunit_protegido.php` lo ejecuta antes de la pasada: si alguna conexión se quedara
 * apuntando a las BD de desarrollo (un fichero `pruebas-*.conn.inc` que falta, una conexión nueva que
 * no pasa por `ConfigDB`), los tests volverían a escribir en ellas sin que nada lo avisara.
 * Fase 6, paso 6.1, del plan de revisión de tests.
 */
final class ConexionesDelEntornoTest extends myTest
{
    public function test_las_conexiones_van_a_las_bd_del_entorno(): void
    {
        $enPruebas = getenv('WEBDIR') === 'pruebas';
        $bases = [];
        foreach ($GLOBALS as $nombre => $valor) {
            if (!is_string($nombre) || !str_starts_with($nombre, 'oDB') || !$valor instanceof PDO
                || $valor->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
                continue;
            }
            $st = $valor->query('SELECT current_database()');
            $bases[$nombre] = $st === false ? '?' : (string) $st->fetchColumn();
        }
        $this->assertNotSame([], $bases, 'la suite abre conexiones a PostgreSQL');

        // Las conexiones de mantenimiento (`importar`: copias, roles, vistas, zonas) se abren a demanda.
        $importar = new ConfigDB('importar');
        foreach (['public', 'publicv', 'publicv-e', 'publicf', 'public_select', 'publicv-e_select'] as $clave) {
            $conn = $importar->getConexionImportarReplica($clave);
            $bases["importar:$clave"] = is_string($conn['dbname'] ?? null) ? $conn['dbname'] : '?';
        }

        $fuera = array_filter($bases, static fn (string $bd): bool => str_starts_with($bd, 'pruebas-') !== $enPruebas);
        $this->assertSame(
            [],
            $fuera,
            ($enPruebas ? 'con WEBDIR=pruebas hay conexiones a BD que no son de pruebas' : 'sin WEBDIR=pruebas hay conexiones a BD de pruebas')
            . ': ' . json_encode($fuera) . '; todas: ' . json_encode($bases),
        );
    }
}
