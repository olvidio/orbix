<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\menus\domain\PermisoMenuBits;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Herramientas de administración que viven fuera de `src/devel_db_admin` y trabajan sobre todos los
 * esquemas: las resincronizaciones de copias entre esquemas y la estructura de tablas del generador de
 * código. Solo las usa quien ve las herramientas de base de datos en el menú (permiso `admin_sv`). Antes,
 * cualquier usuario con sesión podía lanzarlas con un POST (revisión de seguridad del 2026-11-19,
 * AUT-10 y AUT-14).
 */
final class OtrasHerramientasSoloAdministracionTest extends myTest
{
    private const SRC = __DIR__ . '/../../../../../src';

    private const CONTROLADORES = [
        'resincronizar sacd' => '/personas/infrastructure/ui/http/controllers/sacd_resincronizar.php',
        'resincronizar cargos' => '/actividadcargos/infrastructure/ui/http/controllers/cargos_activ_resincronizar.php',
        'resincronizar centros' => '/ubis/infrastructure/ui/http/controllers/centros_resincronizar.php',
    ];

    /** @var array<string, mixed> */
    private array $sesionPrevia = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionPrevia = $_SESSION;
    }

    public function tearDown(): void
    {
        $_SESSION = $this->sesionPrevia;
        parent::tearDown();
    }

    /** @return iterable<string, array{string}> */
    public static function controladores(): iterable
    {
        foreach (self::CONTROLADORES as $nombre => $ruta) {
            yield $nombre => [$ruta];
        }
    }

    #[DataProvider('controladores')]
    public function test_comprueba_el_permiso_lo_primero(string $ruta): void
    {
        $codigo = (string) file_get_contents(self::SRC . $ruta);
        $guardia = strpos($codigo, 'PermisoHerramientasBdGuard::permiteEsquemas()');
        $this->assertNotFalse($guardia, basename($ruta) . ' no comprueba el permiso');
        foreach (['require ', 'FilterPostGet::post(', '$_POST', 'HashB::open('] as $antes) {
            $pos = strpos($codigo, $antes);
            if ($pos !== false) {
                $this->assertGreaterThan($guardia, $pos, basename($ruta) . ": «{$antes}» antes de comprobar el permiso");
            }
        }
    }

    #[DataProvider('controladores')]
    public function test_sin_administracion_no_se_ejecuta(string $ruta): void
    {
        $codigo = (string) file_get_contents(self::SRC . $ruta);
        if (!str_contains($codigo, 'PermisoHerramientasBdGuard::')) {
            $this->fail(basename($ruta) . ' no comprueba el permiso (no se ejecuta para no lanzar la herramienta)');
        }
        $_SESSION['iPermMenus'] = PermisoMenuBits::map()['sm'];
        $postAnterior = $_POST;
        $_POST = ['db' => 'comun', 'tabla' => 'public.aux_roles'];
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::SRC . $ruta;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
        }

        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');
        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }
}
