<?php

declare(strict_types=1);

namespace Tests\integration\menus\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Exportar los menús (a una plantilla de la BD o a ficheros SQL) solo con la cápsula de su pantalla.
 *
 * `menus_exportar` sobrescribe una plantilla de menús; `menus_exportar_ref_a_ficheros` vuelca las
 * tablas de referencia a ficheros o, con `accion=importar`, las vacía y las recarga con `psql`.
 * Antes se ejecutaban con cualquier petición. Fase 6.5 del plan de revisión de tests.
 *
 * Aquí solo se comprueba el rechazo (no se ejecuta ninguna exportación).
 */
final class MenusExportarControllersTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/menus/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $plantillas = $this->createMock(TemplateMenuRepositoryInterface::class);
        $plantillas->expects($this->never())->method('Guardar');
        $plantillas->expects($this->never())->method('findByName');
        $this->sustituirServicio(TemplateMenuRepositoryInterface::class, $plantillas);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /** @return iterable<string, array{string, string, array<string, string>}> controlador, campo de la cápsula y resto del POST */
    public static function controladoresProvider(): iterable
    {
        yield 'a una plantilla' => ['menus_exportar.php', 'ctx_exportar', ['nombre' => 'zz_test_no_existe', 'sobreescribir' => 'false']];
        // Sin `accion` el controlador no hace nada: no se arriesga un volcado ni una recarga.
        yield 'a ficheros' => ['menus_exportar_ref_a_ficheros.php', 'ctx_ficheros', []];
    }

    /** @param array<string, string> $resto */
    #[DataProvider('controladoresProvider')]
    public function test_sin_la_capsula_de_su_pantalla_responde_no_autorizada(string $controlador, string $campo, array $resto): void
    {
        foreach (['sin cápsula' => [], 'falsa' => [$campo => 'x'], 'de otra acción' => [$campo => HashB::sign('menus_importar')]] as $txt => $post) {
            $_POST = $post + $resto;
            InProcessSrcDispatch::begin();
            ob_start();
            try {
                require self::DIR . $controlador;
            } finally {
                $salida = (string) ob_get_clean();
                InProcessSrcDispatch::end();
            }
            $json = json_decode($salida, true);
            $this->assertIsArray($json, "$controlador ($txt) debe responder JSON; respondió: " . substr($salida, 0, 200));
            $this->assertFalse($json['success'] ?? null, $txt);
            $this->assertSame(_("Operación no autorizada"), $json['mensaje'] ?? null, $txt);
        }
    }
}
