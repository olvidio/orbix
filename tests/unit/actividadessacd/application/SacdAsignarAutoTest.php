<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadessacd\application\SacdAsignarAuto;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;

final class SacdAsignarAutoTest extends TestCase
{
    private mixed $previousContainer;

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

    public function test_f_ini_iso_vacio_no_hace_nada(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = SacdAsignarAuto::execute(['f_ini_iso' => '']);
        $this->assertSame(['asignadas' => 0, 'sin_asignar' => 0], $out);
    }

    public function test_asigna_cuando_hay_centro_y_sacd_titular(): void
    {
        $actividadRepo = $this->createMock(ActividadDlRepositoryInterface::class);
        $actividadRepo->method('getArrayIdsWithKeyFini')->willReturn(['2026-05-01#1' => 500]);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([3100 => 'sacd1']);
        $cargoRepo->method('getArrayIdCargosSacd')->willReturn([3100]);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);
        $activCargoRepo->method('getNewId')->willReturn(9001);
        $activCargoRepo->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(function (ActividadCargo $c) {
                return $c->getId_activ() === 500
                    && $c->getId_cargo() === 3100
                    && $c->getId_nom() === 777
                    && $c->getId_item() === 9001;
            }))
            ->willReturn(true);

        $ce = new CentroEncargado();
        $ce->setId_activ(500);
        $ce->setId_ubi(50);
        $ce->setNum_orden(0);

        $centroEncRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $centroEncRepo->method('getCentrosEncargados')->willReturn([$ce]);

        $oEnc = new Encargo();
        $oEnc->setId_enc(11);
        $oEnc->setId_ubi(50);

        $oEncSacd = new EncargoSacd();
        $oEncSacd->setId_enc(11);
        $oEncSacd->setId_nom(777);

        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->method('getEncargos')->willReturn([$oEnc]);

        $encargoSacdRepo = $this->createMock(EncargoSacdRepositoryInterface::class);
        $encargoSacdRepo->method('getEncargosSacd')->willReturn([$oEncSacd]);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadDlRepositoryInterface::class => $actividadRepo,
            CargoRepositoryInterface::class => $cargoRepo,
            ActividadCargoRepositoryInterface::class => $activCargoRepo,
            CentroEncargadoRepositoryInterface::class => $centroEncRepo,
            EncargoRepositoryInterface::class => $encargoRepo,
            EncargoSacdRepositoryInterface::class => $encargoSacdRepo,
        ]);

        $out = SacdAsignarAuto::execute(['f_ini_iso' => '2026-01-01']);
        $this->assertSame(['asignadas' => 1, 'sin_asignar' => 0], $out);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
