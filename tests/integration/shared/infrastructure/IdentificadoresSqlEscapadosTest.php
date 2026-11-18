<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use Tests\myTest;

/**
 * Nombres de tabla y de esquema que llegan de las herramientas de esquemas (revisión de seguridad del
 * 2026-11-18). Solo leen, con la conexión de mantenimiento de las BD de pruebas.
 */
final class IdentificadoresSqlEscapadosTest extends myTest
{
    public function setUp(): void
    {
        if (getenv('WEBDIR') !== 'pruebas') {
            $this->markTestSkipped('Conexión de mantenimiento: solo en el entorno de pruebas (tools/qa/phpunit_protegido.php).');
        }
        parent::setUp();
    }

    /**
     * «Esquemas con esta tabla» (`db_propiedades_data`, `mover_tabla`): el nombre de la tabla iba entre
     * comillas en el SQL y el resultado vuelve en el JSON, así que un UNION leía cualquier cosa.
     */
    public function test_esquemas_con_una_tabla_con_comillas_no_lee_otra_cosa(): void
    {
        $r = (new DBPropiedades())->array_esquemas_con_tabla("zz_no_existe' UNION SELECT usename::text FROM pg_user --");

        $this->assertSame([], $r);
    }

    public function test_esquemas_con_una_tabla_real(): void
    {
        $this->assertContains('H-dlbv', (new DBPropiedades())->array_esquemas_con_tabla('e_notas_dl'));
    }

    /** El nombre de esquema de una conexión no puede cerrar las comillas del `SET search_path`. */
    public function test_el_esquema_de_la_conexion_no_ejecuta_otra_sentencia(): void
    {
        $config = (new ConfigDB('importar'))->getEsquema('publicv');
        $con = new DBConnection($config);
        $con->setEsquema('zz"; SET search_path TO public; --');

        $searchPath = (string) $con->getPDO()->query('SHOW search_path')->fetchColumn();

        $this->assertNotSame('public', $searchPath);
        $this->assertStringContainsString('zz', $searchPath);
    }
}
