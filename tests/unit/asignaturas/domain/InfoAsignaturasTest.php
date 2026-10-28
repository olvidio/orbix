<?php

declare(strict_types=1);

namespace Tests\unit\asignaturas\domain;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\criteria\AsignaturaField;
use src\asignaturas\domain\InfoAsignaturas;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

final class InfoAsignaturasTest extends TestCase
{
    public function test_configuracion_por_defecto(): void
    {
        $repo = $this->createStub(AsignaturaRepositoryInterface::class);
        $info = new InfoAsignaturas($repo);

        $this->assertSame('src\\asignaturas\\domain\\entity\\Asignatura', $info->getClase());
        $this->assertSame('getAsignaturas', $info->getMetodoGestor());
        $this->assertSame(AsignaturaRepositoryInterface::class, $info->getRepositoryInterface());
    }

    public function test_getColeccion_filtra_asignaturas_no_opcionales(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(
                Criteria::create()
                    ->where(Filter::lt(AsignaturaField::IdAsignatura, 3000))
                    ->orderBy(AsignaturaField::IdNivel)
            ))
            ->willReturn([]);

        $info = new InfoAsignaturas($repo);

        $this->assertSame([], $info->getColeccion());
    }

    public function test_getColeccion_con_k_buscar_anade_filtro_nombre(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(
                Criteria::create()
                    ->where(Filter::matchesUnaccented(AsignaturaField::NombreAsignatura, 'calc'))
                    ->andWhere(Filter::lt(AsignaturaField::IdAsignatura, 3000))
                    ->orderBy(AsignaturaField::IdNivel)
            ))
            ->willReturn(['asig-1']);

        $info = new InfoAsignaturas($repo);
        $info->setK_buscar('calc');

        $this->assertSame(['asig-1'], $info->getColeccion());
    }
}
