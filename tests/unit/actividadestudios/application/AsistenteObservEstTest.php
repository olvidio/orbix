<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\AsistenteObservEst;
use src\asistentes\application\services\AsistenteActividadService;
use Psr\Container\ContainerInterface;

/**
 * El flujo completo depende de {@see AsistenteActividadService::getRepoAsistente}
 * (persona + actividad en global). Solo se cubren validaciones de entrada.
 */
final class AsistenteObservEstTest extends TestCase
{
    public function test_faltan_ids_devuelve_mensaje(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->never())->method('get');
        $service = $this->createMock(AsistenteActividadService::class);
        $service->expects($this->never())->method('getRepoAsistente');

        $useCase = new AsistenteObservEst($container, $service);

        $msg = $useCase->execute(['id_activ' => 0, 'id_nom' => 5]);
        $this->assertNotSame('', $msg);

        $msg2 = $useCase->execute(['id_activ' => 9, 'id_nom' => 0, 'id_pau' => 0]);
        $this->assertNotSame('', $msg2);
    }
}
