<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\controllers;

use src\menus\domain\PermisoMenuBits;
use src\permisos\domain\PermDl;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * Pantalla genérica de tablas (`tablaDB_*`): solo da de alta, modifica o borra quien puede según la misma
 * regla con la que se le muestra la tabla ({@see \src\shared\application\PermisoTablaGenerica}). Antes la
 * lista firmaba una cápsula de borrado para cada fila y el formulario firmaba la de guardado con el modo y
 * la clave del POST, para cualquier usuario con sesión (revisión de seguridad del 2026-11-18).
 *
 * Usa «posibles idiomas» (`InfoLocales`), que en el menú pide `admin_sv`. Solo el caso de guardar escribe,
 * y borra una clave que no existe.
 */
final class TablaGenericaPermisoTest extends myTest
{
    private const CONTROLADORES = __DIR__ . '/../../../../../src/shared/infrastructure/ui/http/controllers';
    private const CLASE = 'src\\usuarios\\domain\\InfoLocales';

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

    /**
     * @param array<string, mixed> $post
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

    /** @return array<string, mixed> */
    private static function datos(array $json): array
    {
        $d = $json['data'] ?? [];

        return is_string($d) ? (array) json_decode($d, true) : (array) $d;
    }

    private static function sPkey(string $idLocale): string
    {
        return FuncTablasSupport::urlsafeB64encode(json_encode($idLocale, JSON_THROW_ON_ERROR));
    }

    public function test_formulario_sin_permiso_no_firma(): void
    {
        $this->permisosDeMenu('sm');

        $json = $this->llamar('tablaDB_formulario_datos', [
            'clase_info' => self::CLASE, 'mod' => 'editar',
            'a_pkey' => json_encode('es_ES.UTF-8'), 's_pkey' => self::sPkey('es_ES.UTF-8'),
        ]);

        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }

    public function test_formulario_con_permiso_firma_la_clave_del_registro_cargado(): void
    {
        $this->permisosDeMenu('admin_sv');

        // El POST trae otra clave en s_pkey: se firma la del registro que se ha cargado (a_pkey).
        $json = $this->llamar('tablaDB_formulario_datos', [
            'clase_info' => self::CLASE, 'mod' => 'editar',
            'a_pkey' => json_encode('es_ES.UTF-8'), 's_pkey' => self::sPkey('ca_ES.UTF-8'),
        ]);

        $this->assertTrue($json['success'] ?? null, (string) ($json['mensaje'] ?? ''));
        $ctx = HashB::open((string) (self::datos($json)['ctx_update'] ?? ''), 'tablaDB_update');
        $this->assertSame('editar', $ctx['mod']);
        $this->assertSame(self::sPkey('es_ES.UTF-8'), $ctx['s_pkey']);
    }

    public function test_formulario_no_firma_un_modo_que_no_es_alta_ni_modificacion(): void
    {
        $this->permisosDeMenu('admin_sv');

        $json = $this->llamar('tablaDB_formulario_datos', [
            'clase_info' => self::CLASE, 'mod' => 'eliminar', 'a_pkey' => json_encode('es_ES.UTF-8'),
        ]);

        $this->assertFalse($json['success'] ?? null);
    }

    public function test_lista_sin_permiso_se_ve_sin_capsulas_de_borrado(): void
    {
        $this->permisosDeMenu('sm');

        $json = $this->llamar('tablaDB_lista_datos', ['clase_info' => self::CLASE]);

        $this->assertTrue($json['success'] ?? null, (string) ($json['mensaje'] ?? ''));
        $filas = array_filter((array) (self::datos($json)['a_valores'] ?? []), 'is_array');
        $this->assertNotSame([], $filas);
        foreach ($filas as $fila) {
            $this->assertArrayNotHasKey('ctx_eliminar', $fila);
        }
    }

    public function test_lista_con_permiso_lleva_capsulas_de_borrado(): void
    {
        $this->permisosDeMenu('admin_sv');

        $json = $this->llamar('tablaDB_lista_datos', ['clase_info' => self::CLASE]);

        $filas = array_values(array_filter((array) (self::datos($json)['a_valores'] ?? []), 'is_array'));
        $this->assertArrayHasKey('ctx_eliminar', $filas[0]);
    }

    /** Aunque traiga una cápsula válida (firmada antes, o por la pantalla de otro usuario), sin permiso no se ejecuta. */
    public function test_guardar_sin_permiso_no_ejecuta(): void
    {
        $this->permisosDeMenu('sm');
        $ctx = HashB::sign('tablaDB_update', [
            'clase_info' => self::CLASE, 'mod' => 'eliminar', 's_pkey' => self::sPkey('zz_NO_EXISTE.UTF-8'),
            'id_pau' => '', 'obj_pau' => '',
        ]);

        $json = $this->llamar('tablaDB_update', ['ctx_eliminar' => $ctx]);

        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }

    /**
     * El permiso se comprueba para la persona, casa o actividad de la cápsula: el registro se guarda para
     * esa misma, no para el `id_pau` que llegue en el formulario (LOG-04).
     */
    public function test_guardar_usa_el_id_pau_de_la_capsula_y_no_el_del_formulario(): void
    {
        $codigo = (string) file_get_contents(dirname(__DIR__, 5) . '/src/shared/infrastructure/ui/http/controllers/tablaDB_update.php');

        $this->assertStringNotContainsString('setCampos($_POST)', $codigo);
        $this->assertMatchesRegularExpression('/\$campos\[\'id_pau\'\]\s*=\s*\$Qid_pau;/', $codigo);
    }
}
