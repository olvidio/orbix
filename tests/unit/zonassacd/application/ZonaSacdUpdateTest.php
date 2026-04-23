<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\zonassacd\application\ZonaSacdUpdate;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

/**
 * Unitarios para {@see ZonaSacdUpdate::execute()}.
 *
 * Cubrimos los seis caminos del algoritmo:
 *  - `id_zona_new` vacio -> no-op.
 *  - `acumular === 2`:
 *      * `'no'`  -> elimina via `ZonaSacdRepositoryInterface::Eliminar`.
 *                   (Regresion directa del bug `$cZonaSacd[0]->DBEliminar()`.)
 *      * Nueva zona existente -> marca como no propia.
 *      * Nueva zona inexistente -> crea entry no-propia.
 *  - `acumular !== 2`:
 *      * `id_zona === 'no' || == 0` -> crea nuevo vinculo propio.
 *      * `id_zona_new === 'no'` con zona actual -> elimina.
 *      * Caso general -> actualiza zona y la marca propia.
 *
 * Tambien comprobamos que `setPropia` recibe boolean real y no la
 * string `'f'`/`'t'` (que PHP coercia silenciosamente a `true` y que
 * era un bug adicional detectado durante la auditoria).
 */
final class ZonaSacdUpdateTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_id_zona_new_vacio_es_noop(): void
    {
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->expects($this->never())->method($this->anything());

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdUpdate::execute('5', '', 1, [501]);

        $this->assertSame(['tipo' => 'update', 'mensaje' => '', 'error' => ''], $out);
    }

    public function test_acumular_eliminar_usa_Repository_Eliminar_no_DBEliminar(): void
    {
        // Regresion del bug `$cZonaSacd[0]->DBEliminar()`: la entidad no
        // expone ese metodo, el contrato correcto es `Eliminar` en el
        // repositorio.
        $existente = $this->createMock(ZonaSacd::class);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getZonasSacds')
            ->with(['id_nom' => 501, 'id_zona' => '5'])
            ->willReturn([$existente]);
        $repo->expects($this->once())
            ->method('Eliminar')
            ->with($existente)
            ->willReturn(true);
        $repo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdUpdate::execute('5', 'no', 2, [501]);

        $this->assertSame('', $out['mensaje']);
    }

    public function test_acumular_eliminar_con_Eliminar_false_acumula_error(): void
    {
        $existente = $this->createMock(ZonaSacd::class);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([$existente]);
        $repo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdUpdate::execute('5', 'no', 2, [501]);

        $this->assertSame('hay un error, no se ha eliminado', $out['mensaje']);
    }

    public function test_acumular_con_zona_existente_la_marca_como_no_propia(): void
    {
        // Regresion silenciosa: `setPropia('f')` se coercia a `true`. Aqui
        // exigimos que se pase `false` (bool real) en la rama acumular.
        $existente = $this->createMock(ZonaSacd::class);
        $existente->expects($this->once())->method('setPropia')->with(false);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getZonasSacds')
            ->with(['id_nom' => 501, 'id_zona' => 8])
            ->willReturn([$existente]);
        $repo->expects($this->once())->method('Guardar')->with($existente)->willReturn(true);
        $repo->expects($this->never())->method('getNewId');

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        ZonaSacdUpdate::execute('5', '8', 2, [501]);
    }

    public function test_acumular_sin_zona_existente_crea_entry_no_propia(): void
    {
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([]);
        $repo->expects($this->once())->method('getNewId')->willReturn(777);

        $capturada = null;
        $repo->expects($this->once())
            ->method('Guardar')
            ->willReturnCallback(
                static function (ZonaSacd $e) use (&$capturada): bool {
                    $capturada = $e;
                    return true;
                }
            );

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        ZonaSacdUpdate::execute('5', '8', 2, [501]);

        $this->assertNotNull($capturada);
        $this->assertSame(777, $capturada->getId_item());
        $this->assertSame(501, $capturada->getId_nom());
        $this->assertSame(8, $capturada->getId_zona());
        $this->assertFalse($capturada->isPropia());
    }

    public function test_reemplazo_desde_no_crea_nuevo_vinculo_propio(): void
    {
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->expects($this->once())->method('getNewId')->willReturn(99);

        $capturada = null;
        $repo->expects($this->once())
            ->method('Guardar')
            ->willReturnCallback(
                static function (ZonaSacd $e) use (&$capturada): bool {
                    $capturada = $e;
                    return true;
                }
            );

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        ZonaSacdUpdate::execute('no', '8', 1, [501]);

        $this->assertNotNull($capturada);
        $this->assertSame(8, $capturada->getId_zona());
        $this->assertTrue($capturada->isPropia());
    }

    public function test_reemplazo_a_no_elimina_la_asignacion_actual(): void
    {
        $existente = $this->createMock(ZonaSacd::class);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getZonasSacds')
            ->with(['id_nom' => 501, 'id_zona' => '5'])
            ->willReturn([$existente]);
        $repo->expects($this->once())->method('Eliminar')->with($existente)->willReturn(true);
        $repo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        ZonaSacdUpdate::execute('5', 'no', 1, [501]);
    }

    public function test_reemplazo_a_nueva_zona_actualiza_y_marca_propia(): void
    {
        $existente = $this->createMock(ZonaSacd::class);
        $existente->expects($this->once())->method('setId_zona')->with(8);
        $existente->expects($this->once())->method('setPropia')->with(true);

        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([$existente]);
        $repo->expects($this->once())->method('Guardar')->with($existente)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        ZonaSacdUpdate::execute('5', '8', 1, [501]);
    }

    public function test_reemplazo_a_nueva_zona_sin_asignacion_previa_es_noop(): void
    {
        // Sin asignacion existente (getZonasSacds => []), el servicio
        // no hace nada: ni crea ni guarda ni elimina.
        $repo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $repo->method('getZonasSacds')->willReturn([]);
        $repo->expects($this->never())->method('Guardar');
        $repo->expects($this->never())->method('Eliminar');

        $GLOBALS['container'] = $this->containerFromMap([
            ZonaSacdRepositoryInterface::class => $repo,
        ]);

        $out = ZonaSacdUpdate::execute('5', '8', 1, [501]);

        $this->assertSame('', $out['mensaje']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
