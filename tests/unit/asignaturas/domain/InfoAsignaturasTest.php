<?php

declare(strict_types=1);

namespace Tests\unit\asignaturas\domain;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\InfoAsignaturas;

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
            ->method('getAsignaturas')
            ->with(
                ['id_asignatura' => 3000, '_ordre' => 'id_nivel'],
                ['id_asignatura' => '<'],
            )
            ->willReturn([]);

        $info = new InfoAsignaturas($repo);

        $this->assertSame([], $info->getColeccion());
    }

    public function test_getColeccion_con_k_buscar_anade_filtro_nombre(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getAsignaturas')
            ->with(
                [
                    'nombre_asignatura' => 'calc',
                    'id_asignatura' => 3000,
                    '_ordre' => 'id_nivel',
                ],
                [
                    'nombre_asignatura' => 'sin_acentos',
                    'id_asignatura' => '<',
                ],
            )
            ->willReturn(['asig-1']);

        $info = new InfoAsignaturas($repo);
        $info->setK_buscar('calc');

        $this->assertSame(['asig-1'], $info->getColeccion());
    }
}
