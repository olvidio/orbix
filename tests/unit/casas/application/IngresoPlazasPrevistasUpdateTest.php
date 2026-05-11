<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;

final class IngresoPlazasPrevistasUpdateTest extends TestCase
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

    public function test_json_sin_id_activ(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $this->createMock(IngresoRepositoryInterface::class),
        ]);

        $this->assertNotSame('', IngresoPlazasPrevistasUpdate::execute([
            'data' => '{}',
            'colName' => '""',
        ]));
    }

    public function test_ingreso_no_encontrado(): void
    {
        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(10)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $data = json_encode((object)['id' => 10, 'plazas' => 4]);
        $colName = json_encode('plazas');

        $this->assertNotSame('', IngresoPlazasPrevistasUpdate::execute([
            'data' => $data,
            'colName' => $colName,
        ]));
    }

    public function test_falla_guardar(): void
    {
        $oIngreso = $this->createMock(Ingreso::class);
        $oIngreso->expects($this->once())->method('setNumAsistentesPrevistosVo')->with(7);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($oIngreso);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('x');

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $data = json_encode((object)['id' => 3, 'c' => 7]);
        $colName = json_encode('c');

        $msg = IngresoPlazasPrevistasUpdate::execute(['data' => $data, 'colName' => $colName]);
        $this->assertNotSame('', $msg);
        $this->assertStringContainsString('x', $msg);
    }

    public function test_exito(): void
    {
        $oIngreso = $this->createMock(Ingreso::class);
        $oIngreso->expects($this->once())->method('setNumAsistentesPrevistosVo')->with(12);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(8)->willReturn($oIngreso);
        $repo->method('Guardar')->with($oIngreso)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            IngresoRepositoryInterface::class => $repo,
        ]);

        $data = json_encode((object)['id' => 8, 'plazas' => 12]);
        $colName = json_encode('plazas');

        $this->assertSame('', IngresoPlazasPrevistasUpdate::execute([
            'data' => $data,
            'colName' => $colName,
        ]));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
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
