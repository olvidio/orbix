<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\actividadescentro\domain\Info3010;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Las tablas de solo consulta (`DatosInfoRepo::isSoloLectura()`, p. ej. el dossier 3010 de centros
 * encargados) no se pueden dar de alta, modificar ni borrar con la pantalla genérica de tablas, aunque
 * la petición llegue con una cápsula válida: ni se pinta el formulario ni se ejecuta la acción.
 */
final class TablaDBSoloLecturaControllerTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/shared/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        // El repositorio no debe llegar a usarse: ni leer la ficha ni guardar ni borrar.
        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');
        $repo->expects($this->never())->method('Eliminar');
        $repo->expects($this->never())->method('findById');
        $this->sustituirServicio(CentroEncargadoRepositoryInterface::class, $repo);
        $this->sustituirServicio(Info3010::class, new Info3010($repo));
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

    /** @return array<string, array{string}> */
    public static function modos(): array
    {
        return ['alta' => ['nuevo'], 'modificación' => ['editar']];
    }

    #[DataProvider('modos')]
    public function test_no_hay_formulario_de_una_tabla_de_solo_consulta(string $mod): void
    {
        $json = $this->ejecutar('tablaDB_formulario_datos.php', [
            'clase_info' => urlencode(Info3010::class),
            'mod' => $mod,
            'id_pau' => '3001234',
            'obj_pau' => 'Actividad',
        ]);

        $this->assertFalse($json['success'] ?? null);
        $this->assertSame('Operación no autorizada', $json['mensaje'] ?? null);
    }

    #[DataProvider('modos')]
    public function test_no_se_guarda_en_una_tabla_de_solo_consulta_aunque_la_capsula_sea_valida(string $mod): void
    {
        $json = $this->ejecutar('tablaDB_update.php', [
            'ctx_update' => HashB::sign('tablaDB_update', ['mod' => $mod, 'clase_info' => urlencode(Info3010::class), 's_pkey' => '', 'id_pau' => '3001234', 'obj_pau' => 'Actividad']),
            'id_ubi' => '10018158',
        ]);

        $this->assertFalse($json['success'] ?? null);
        $this->assertSame('Operación no autorizada', $json['mensaje'] ?? null);
    }

    public function test_no_se_borra_de_una_tabla_de_solo_consulta_aunque_la_capsula_sea_valida(): void
    {
        $pk = \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode((string) json_encode(['id_activ' => 3001234, 'id_ubi' => 10018158]));
        $json = $this->ejecutar('tablaDB_update.php', [
            'ctx_eliminar' => HashB::sign('tablaDB_update', ['mod' => 'eliminar', 'clase_info' => urlencode(Info3010::class), 's_pkey' => $pk, 'id_pau' => '3001234', 'obj_pau' => 'Actividad']),
        ]);

        $this->assertFalse($json['success'] ?? null);
        $this->assertSame('Operación no autorizada', $json['mensaje'] ?? null);
    }
}
