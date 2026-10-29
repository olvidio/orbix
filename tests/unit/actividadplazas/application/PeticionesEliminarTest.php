<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PeticionesEliminar;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\criteria\PlazaPeticionField as F;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

final class PeticionesEliminarTest extends TestCase
{
    public function test_faltan_parametros(): void
    {
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->expects($this->never())->method('matching');

        $msg = (new PeticionesEliminar($repo))->execute(['id_nom' => 0, 'sactividad' => 'ca']);
        $this->assertNotSame('', $msg);
    }

    public function test_elimina_todas_y_exito(): void
    {
        $p1 = $this->createMock(PlazaPeticion::class);
        $p2 = $this->createMock(PlazaPeticion::class);

        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('matching')->with($this->equalTo(
            Criteria::create()
                ->where(Filter::eq(F::IdNom, 9))
                ->andWhere(Filter::eq(F::Tipo, 'ca'))
        ))->willReturn([$p1, $p2]);
        $repo->expects($this->exactly(2))->method('Eliminar')
            ->willReturnOnConsecutiveCalls(true, true);

        $this->assertSame('', (new PeticionesEliminar($repo))->execute(['id_nom' => 9, 'sactividad' => 'ca']));
    }

    public function test_error_si_falla_eliminar(): void
    {
        $p1 = $this->createMock(PlazaPeticion::class);
        $repo = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $repo->method('matching')->willReturn([$p1]);
        $repo->method('Eliminar')->willReturn(false);

        $msg = (new PeticionesEliminar($repo))->execute(['id_nom' => 1, 'sactividad' => 'crt']);
        $this->assertNotSame('', $msg);
    }
}
