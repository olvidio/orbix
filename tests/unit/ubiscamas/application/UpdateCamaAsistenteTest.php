<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\ubiscamas\application\UpdateCamaAsistente;

final class UpdateCamaAsistenteTest extends TestCase
{
    public function test_asigna_cama_a_asistente_de_paso_con_id_negativo(): void
    {
        $idNom = -15;
        $idActiv = 9;
        $idCama = Uuid::uuid4()->toString();

        $asistente = $this->createMock(Asistente::class);
        $asistente->expects($this->once())->method('setCamaVo');

        $repo = $this->createMock(AsistenteExRepositoryInterface::class);
        $repo->expects($this->once())->method('Guardar')->with($asistente)->willReturn(true);

        $servicio = $this->createMock(AsistenteActividadService::class);
        $servicio->method('buscarAsistencia')->with($idNom, $idActiv)->willReturn($asistente);
        $servicio->method('getRepoAsistente')->with($idNom, $idActiv)->willReturn(AsistenteExRepositoryInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(AsistenteExRepositoryInterface::class)->willReturn($repo);

        $out = (new UpdateCamaAsistente($servicio, $container))->execute($idNom, $idActiv, $idCama);

        $this->assertSame(['success' => true, 'mensaje' => 'ok'], $out);
    }
}
