<?php

declare(strict_types=1);

namespace Tests\integration\menus\infrastructure\controllers;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DetectaEscriturasEnBd;

/**
 * `menus_importar` sustituye los menús de la aplicación (`aux_grupmenu`, `aux_grupmenu_rol`, `aux_menus` de sv-e) por
 * los de una plantilla (`ref_*`). Se ejecuta dentro de una transacción que se deshace (`DetectaEscriturasEnBd`).
 */
final class MenusImportarControllerTest extends myTest
{
    use DetectaEscriturasEnBd;

    private const CONTROLADOR = __DIR__ . '/../../../../../src/menus/infrastructure/ui/http/controllers/menus_importar.php';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /** @return array{grupos: int, roles: int, menus: int} */
    private function contarMenus(): array
    {
        $oDB = GlobalPdo::get('oDBE');
        $n = static fn(string $tabla): int => (int) $oDB->query("SELECT count(*) FROM $tabla")->fetchColumn();

        return ['grupos' => $n('aux_grupmenu'), 'roles' => $n('aux_grupmenu_rol'), 'menus' => $n('aux_menus')];
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADOR;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'menus_importar debe emitir solo JSON; emitió: ' . substr($salida, 0, 200));

        return $json;
    }

    /**
     * Hallazgo: con un id de plantilla que no existe (o sin id: se lee como 0) vacía los menús de la aplicación y no
     * pone ninguno, y responde que todo ha ido bien. Debería comprobar antes que la plantilla tiene menús y, si no,
     * avisar sin tocar nada. (Duda 27 del plan.)
     */
    public function test_con_una_plantilla_que_no_existe_vacia_los_menus_de_la_aplicacion(): void
    {
        $idInexistente = 999999;
        $oDBPC = GlobalPdo::get('oDBPC');
        $this->assertSame(0, (int) $oDBPC->query("SELECT count(*) FROM ref_menus WHERE id_template_menu = $idInexistente")->fetchColumn());
        $antes = $this->contarMenus();
        $this->assertGreaterThan(0, $antes['menus'], 'la BD de pruebas tiene menús');

        $dentro = null;
        $json = null;
        $this->escriturasDe(function () use ($idInexistente, &$dentro, &$json): void {
            $json = $this->ejecutar(['ctx_importar' => HashB::sign('menus_importar'), 'id_template_menu' => (string) $idInexistente]);
            $dentro = $this->contarMenus();
        });

        $this->assertTrue($json['success'], 'responde que ha ido bien');
        $this->assertSame(['grupos' => 0, 'roles' => 0, 'menus' => 0], $dentro);
        $this->assertSame($antes, $this->contarMenus(), 'la transacción del test lo ha deshecho');
    }

    public function test_sin_su_capsula_no_toca_los_menus(): void
    {
        $antes = $this->contarMenus();

        $r = $this->escriturasDe(function (): void {
            foreach (['xxx', HashB::sign('menus_exportar')] as $ctx) {
                $json = $this->ejecutar(['ctx_importar' => $ctx, 'id_template_menu' => '1']);
                $this->assertSame([false, 'Operación no autorizada'], [$json['success'], $json['mensaje']]);
            }
        });

        $this->assertSame([], $r['escrituras']);
        $this->assertSame($antes, $this->contarMenus());
    }
}
