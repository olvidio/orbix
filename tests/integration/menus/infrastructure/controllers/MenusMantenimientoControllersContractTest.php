<?php

declare(strict_types=1);

namespace Tests\integration\menus\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\menus\application\MenuCopiar;
use src\menus\application\MenuEliminar;
use src\menus\application\MenuGuardar;
use src\menus\application\MenuMover;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores que mantienen menús y grupos de menús: solo actúan con la cápsula que firma su
 * pantalla, el menú o el grupo sobre el que actúan sale de la cápsula (no del POST) y los errores llegan como
 * `success = false`. En proceso, con el caso de uso o el repositorio sustituido por un doble.
 */
final class MenusMantenimientoControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/menus/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador . '.php';
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    /** Doble de un caso de uso invocable: anota los argumentos y devuelve el texto de error indicado. */
    private function doble(string $error = ''): object
    {
        return new class($error) {
            /** @var list<mixed>|null */
            public ?array $argumentos = null;

            public function __construct(private string $error)
            {
            }

            public function __invoke(mixed ...$argumentos): string
            {
                $this->argumentos = $argumentos;

                return $this->error;
            }
        };
    }

    /**
     * @return iterable<string, array{string, string, class-string, array<string, mixed>, list<mixed>}>
     */
    public static function invocablesProvider(): iterable
    {
        yield 'menu_guardar' => [
            'menu_guardar', 'ctx_guardar', MenuGuardar::class,
            ['id_menu' => '99', 'filtro_grupo' => '3', 'ok' => 'on', 'orden' => '10,20', 'txt_menu' => 'Menú', 'parametros' => 'a=1', 'id_metamenu' => '5', 'perm_menu' => ['1', '4']],
            [3, 12, 'on', '10,20', 'Menú', 'a=1', 5, ['1', '4']],
        ];
        yield 'menu_mover' => ['menu_mover', 'ctx_mover', MenuMover::class, ['id_menu' => '99', 'gm_new' => '4'], [12, '4']];
        yield 'menu_copiar' => ['menu_copiar', 'ctx_copiar', MenuCopiar::class, ['id_menu' => '99', 'gm_new' => '4'], [12, '4']];
        yield 'menu_eliminar' => ['menu_eliminar', 'ctx_eliminar', MenuEliminar::class, ['id_menu' => '99'], [12]];
    }

    /**
     * @param array<string, mixed> $post
     * @param list<mixed> $argumentos
     */
    #[DataProvider('invocablesProvider')]
    public function test_con_su_cápsula_el_menú_sale_de_la_cápsula(string $controlador, string $campo, string $caso, array $post, array $argumentos): void
    {
        $doble = $this->doble();
        $this->sustituirServicio($caso, $doble);

        $json = $this->ejecutar($controlador, [$campo => HashB::sign($controlador, ['id_menu' => 12])] + $post);

        $this->assertTrue($json['success'], (string) ($json['mensaje'] ?? ''));
        $this->assertSame($argumentos, $doble->argumentos);
    }

    /**
     * @param array<string, mixed> $post
     */
    #[DataProvider('invocablesProvider')]
    public function test_con_una_cápsula_falsa_o_de_otra_acción_no_llama_al_caso_de_uso(string $controlador, string $campo, string $caso, array $post): void
    {
        foreach (['falsa' => 'xxx', 'otra acción' => HashB::sign('otra_accion', ['id_menu' => 12]), 'ninguna' => null] as $tipo => $ctx) {
            $doble = $this->doble();
            $this->sustituirServicio($caso, $doble);

            $json = $this->ejecutar($controlador, $ctx === null ? $post : [$campo => $ctx] + $post);

            $this->assertFalse($json['success'], $tipo);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $tipo);
            $this->assertNull($doble->argumentos, $tipo);
        }
    }

    public function test_un_error_del_caso_de_uso_llega_como_success_false(): void
    {
        $this->sustituirServicio(MenuEliminar::class, $this->doble('hay un error, no se ha eliminado'));

        $json = $this->ejecutar('menu_eliminar', ['ctx_eliminar' => HashB::sign('menu_eliminar', ['id_menu' => 12])]);

        $this->assertFalse($json['success']);
        $this->assertSame('hay un error, no se ha eliminado', $json['mensaje']);
    }

    public function test_mover_o_copiar_sin_destino_avisa_y_no_llama_al_caso_de_uso(): void
    {
        foreach (['menu_mover' => ['ctx_mover', MenuMover::class], 'menu_copiar' => ['ctx_copiar', MenuCopiar::class]] as $controlador => [$campo, $caso]) {
            $doble = $this->doble();
            $this->sustituirServicio($caso, $doble);

            $json = $this->ejecutar($controlador, [$campo => HashB::sign($controlador, ['id_menu' => 12]), 'gm_new' => '']);

            $this->assertFalse($json['success'], $controlador);
            $this->assertSame('hay un error. Debe indicar el destino', $json['mensaje'], $controlador);
            $this->assertNull($doble->argumentos, $controlador);
        }
    }

    public function test_guardar_un_grupo_existente_toma_el_grupo_de_la_cápsula(): void
    {
        $grupo = new GrupMenu();
        $grupo->setId_grupmenu(7);
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with(7)->willReturn($grupo);
        $repo->expects($this->never())->method('getNewId');
        $repo->expects($this->once())->method('Guardar')->with($grupo)->willReturn(true);
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);

        $json = $this->ejecutar('grupmenu_guardar', [
            'ctx_guardar' => HashB::sign('grupmenu_guardar', ['id_grupmenu' => 7]),
            'id_grupmenu' => '8',
            'grupmenu' => 'Secretaría',
            'orden' => '4',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(['Secretaría', 4], [$grupo->getGrup_menu(), $grupo->getOrden()]);
    }

    public function test_guardar_un_grupo_nuevo_toma_un_id_nuevo(): void
    {
        $guardado = null;
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->never())->method('findById');
        $repo->method('getNewId')->willReturn(55);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (GrupMenu $g) use (&$guardado): bool {
            $guardado = $g;

            return true;
        });
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);

        $json = $this->ejecutar('grupmenu_guardar', ['ctx_guardar' => HashB::sign('grupmenu_guardar', []), 'grupmenu' => 'Nuevo', 'orden' => '1']);

        $this->assertTrue($json['success']);
        $this->assertSame([55, 'Nuevo', 1], [$guardado?->getId_grupmenu(), $guardado?->getGrup_menu(), $guardado?->getOrden()]);
    }

    public function test_guardar_un_grupo_sin_nombre_inexistente_o_con_error_avisa(): void
    {
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);
        $json = $this->ejecutar('grupmenu_guardar', ['ctx_guardar' => HashB::sign('grupmenu_guardar', ['id_grupmenu' => 7]), 'grupmenu' => '']);
        $this->assertSame([false, 'debe poner un nombre'], [$json['success'], $json['mensaje']]);
        $this->reponerServicios();

        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);
        $json = $this->ejecutar('grupmenu_guardar', ['ctx_guardar' => HashB::sign('grupmenu_guardar', ['id_grupmenu' => 7]), 'grupmenu' => 'X']);
        $this->assertSame([false, 'No encuentro el grupmenu'], [$json['success'], $json['mensaje']]);
        $this->reponerServicios();

        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(55);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('detalle');
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);
        $json = $this->ejecutar('grupmenu_guardar', ['ctx_guardar' => HashB::sign('grupmenu_guardar', []), 'grupmenu' => 'X']);
        $this->assertSame([false, "hay un error, no se ha guardado\ndetalle"], [$json['success'], $json['mensaje']]);
    }

    public function test_eliminar_un_grupo_toma_el_grupo_de_la_cápsula(): void
    {
        $grupo = new GrupMenu();
        $grupo->setId_grupmenu(7);
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with(7)->willReturn($grupo);
        $repo->expects($this->once())->method('Eliminar')->with($grupo)->willReturn(true);
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);

        $json = $this->ejecutar('grupmenu_eliminar', ['ctx_eliminar' => HashB::sign('grupmenu_eliminar', ['id_grupmenu' => 7]), 'id_grupmenu' => '8']);

        $this->assertTrue($json['success']);
    }

    public function test_eliminar_un_grupo_sin_grupo_en_la_cápsula_o_inexistente_avisa(): void
    {
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Eliminar');
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);

        foreach ([[], ['id_grupmenu' => 7]] as $payload) {
            $json = $this->ejecutar('grupmenu_eliminar', ['ctx_eliminar' => HashB::sign('grupmenu_eliminar', $payload)]);
            $this->assertSame([false, 'No encuentro el grupmenu'], [$json['success'], $json['mensaje']]);
        }
    }

    public function test_grupos_sin_su_cápsula_no_tocan_el_repositorio(): void
    {
        $repo = $this->createMock(GrupMenuRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');
        $repo->expects($this->never())->method('Eliminar');
        $this->sustituirServicio(GrupMenuRepositoryInterface::class, $repo);

        foreach (['grupmenu_guardar' => 'ctx_guardar', 'grupmenu_eliminar' => 'ctx_eliminar'] as $controlador => $campo) {
            foreach (['xxx', HashB::sign('otra_accion', ['id_grupmenu' => 7])] as $ctx) {
                $json = $this->ejecutar($controlador, [$campo => $ctx, 'grupmenu' => 'X', 'id_grupmenu' => '7']);
                $this->assertSame([false, 'Operación no autorizada'], [$json['success'], $json['mensaje']], $controlador);
            }
        }
    }
}
