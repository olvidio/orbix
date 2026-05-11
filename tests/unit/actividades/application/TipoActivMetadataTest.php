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
    private mixed $previousContainer = null;

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

    public function test_maps_coinciden_con_tipos_actividades(): void
    {
        $GLOBALS['container'] = $this->containerConTipos([]);

        $out = (new TipoActivMetadata())->execute();

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

        $GLOBALS['container'] = $this->containerConTipos([$a, $b]);

        $out = (new TipoActivMetadata())->execute();

        $this->assertSame([
            ['id_tipo_activ' => 100001, 'nombre' => 'Tipo A'],
            ['id_tipo_activ' => 200002, 'nombre' => 'Tipo B'],
        ], $out['filas']);
    }

    public function test_filas_vacias_si_repositorio_devuelve_no_array(): void
    {
        $repo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn(false);

        $GLOBALS['container'] = new class($repo) {
            public function __construct(private readonly TipoDeActividadRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== TipoDeActividadRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };

        $out = (new TipoActivMetadata())->execute();

        $this->assertSame([], $out['filas']);
    }

    /**
     * @param list<TipoDeActividad> $tipos
     */
    private function containerConTipos(array $tipos): object
    {
        $repo = $this->createStub(TipoDeActividadRepositoryInterface::class);
        $repo->method('getTiposDeActividades')->willReturn($tipos);

        return new class($repo) {
            public function __construct(private readonly TipoDeActividadRepositoryInterface $repo) {}

            public function get(string $key): object
            {
                if ($key !== TipoDeActividadRepositoryInterface::class) {
                    throw new \RuntimeException('Clave inesperada: ' . $key);
                }

                return $this->repo;
            }
        };
    }
}
