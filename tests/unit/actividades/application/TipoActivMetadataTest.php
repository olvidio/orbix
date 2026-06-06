<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\TipoActivMetadata;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\entity\TiposActividades;

final class TipoActivMetadataTest extends TestCase
{
    public function test_maps_coinciden_con_tipos_actividades(): void
    {
        $repo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn([]);

        $out = (new TipoActivMetadata($repo))->execute();

        $this->assertSame(['maps', 'filas'], array_keys($out));
        $this->assertSame(
            [
                'sfsv' => TiposActividades::A_SFSV,
                'asistentes' => TiposActividades::A_ASISTENTES,
                'actividad1digito' => TiposActividades::A_ACTIVIDAD_1_DIGITO,
                'actividad2digitos' => TiposActividades::A_ACTIVIDAD_2_DIGITOS,
            ],
            $out['maps']
        );
    }

    public function test_filas_desde_repositorio(): void
    {
        $a = new TipoDeActividad();
        $a->setId_tipo_activ(100001);
        $a->setNombre('Tipo A');
        $b = new TipoDeActividad();
        $b->setId_tipo_activ(200002);
        $b->setNombre('Tipo B');

        $repo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn([$a, $b]);

        $out = (new TipoActivMetadata($repo))->execute();

        $this->assertSame([
            ['id_tipo_activ' => 100001, 'nombre' => 'Tipo A'],
            ['id_tipo_activ' => 200002, 'nombre' => 'Tipo B'],
        ], $out['filas']);
    }

    public function test_filas_vacias_si_repositorio_devuelve_no_array(): void
    {
        $repo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn([]);

        $out = (new TipoActivMetadata($repo))->execute();

        $this->assertSame([], $out['filas']);
    }
}
