<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaUpdate;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

final class TipoTarifaUpdateTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_nuevo(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(77);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (TipoTarifa $t) {
            return $t->getId_tarifa() === 77;
        });

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $msg = TipoTarifaUpdate::execute([
            'id_tarifa' => 'nuevo',
            'letra' => 'A',
            'modo' => '1',
            'observ' => 'x',
        ]);
        $this->assertSame('', $msg);
    }

    public function test_actualizar_no_encontrada(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);
        $repo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertNotSame('', TipoTarifaUpdate::execute(['id_tarifa' => '5']));
    }

    public function test_actualizar_exito(): void
    {
        $o = new TipoTarifa();
        $o->setId_tarifa(5);
        $o->setModo(1);
        $o->setLetra('B');

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn($o);
        $repo->expects($this->once())->method('Guardar')->with($o)->willReturn(true);

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $this->assertSame('', TipoTarifaUpdate::execute([
            'id_tarifa' => '5',
            'observ' => 'nota',
        ]));
    }

    public function test_guardar_falla(): void
    {
        $o = new TipoTarifa();
        $o->setId_tarifa(5);
        $o->setModo(0);

        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn($o);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('err');

        $GLOBALS['container'] = $this->containerOne(TipoTarifaRepositoryInterface::class, $repo);

        $msg = TipoTarifaUpdate::execute(['id_tarifa' => '5', 'modo' => '0']);
        $this->assertStringContainsString('err', $msg);
    }

    /**
     * @param class-string $iface
     */
    private function containerOne(string $iface, object $service): object
    {
        return new class($iface, $service) {
            public function __construct(
                private readonly string $iface,
                private readonly object $service
            ) {}

            public function get(string $id): object
            {
                if ($id !== $this->iface) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->service;
            }
        };
    }
}
