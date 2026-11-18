<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\menus\domain\PermisoMenuBits;
use src\permisos\domain\PermDl;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Las herramientas de base de datos (`src/devel_db_admin`) solo las usa quien las ve en el menú (permiso de
 * menú `admin_sv`; las migraciones, también `admin_sf`). Antes, cualquier usuario con sesión podía pedir
 * sus datos y las cápsulas que autorizan crear, renombrar o eliminar esquemas (revisión de seguridad del
 * 2026-11-18).
 */
final class HerramientasBdSoloAdministracionTest extends myTest
{
    private const CONTROLADORES = __DIR__ . '/../../../../../src/devel_db_admin/infrastructure/ui/http/controllers';

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

    /** Permisos de menú del usuario (la sesión de los tests no los trae: la app `menus` no figura instalada). */
    private function permisosDeMenu(string ...$nombres): void
    {
        $perm = 0;
        foreach ($nombres as $nombre) {
            $perm |= PermisoMenuBits::map()[$nombre];
        }
        $_SESSION['iPermMenus'] = $perm;
        $_SESSION['oPerm'] = new PermDl();
        $_SESSION['oPerm']->setAccion($perm);
    }

    /** Usuario con permisos de menú de oficina (sm), sin administración. */
    private function usuarioSinAdministracion(): void
    {
        $this->permisosDeMenu('sm');
    }

    /**
     * @param array<string, string> $post
     * @return array<string, mixed>
     */
    private function llamar(string $controlador, array $post): array
    {
        $postAnterior = $_POST;
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADORES . '/' . $controlador . '.php';
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');

        return $json;
    }

    /** @return iterable<string, array{string, array<string, string>}> */
    public static function lecturas(): iterable
    {
        yield 'esquemas de referencia y cápsulas' => ['db_propiedades_data', ['op' => 'db_que_esquema_ref']];
        yield 'lista de migraciones' => ['migraciones_lista_data', []];
        yield 'apps con tablas' => ['apptables_apps_data', []];
        yield 'delegaciones de una región' => ['db_lugar', ['region' => 'H']];
    }

    #[DataProvider('lecturas')]
    public function test_sin_administracion_no_se_ve_nada(string $controlador, array $post): void
    {
        $this->usuarioSinAdministracion();

        $json = $this->llamar($controlador, $post);

        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }

    #[DataProvider('lecturas')]
    public function test_con_administracion_responde(string $controlador, array $post): void
    {
        $this->permisosDeMenu('sm', 'admin_sv');

        $json = $this->llamar($controlador, $post);

        $this->assertTrue($json['success'] ?? null, (string) ($json['mensaje'] ?? ''));
    }

    /** Un administrador sf ve las migraciones (como en su menú), pero no las herramientas de esquemas. */
    public function test_un_administrador_sf_solo_ve_las_migraciones(): void
    {
        $this->permisosDeMenu('admin_sf');

        $this->assertTrue($this->llamar('migraciones_lista_data', [])['success'] ?? null);
        $this->assertFalse($this->llamar('db_propiedades_data', ['op' => 'db_que_esquema_ref'])['success'] ?? null);
    }

    /** @return iterable<string, array{string}> */
    public static function controladores(): iterable
    {
        foreach (glob(self::CONTROLADORES . '/*.php') ?: [] as $f) {
            yield basename($f) => [$f];
        }
    }

    /** Todos los controladores del módulo comprueban el permiso antes de leer la petición o abrir la cápsula. */
    #[DataProvider('controladores')]
    public function test_cada_controlador_comprueba_el_permiso_lo_primero(string $fichero): void
    {
        $codigo = (string) file_get_contents($fichero);
        $guardia = strpos($codigo, 'PermisoHerramientasBdGuard::');
        $this->assertNotFalse($guardia, basename($fichero) . ' no comprueba el permiso');
        foreach (['FilterPostGet::post(', '$_POST', 'HashB::open(', 'DependencyResolver::get(', '->ejecutar('] as $antes) {
            $pos = strpos($codigo, $antes);
            if ($pos !== false) {
                $this->assertGreaterThan($pos === false ? -1 : $guardia, $pos, basename($fichero) . ": «{$antes}» antes de comprobar el permiso");
            }
        }
    }
}
