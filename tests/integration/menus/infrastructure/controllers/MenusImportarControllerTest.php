<?php

declare(strict_types=1);

namespace Tests\integration\menus\infrastructure\controllers;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DetectaEscriturasEnBd;

/**
 * `menus_importar` sustituye los menús de la aplicación (`aux_grupmenu`, `aux_grupmenu_rol`, `aux_menus` de sv-e) por
 * los de una plantilla (`ref_*` de comun). Se ejecuta dentro de una transacción que se deshace (`DetectaEscriturasEnBd`).
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

    /** @return array{grupos: int, roles: int, menus: int} */
    private function contarPlantilla(int $idTemplate): array
    {
        $oDBPC = GlobalPdo::get('oDBPC');
        $n = static fn(string $tabla): int => (int) $oDBPC->query("SELECT count(*) FROM $tabla WHERE id_template_menu = $idTemplate")->fetchColumn();

        return ['grupos' => $n('ref_grupmenu'), 'roles' => $n('ref_grupmenu_rol'), 'menus' => $n('ref_menus')];
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

    /** Una plantilla sin menús (que no existe o no se ha exportado) no vacía los menús de la aplicación (duda 27). */
    public function test_con_una_plantilla_sin_menus_avisa_y_no_toca_nada(): void
    {
        $idInexistente = 999999;
        $this->assertSame(0, $this->contarPlantilla($idInexistente)['menus']);
        $antes = $this->contarMenus();
        $this->assertGreaterThan(0, $antes['menus'], 'la BD de pruebas tiene menús');

        $dentro = null;
        $json = null;
        $r = $this->escriturasDe(function () use ($idInexistente, &$dentro, &$json): void {
            $json = $this->ejecutar(['ctx_importar' => HashB::sign('menus_importar'), 'id_template_menu' => (string) $idInexistente]);
            $dentro = $this->contarMenus();
        });

        $this->assertFalse($json['success']);
        $this->assertSame('La plantilla de menús no tiene menús: no se ha cambiado nada.', $json['mensaje']);
        $this->assertSame($antes, $dentro);
        $this->assertSame([], $r['escrituras']);
    }

    public function test_con_una_plantilla_con_menus_los_sustituye_por_los_de_la_plantilla(): void
    {
        $idTemplate = (int) GlobalPdo::get('oDBPC')
            ->query('SELECT id_template_menu FROM ref_menus GROUP BY 1 ORDER BY count(*) DESC LIMIT 1')
            ->fetchColumn();
        if ($idTemplate === 0) {
            $this->markTestSkipped('La BD de pruebas no tiene plantillas de menús.');
        }
        $plantilla = $this->contarPlantilla($idTemplate);
        $antes = $this->contarMenus();

        $dentro = null;
        $json = null;
        $r = $this->escriturasDe(function () use ($idTemplate, &$dentro, &$json): void {
            $json = $this->ejecutar(['ctx_importar' => HashB::sign('menus_importar'), 'id_template_menu' => (string) $idTemplate]);
            $dentro = $this->contarMenus();
        });

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
        $this->assertSame($plantilla, $dentro);
        $this->assertSame([], $r['abortadas']);
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
