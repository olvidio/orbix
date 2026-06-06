<?php

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\CasaIngresoEliminar;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;

final class CasaIngresoEliminarTest extends TestCase
{
    public function test_sin_id_activ(): void
    {
        $useCase = new CasaIngresoEliminar(
            $this->createMock(IngresoRepositoryInterface::class),
        );

        $rta = $useCase->execute([]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_ingreso_no_encontrado(): void
    {
        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn(null);

        $rta = (new CasaIngresoEliminar($repo))->execute(['id_activ' => 5]);
        $this->assertFalse($rta['ok']);
        $this->assertNotSame('', $rta['mensaje']);
    }

    public function test_falla_eliminar(): void
    {
        $ing = $this->createMock(Ingreso::class);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($ing);
        $repo->method('Eliminar')->willReturn(false);

        $rta = (new CasaIngresoEliminar($repo))->execute(['id_activ' => 1]);
        $this->assertFalse($rta['ok']);
    }

    public function test_exito(): void
    {
        $ing = $this->createMock(Ingreso::class);

        $repo = $this->createMock(IngresoRepositoryInterface::class);
        $repo->method('findById')->willReturn($ing);
        $repo->method('Eliminar')->willReturn(true);

        $this->assertSame(
            ['ok' => true, 'mensaje' => '', 'data' => ''],
            (new CasaIngresoEliminar($repo))->execute(['id_activ' => 2])
        );
    }
}
