<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use function DI\factory;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class MatriculasListaOtrasRDataTest extends TestCase
{
    private mixed $containerPrevio = null;

    protected function setUp(): void
    {
        $this->containerPrevio = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->containerPrevio === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->containerPrevio;
        }
    }

    public function test_omite_quien_esta_en_esquema_y_lista_quien_no_tiene_ficha(): void
    {
        $enEsquema = $this->nota(10, 1111);
        $otraDelMismo = $this->nota(10, 2222);
        $fuera = $this->nota(20, 2222);

        $repo = $this->createMock(PersonaNotaOtraRegionStgrRepositoryInterface::class);
        $repo->method('getPersonaNotas')->willReturn([$enEsquema, $otraDelMismo, $fuera]);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            PersonaNotaOtraRegionStgrRepositoryInterface::class => factory(static fn () => $repo),
        ]);
        $GLOBALS['container'] = $builder->build();

        $asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $asignaturas->method('getArrayAsignaturas')->willReturn([
            1111 => 'Latín',
            2222 => 'Griego',
        ]);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('idNomsEnEsquemasAquinate')
            ->with($this->callback(static function (array $ids): bool {
                $copia = array_map(intval(...), $ids);
                sort($copia);

                return $copia === [10, 20];
            }))
            ->willReturn([10 => true]);

        $useCase = new MatriculasListaOtrasRData(
            $this->createMock(PersonaPubRepositoryInterface::class),
            $asignaturas,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ActaRepositoryInterface::class),
            $this->createMock(DelegacionRepositoryInterface::class),
            $finder,
        );

        $out = $useCase->execute([
            'apellido1' => '',
            'esquema_region_stgr' => 'H-Hv',
        ]);

        $this->assertSame('', $out['msg_err']);
        $this->assertSame(_('Alumnos sin ficha en los esquemas Aquinate cargados'), $out['titulo']);
        $this->assertCount(1, $out['a_valores']);
        $fila = array_values($out['a_valores'])[0];
        $this->assertSame(20, $fila[5]);
        $this->assertSame(sprintf(_('sin ficha (%d)'), 20), $fila[1]);
        $this->assertSame('Griego', $fila[4]);
        $this->assertSame('', $fila[2]);
    }

    private function nota(int $idNom, int $idAsignatura): PersonaNotaOtraRegionStgr
    {
        $nota = $this->createMock(PersonaNotaOtraRegionStgr::class);
        $nota->method('getId_nom')->willReturn($idNom);
        $nota->method('getId_asignatura')->willReturn($idAsignatura);
        $nota->method('getId_activ')->willReturn(null);
        $nota->method('getActa')->willReturn(null);

        return $nota;
    }
}
