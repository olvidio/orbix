<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\ZonaCtrUpdate;

/**
 * Unitarios para {@see ZonaCtrUpdate::execute()}.
 *
 * Validamos:
 *  - El prefijo de `id_ubi` decide si se usa `CentroDlRepositoryInterface`
 *    (comienza por `1`) o `CentroEllasRepositoryInterface` (el resto).
 *  - `'no'` se normaliza a cadena vacia al guardar.
 *  - Si el repo devuelve `false` en `Guardar`, se acumula el error.
 *  - Los metodos que llamamos al centro (`findById`, `setId_zona`, `Guardar`)
 *    son los reales del contrato: si alguien los renombra, el test canta.
 */
final class ZonaCtrUpdateTest extends TestCase
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

    public function test_id_ubi_empezando_por_1_usa_CentroDl_y_guarda_zona(): void
    {
        $oCentro = $this->createMock(CentroDl::class);
        // `id_zona_new` llega como string desde el form; `setId_zona` espera
        // `?int`, por eso el servicio lo castea explicitamente.
        $oCentro->expects($this->once())->method('setId_zona')->with(7);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())
            ->method('findById')
            ->with(1001)
            ->willReturn($oCentro);
        $centroDlRepo->expects($this->once())
            ->method('Guardar')
            ->with($oCentro)
            ->willReturn(true);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class => $centroEllasRepo,
        ]);

        $out = ZonaCtrUpdate::execute('7', ['1001']);

        $this->assertSame(['tipo' => 'update', 'mensaje' => '', 'error' => ''], $out);
    }

    public function test_id_ubi_empezando_por_otro_digito_usa_CentroEllas(): void
    {
        $oCentro = $this->createMock(CentroEllas::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(3);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())
            ->method('findById')
            ->with(2005)
            ->willReturn($oCentro);
        $centroEllasRepo->expects($this->once())
            ->method('Guardar')
            ->willReturn(true);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class => $centroEllasRepo,
        ]);

        $out = ZonaCtrUpdate::execute('3', ['2005']);

        $this->assertSame('', $out['mensaje']);
    }

    public function test_id_zona_no_se_normaliza_a_null(): void
    {
        // Regresion: antes pasabamos '' (string vacio) a `setId_zona(?int)`
        // y PHP lanzaba TypeError. El contrato correcto es `null`.
        $oCentro = $this->createMock(CentroDl::class);
        $oCentro->expects($this->once())->method('setId_zona')->with(null);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class
                => $this->createStub(CentroEllasRepositoryInterface::class),
        ]);

        ZonaCtrUpdate::execute('no', ['1042']);
    }

    public function test_guardar_false_acumula_error_por_cada_centro_afectado(): void
    {
        $oCentro = $this->createStub(CentroDl::class);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('findById')->willReturn($oCentro);
        $centroDlRepo->method('Guardar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class
                => $this->createStub(CentroEllasRepositoryInterface::class),
        ]);

        $out = ZonaCtrUpdate::execute('9', ['1001', '1002']);

        // Dos centros -> dos lineas de error separadas por "\n".
        $this->assertSame("hay un error, no se ha guardado.\nhay un error, no se ha guardado.", $out['mensaje']);
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
