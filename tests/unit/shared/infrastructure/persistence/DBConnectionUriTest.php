<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * La URI que se pasa a `psql` / `pg_dump` no lleva la contraseña (la línea de órdenes la ve cualquier
 * proceso del servidor y acababa en mensajes de error): va en un fichero `PGPASSFILE` propio del proceso
 * (revisión de seguridad del 2026-11-20, FIC-06).
 */
final class DBConnectionUriTest extends TestCase
{
    public function test_la_uri_no_lleva_la_contrasena_y_esta_queda_en_el_fichero_de_claves(): void
    {
        $uri = (new DBConnection([
            'host' => 'db.example', 'port' => '5432', 'dbname' => 'comun', 'user' => 'orbix_admindb',
            'password' => 'S3cr:eto\\1', 'schema' => 'public',
        ]))->getURI();

        $this->assertSame('postgresql://orbix_admindb@db.example:5432/comun', $uri);
        $this->assertStringNotContainsString('S3cr', $uri);

        $fichero = getenv('PGPASSFILE');
        $this->assertIsString($fichero);
        $this->assertFileExists($fichero);
        $this->assertSame('0600', substr(sprintf('%o', fileperms($fichero)), -4));
        $this->assertStringContainsString('db.example:5432:*:orbix_admindb:S3cr\\:eto\\\\1' . "\n", (string) file_get_contents($fichero));
    }

    public function test_por_socket_no_hay_contrasena_que_guardar(): void
    {
        $uri = (new DBConnection([
            'host' => '/var/run/postgresql', 'port' => '', 'dbname' => 'comun', 'user' => 'postgres', 'password' => '', 'schema' => 'public',
        ]))->getURI();

        $this->assertSame('postgresql:///comun?host=/var/run/postgresql', $uri);
    }
}
