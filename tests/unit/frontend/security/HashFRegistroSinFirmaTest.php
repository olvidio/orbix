<?php

declare(strict_types=1);

namespace Tests\unit\frontend\security;

use frontend\shared\security\HashF;
use PHPUnit\Framework\TestCase;

/**
 * Modo de registro de las peticiones que llegan sin firma `h` (para medir qué pantallas se romperían si la
 * firma fuera obligatoria). Solo funciona con el fichero bandera; apunta método, ruta y nombres de los
 * parámetros, nunca sus valores.
 */
final class HashFRegistroSinFirmaTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_hashf_' . bin2hex(random_bytes(4));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        rmdir($this->dir);
    }

    public function test_sin_bandera_no_apunta_nada(): void
    {
        HashF::registrarSinFirma($this->dir, 'POST', '/orbix/frontend/x/controller/y.php', ['id' => '1']);

        $this->assertFileDoesNotExist($this->dir . '/' . HashF::REGISTRO_SIN_FIRMA);
    }

    public function test_con_bandera_apunta_la_ruta_y_los_nombres_sin_valores(): void
    {
        touch($this->dir . '/' . HashF::BANDERA_REGISTRO_SIN_FIRMA);

        HashF::registrarSinFirma($this->dir, 'POST', '/orbix/frontend/x/controller/y.php?id_nom=5', ['clave' => 'secreta', 'id_nom' => '5']);

        $linea = trim((string) file_get_contents($this->dir . '/' . HashF::REGISTRO_SIN_FIRMA));
        $this->assertSame("POST\t/orbix/frontend/x/controller/y.php\tclave,id_nom", substr($linea, strpos($linea, "\t") + 1));
        $this->assertStringNotContainsString('secreta', $linea);
        $this->assertStringNotContainsString('=5', $linea);
    }
}
