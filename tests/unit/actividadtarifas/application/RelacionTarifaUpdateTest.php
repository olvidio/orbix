<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;

final class RelacionTarifaUpdateTest extends TestCase
{
    public function test_falta_tarifa(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_tarifa' => 0,
            'id_tipo_activ' => 123456,
        ]));
    }

    public function test_falta_tipo_actividad(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->expects($this->never())->method('Guardar');

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_tarifa' => 1,
            'id_tipo_activ' => 0,
        ]));
    }

    public function test_nuevo(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(99);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (RelacionTarifaTipoActividad $r) {
            return $r->getId_item() === 99
                && $r->getId_tarifa() === 5
                && $r->getId_tipo_activ() === 123456;
        });

        $this->assertSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => 'nuevo',
            'id_tarifa' => 5,
            'id_tipo_activ' => 123456,
        ]));
    }

    public function test_nuevo_con_tipo_que_ya_tiene_tarifa_avisa_sin_guardar(): void
    {
        $existente = new RelacionTarifaTipoActividad();
        $existente->setId_item(3);
        $existente->setId_tarifa(2);
        $existente->setId_tipo_activ(111001);

        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(99);
        $repo->method('matching')->willReturn([$existente]);
        $repo->expects($this->never())->method('Guardar');

        $this->assertSame(
            _("este tipo de actividad ya tiene una tarifa"),
            (new RelacionTarifaUpdate($repo))->execute([
                'id_item' => 'nuevo',
                'id_tarifa' => 5,
                'id_tipo_activ' => 111001,
            ])
        );
    }

    public function test_actualizar_la_misma_relacion_no_cuenta_como_duplicada(): void
    {
        $actual = new RelacionTarifaTipoActividad();
        $actual->setId_item(7);
        $actual->setId_tarifa(2);
        $actual->setId_tipo_activ(111001);

        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn($actual);
        $repo->method('matching')->willReturn([$actual]);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => '7',
            'id_tarifa' => 5,
            'id_tipo_activ' => 111001,
        ]));
    }

    public function test_error_de_bd_al_guardar_se_devuelve_como_mensaje(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(99);
        $repo->method('matching')->willReturn([]);
        $repo->method('Guardar')->willThrowException(new \RuntimeException('duplicate key value violates unique constraint'));

        $mensaje = (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => 'nuevo',
            'id_tarifa' => 5,
            'id_tipo_activ' => 111001,
        ]);

        $this->assertStringContainsString(_("hay un error, no se ha guardado"), $mensaje);
        $this->assertStringContainsString('duplicate key', $mensaje);
    }

    public function test_actualizar_no_encontrada(): void
    {
        $repo = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repo->method('findById')->with(7)->willReturn(null);

        $this->assertNotSame('', (new RelacionTarifaUpdate($repo))->execute([
            'id_item' => '7',
            'id_tarifa' => 5,
            'id_tipo_activ' => 123456,
        ]));
    }
}
