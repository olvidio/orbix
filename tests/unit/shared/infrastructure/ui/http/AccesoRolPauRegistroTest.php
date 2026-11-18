<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\ui\http;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\ui\http\AccesoRolPau;

/**
 * Modo de registro de lo que piden los usuarios con un rol limitado a una persona, casa o centro (rol «pau»:
 * p-sacd, casa, centro), para construir la lista de pantallas y endpoints que pueden pedir (revisión de
 * seguridad del 2026-11-18, punto 2). Solo con el fichero bandera; apunta el pau, el método, la ruta y los
 * nombres de los parámetros, nunca sus valores. Los usuarios de oficina (sin pau) no se apuntan.
 */
final class AccesoRolPauRegistroTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_pau_' . bin2hex(random_bytes(4));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        rmdir($this->dir);
    }

    private function registro(): string
    {
        return (string) @file_get_contents($this->dir . '/' . AccesoRolPau::REGISTRO);
    }

    public function test_sin_bandera_no_apunta_nada(): void
    {
        AccesoRolPau::registrar($this->dir, 'ctr', 'POST', '/orbix/frontend/x/controller/y.php', ['id' => '1']);

        $this->assertFileDoesNotExist($this->dir . '/' . AccesoRolPau::REGISTRO);
    }

    public function test_un_usuario_de_oficina_no_se_apunta(): void
    {
        touch($this->dir . '/' . AccesoRolPau::BANDERA_REGISTRO);

        AccesoRolPau::registrar($this->dir, '', 'POST', '/orbix/frontend/x/controller/y.php', ['id' => '1']);

        $this->assertSame('', $this->registro());
    }

    public function test_apunta_pau_ruta_y_nombres_sin_valores(): void
    {
        touch($this->dir . '/' . AccesoRolPau::BANDERA_REGISTRO);

        AccesoRolPau::registrar($this->dir, 'cdc', 'POST', '/orbix/frontend/casas/controller/casa.php?id_ubi=7', ['clave' => 'secreta', 'id_ubi' => '7']);

        $linea = trim($this->registro());
        $this->assertSame("cdc\tPOST\tfrontend/casas/controller/casa.php\tclave,id_ubi", substr($linea, strpos($linea, "\t") + 1));
        $this->assertStringNotContainsString('secreta', $linea);
    }

    /** La ruta se guarda sin el prefijo de la instalación ni el esquema, para poder compararla con la lista. */
    public function test_la_ruta_queda_sin_prefijo(): void
    {
        $this->assertSame('src/casas/casa_datos', AccesoRolPau::ruta('/orbix/H-dlbv/src/casas/casa_datos?x=1'));
        $this->assertSame('src/casas/casa_datos', AccesoRolPau::ruta('/pruebas/src/casas/casa_datos'));
        $this->assertSame('frontend/shared/controller/src_ajax.php/src/ubis/plano_subir', AccesoRolPau::ruta('/orbix/frontend/shared/controller/src_ajax.php/src/ubis/plano_subir'));
        $this->assertSame('index.php', AccesoRolPau::ruta('/orbix/index.php?id_grupmenu=3'));
    }
}
