<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\TipoTarifaUpdate;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

final class TipoTarifaUpdateTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
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

        $msg = (new TipoTarifaUpdate($repo))->execute([
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

        $this->assertNotSame('', (new TipoTarifaUpdate($repo))->execute(['id_tarifa' => '5']));
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

        $this->assertSame('', (new TipoTarifaUpdate($repo))->execute([
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

        $msg = (new TipoTarifaUpdate($repo))->execute(['id_tarifa' => '5', 'modo' => '0']);
        $this->assertStringContainsString('err', $msg);
    }
}
