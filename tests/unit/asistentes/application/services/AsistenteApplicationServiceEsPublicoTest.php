<?php

namespace Tests\unit\asistentes\application\services;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\contracts\UnitOfWorkInterface;
use Psr\Container\ContainerInterface;

final class AsistenteApplicationServiceEsPublicoTest extends TestCase
{
    public function test_guardar_marca_es_publico_cuando_persona_y_actividad_son_de_dl_distintas(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100);
        $asistente->setId_nom(200);

        $repo = $this->createMock(AsistenteRepositoryInterface::class);
        $repo->method('findById')->with(100, 200)->willReturn($asistente);
        $repo->expects($this->once())->method('Guardar')->with($asistente)->willReturn(true);

        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getDl')->willReturn('dlv');
        $persona->method('getId_schema')->willReturn(3);

        $personaFinder = $this->createMock(PersonaFinderService::class);
        $personaFinder->method('findPersonaEnGlobal')->with(200)->willReturn($persona);

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->once())->method('marcarEsPublico')->with(200, 3)->willReturn(true);

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('dlp');

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with(100)->willReturn($actividad);

        $uow = $this->createMock(UnitOfWorkInterface::class);
        $uow->expects($this->once())->method('execute')->willReturnCallback(
            static function (callable $callback) {
                return $callback(new class {
                    public function registerEntity(object $entity): void
                    {
                    }
                });
            }
        );

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($repo) {
            if ($id === AsistenteDlRepositoryInterface::class) {
                return $repo;
            }

            return $this->createMock($id);
        });

        $service = new AsistenteApplicationService(
            $repo,
            $uow,
            $container,
            $actividadRepo,
            $personaAll,
            $personaFinder,
        );

        $this->assertTrue($service->guardar($asistente));
    }

    public function test_guardar_no_marca_es_publico_cuando_actividad_es_de_la_misma_dl(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100);
        $asistente->setId_nom(200);

        $repo = $this->createMock(AsistenteRepositoryInterface::class);
        $repo->method('findById')->with(100, 200)->willReturn($asistente);
        $repo->expects($this->once())->method('Guardar')->with($asistente)->willReturn(true);

        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getDl')->willReturn('dlv');
        $persona->method('getId_schema')->willReturn(3);

        $personaFinder = $this->createMock(PersonaFinderService::class);
        $personaFinder->method('findPersonaEnGlobal')->with(200)->willReturn($persona);

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->never())->method('marcarEsPublico');

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn('dlv');

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with(100)->willReturn($actividad);

        $uow = $this->createMock(UnitOfWorkInterface::class);
        $uow->expects($this->once())->method('execute')->willReturnCallback(
            static function (callable $callback) {
                return $callback(new class {
                    public function registerEntity(object $entity): void
                    {
                    }
                });
            }
        );

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($repo) {
            if ($id === AsistenteDlRepositoryInterface::class) {
                return $repo;
            }

            return $this->createMock($id);
        });

        $service = new AsistenteApplicationService(
            $repo,
            $uow,
            $container,
            $actividadRepo,
            $personaAll,
            $personaFinder,
        );

        $this->assertTrue($service->guardar($asistente));
    }
}
