<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application\services;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\UnitOfWorkInterface;

/**
 * Caso A: guardar asistencia ya no marca publicación; los listados resuelven por global.personas.
 */
final class AsistenteApplicationServiceEsPublicoTest extends TestCase
{
    public function test_guardar_no_marca_publicacion_cross_dl(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100);
        $asistente->setId_nom(200);

        $repo = $this->createMock(AsistenteRepositoryInterface::class);
        $repo->method('findById')->with(100, 200)->willReturn($asistente);
        $repo->expects($this->once())->method('Guardar')->with($asistente)->willReturn(true);

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

        $service = new AsistenteApplicationService($repo, $uow, $container);

        $this->assertTrue($service->guardar($asistente));
    }

    public function test_findById_si_la_vista_no_lo_ve_lo_busca_en_out(): void
    {
        $asistente = new Asistente();
        $asistente->setId_activ(100411479);
        $asistente->setId_nom(100516905);

        $vista = $this->createMock(AsistenteRepositoryInterface::class);
        $vista->method('findById')->with(100411479, 100516905)->willReturn(null);

        $out = $this->createMock(AsistenteOutRepositoryInterface::class);
        $out->method('findById')->with(100411479, 100516905)->willReturn($asistente);

        $vacio = $this->createMock(AsistenteRepositoryInterface::class);
        $vacio->method('findById')->willReturn(null);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function (string $id) use ($out, $vacio) {
            return $id === AsistenteOutRepositoryInterface::class ? $out : $vacio;
        });

        $service = new AsistenteApplicationService(
            $vista,
            $this->createMock(UnitOfWorkInterface::class),
            $container,
        );

        $this->assertSame($asistente, $service->findById(100411479, 100516905));
    }
}
