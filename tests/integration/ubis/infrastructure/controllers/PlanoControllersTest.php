<?php

declare(strict_types=1);

namespace Tests\integration\ubis\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\ubis\application\PlanosDireccion;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los endpoints del plano de una dirección: qué llega a {@see PlanosDireccion} y que
 * subir/borrar solo atienden a la dirección que fija la cápsula firmada por la ficha.
 * Fuera: `plano_download.php` (cabeceras + `exit`); lo cubre `e2e/specs/authenticated/ubis-plano.spec.ts`.
 */
final class PlanoControllersTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/ubis/infrastructure/ui/http/controllers/';

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

    /** @return object{llamadas: list<array{string, array<int, mixed>}>} */
    private function planos(bool $existe = false): object
    {
        $doble = new class($existe) {
            /** @var list<array{string, array<int, mixed>}> */
            public array $llamadas = [];

            public function __construct(private bool $existe)
            {
            }

            /** @param array<int, mixed> $args */
            public function __call(string $metodo, array $args): mixed
            {
                $this->llamadas[] = [$metodo, $args];

                return $metodo === 'existe' ? $this->existe : null;
            }
        };
        $this->sustituirServicio(PlanosDireccion::class, $doble);

        return $doble;
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
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    public function test_existe_sin_parametros_es_un_error_sin_consultar(): void
    {
        $planos = $this->planos();

        $json = $this->ejecutar('plano_existe.php', []);

        $this->assertFalse($json['success']);
        $this->assertSame([], $planos->llamadas);
    }

    public function test_existe_pregunta_por_la_direccion_pedida(): void
    {
        $planos = $this->planos(true);

        $json = $this->ejecutar('plano_existe.php', ['obj_dir' => 'DireccionCdcDl', 'id_direccion' => '12']);

        $this->assertTrue($json['success']);
        $this->assertSame(['existe' => true], json_decode((string) $json['data'], true));
        $this->assertSame([['existe', ['DireccionCdcDl', 12]]], $planos->llamadas);
    }

    public function test_borrar_quita_el_plano_de_la_direccion_de_la_capsula_no_la_del_post(): void
    {
        $planos = $this->planos();
        $ctx = HashB::sign('plano_borrar', ['obj_dir' => 'DireccionCentroDl', 'id_direccion' => 34]);

        $json = $this->ejecutar('plano_borrar.php', ['ctx_plano_borrar' => $ctx, 'obj_dir' => 'DireccionCdc', 'id_direccion' => '99']);

        $this->assertTrue($json['success']);
        $this->assertSame([['borrar', ['DireccionCentroDl', 34]]], $planos->llamadas);
    }

    public function test_borrar_y_subir_sin_su_capsula_no_hacen_nada(): void
    {
        $planos = $this->planos();
        $otra = HashB::sign('plano_subir', ['obj_dir' => 'DireccionCentroDl', 'id_direccion' => 34]);

        $borrar = $this->ejecutar('plano_borrar.php', ['ctx_plano_borrar' => $otra]);
        $subir = $this->ejecutar('plano_subir.php', ['ctx_plano_subir' => '']);

        $this->assertFalse($borrar['success']);
        $this->assertFalse($subir['success']);
        $this->assertSame([], $planos->llamadas);
    }
}
