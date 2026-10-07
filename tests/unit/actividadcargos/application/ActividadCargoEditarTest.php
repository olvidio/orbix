<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\ActividadCargoEditar;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;

final class ActividadCargoEditarTest extends TestCase
{
    private function createSut(
        ?ActividadCargoRepositoryInterface $cargoRepo = null,
    ): ActividadCargoEditar {
        return new ActividadCargoEditar(
            $cargoRepo ?? $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->createMock(AsistenteApplicationService::class),
            $this->createMock(AsistenteDlRepositoryInterface::class),
            $this->createMock(AsistenteOutRepositoryInterface::class),
            $this->createMock(AsistenteExRepositoryInterface::class),
            $this->createMock(AsistentePubRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );
    }

    public function test_rechaza_id_nom_cero(): void
    {
        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->expects($this->never())->method('Guardar');

        $sut = $this->createSut($cargoRepo);

        $this->assertNotSame('', $sut->execute([
            'id_item' => 5,
            'id_activ' => 10,
            'id_nom' => 0,
            'id_cargo' => 3,
        ]));
    }

    public function test_editar_acepta_id_nom_negativo_de_persona_de_paso(): void
    {
        $existente = new ActividadCargo();
        $existente->setId_item(5);

        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('findById')->with(5)->willReturn($existente);
        $cargoRepo->expects($this->once())->method('Guardar')->willReturnCallback(
            static function (ActividadCargo $cargo): bool {
                return $cargo->getId_item() === 5
                    && $cargo->getId_activ() === 10
                    && $cargo->getId_nom() === -1001123
                    && $cargo->getId_cargo() === 4;
            }
        );

        $sut = $this->createSut($cargoRepo);

        $this->assertSame('', $sut->execute([
            'id_item' => 5,
            'id_activ' => 10,
            'id_nom' => -1001123,
            'id_cargo' => 4,
        ]));
    }
}
