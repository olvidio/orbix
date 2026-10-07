<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\ActividadCargoNuevo;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;

final class ActividadCargoNuevoTest extends TestCase
{
    private function createSut(
        ?ActividadCargoRepositoryInterface $cargoRepo = null,
        ?DossierRepositoryInterface $dossierRepo = null,
    ): ActividadCargoNuevo {
        return new ActividadCargoNuevo(
            $cargoRepo ?? $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->createMock(AsistenteApplicationService::class),
            $this->createMock(AsistenteDlRepositoryInterface::class),
            $this->createMock(AsistenteOutRepositoryInterface::class),
            $this->createMock(AsistenteExRepositoryInterface::class),
            $this->createMock(AsistentePubRepositoryInterface::class),
            $dossierRepo ?? $this->createMock(DossierRepositoryInterface::class),
        );
    }

    public function test_rechaza_id_nom_cero(): void
    {
        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->expects($this->never())->method('Guardar');

        $sut = $this->createSut($cargoRepo);

        $this->assertNotSame('', $sut->execute([
            'id_activ' => 10,
            'id_nom' => 0,
            'id_cargo' => 3,
        ]));
    }

    public function test_nuevo_acepta_id_nom_negativo_de_persona_de_paso(): void
    {
        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getNewId')->willReturn(77);
        $cargoRepo->expects($this->once())->method('Guardar')->willReturnCallback(
            static function (ActividadCargo $cargo): bool {
                return $cargo->getId_activ() === 10
                    && $cargo->getId_nom() === -1001123
                    && $cargo->getId_cargo() === 3
                    && $cargo->getId_item() === 77;
            }
        );

        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->exactly(2))->method('abrir');
        $dossierRepo = $this->createMock(DossierRepositoryInterface::class);
        $dossierRepo->method('findByPk')->willReturn($dossier);
        $dossierRepo->expects($this->exactly(2))->method('Guardar')->with($dossier)->willReturn(true);

        $sut = $this->createSut($cargoRepo, $dossierRepo);

        $this->assertSame('', $sut->execute([
            'id_activ' => 10,
            'id_nom' => -1001123,
            'id_cargo' => 3,
        ]));
    }
}
