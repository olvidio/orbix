<?php

declare(strict_types=1);

namespace Tests\integration\zonassacd\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\zonassacd\application\ZonaCtrUpdate;
use src\zonassacd\application\ZonaSacdUpdate;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los dos controladores que escriben en `zonassacd` (`zona_sacd_update`, `zona_ctr_update`): solo
 * actúan con las cápsulas que firma su lista (`ZonaSacdLista`, `ZonaCtrLista`); los ids (y la zona de origen del
 * sacd) salen de la cápsula, no del POST, y un error del caso de uso llega como `success = false`.
 * Se ejecutan en proceso, como los llama `PostRequest`, con el caso de uso sustituido por un doble.
 */
final class ZonassacdControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/zonassacd/infrastructure/ui/http/controllers/';

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
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    /** Doble del caso de uso: guarda los argumentos de `execute` y devuelve el mensaje indicado. */
    private function doble(string $mensaje = ''): object
    {
        return new class($mensaje) {
            /** @var list<mixed>|null */
            public ?array $argumentos = null;

            public function __construct(private string $mensaje)
            {
            }

            /** @return array<string, mixed> */
            public function execute(mixed ...$argumentos): array
            {
                $this->argumentos = $argumentos;

                return ['tipo' => 'update', 'mensaje' => $this->mensaje, 'error' => ''];
            }
        };
    }

    public function test_sacd_con_cápsula_válida_toma_el_sacd_y_la_zona_de_origen_de_la_cápsula(): void
    {
        $doble = $this->doble();
        $this->sustituirServicio(ZonaSacdUpdate::class, $doble);
        $ctx = HashB::sign('zona_sacd_update', ['id_nom' => 100123, 'id_zona' => '7']);

        $json = $this->ejecutar('zona_sacd_update.php', [
            'sel' => [$ctx],
            'id_zona' => 'no',
            'id_zona_new' => '9',
            'acumular' => '2',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(['7', '9', 2, [100123]], $doble->argumentos);
    }

    public function test_sacd_con_cápsula_falsa_o_de_otra_acción_no_llama_al_caso_de_uso(): void
    {
        foreach (['falsa' => 'xxx', 'otra acción' => HashB::sign('zona_ctr_update', ['id_ubi' => '5'])] as $caso => $ctx) {
            $doble = $this->doble();
            $this->sustituirServicio(ZonaSacdUpdate::class, $doble);

            $json = $this->ejecutar('zona_sacd_update.php', ['sel' => [$ctx], 'id_zona' => '7', 'id_zona_new' => '9']);

            $this->assertFalse($json['success'], $caso);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $caso);
            $this->assertNull($doble->argumentos, $caso);
        }
    }

    public function test_sacd_un_error_del_caso_de_uso_llega_como_success_false(): void
    {
        $this->sustituirServicio(ZonaSacdUpdate::class, $this->doble('hay un error, no se ha guardado'));
        $ctx = HashB::sign('zona_sacd_update', ['id_nom' => 100123, 'id_zona' => '7']);

        $json = $this->ejecutar('zona_sacd_update.php', ['sel' => [$ctx], 'id_zona_new' => '9']);

        $this->assertFalse($json['success']);
        $this->assertSame('hay un error, no se ha guardado', $json['mensaje']);
    }

    public function test_ctr_con_cápsulas_válidas_pasa_los_centros_de_las_cápsulas(): void
    {
        $doble = $this->doble();
        $this->sustituirServicio(ZonaCtrUpdate::class, $doble);

        $json = $this->ejecutar('zona_ctr_update.php', [
            'sel' => [
                HashB::sign('zona_ctr_update', ['id_ubi' => '1001']),
                HashB::sign('zona_ctr_update', ['id_ubi' => '1002']),
            ],
            'id_zona_new' => 'no',
        ]);

        $this->assertTrue($json['success']);
        $this->assertSame(['no', ['1001', '1002']], $doble->argumentos);
    }

    public function test_ctr_con_cápsula_falsa_de_otra_acción_o_sin_centro_no_llama_al_caso_de_uso(): void
    {
        $casos = [
            'falsa' => 'xxx',
            'otra acción' => HashB::sign('zona_sacd_update', ['id_nom' => 1, 'id_zona' => '7']),
            'sin centro' => HashB::sign('zona_ctr_update', ['id_ubi' => '']),
        ];
        foreach ($casos as $caso => $ctx) {
            $doble = $this->doble();
            $this->sustituirServicio(ZonaCtrUpdate::class, $doble);

            $json = $this->ejecutar('zona_ctr_update.php', ['sel' => [$ctx], 'id_zona_new' => '9']);

            $this->assertFalse($json['success'], $caso);
            $this->assertSame('Operación no autorizada', $json['mensaje'], $caso);
            $this->assertNull($doble->argumentos, $caso);
        }
    }

    public function test_ctr_un_error_del_caso_de_uso_llega_como_success_false(): void
    {
        $this->sustituirServicio(ZonaCtrUpdate::class, $this->doble('hay un error, no se ha guardado.'));

        $json = $this->ejecutar('zona_ctr_update.php', [
            'sel' => [HashB::sign('zona_ctr_update', ['id_ubi' => '1001'])],
            'id_zona_new' => '9',
        ]);

        $this->assertFalse($json['success']);
        $this->assertSame('hay un error, no se ha guardado.', $json['mensaje']);
    }
}
