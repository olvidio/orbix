<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\UbisBuscarOpcionesData;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\entity\Region;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\RegionNameText;

final class UbisBuscarOpcionesDataTest extends TestCase
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

    public function test_agruppa_dropdowns_y_paises(): void
    {
        $region = $this->createMock(Region::class);
        $region->method('getRegionVo')->willReturn(new RegionCode('cr'));
        $region->method('getNombreRegionVo')->willReturn(new RegionNameText('Costa'));

        $regionRepo = $this->createMock(RegionRepositoryInterface::class);
        $regionRepo->method('getRegiones')->willReturn([$region]);

        $tipoCtrRepo = $this->createMock(TipoCentroRepositoryInterface::class);
        $tipoCtrRepo->method('getArrayTiposCentro')->willReturn(['Z' => 'Zonal']);

        $tipoCasaRepo = $this->createMock(TipoCasaRepositoryInterface::class);
        $tipoCasaRepo->method('getArrayTiposCasa')->willReturn(['c' => 'Casa']);

        $dirRepo = $this->createMock(DireccionCentroRepositoryInterface::class);
        $dirRepo->method('getArrayPaises')->willReturn(['ES' => 'España']);

        $GLOBALS['container'] = $this->containerFromMap([
            RegionRepositoryInterface::class => $regionRepo,
            TipoCentroRepositoryInterface::class => $tipoCtrRepo,
            TipoCasaRepositoryInterface::class => $tipoCasaRepo,
            DireccionCentroRepositoryInterface::class => $dirRepo,
        ]);

        $out = UbisBuscarOpcionesData::execute();

        $this->assertSame(['cr' => 'Costa'], $out['opciones_region']);
        $this->assertSame(['Z' => 'Zonal'], $out['opciones_tipo_ctr']);
        $this->assertSame(['c' => 'Casa'], $out['opciones_tipo_casa']);
        $this->assertSame(['ES' => 'España'], $out['opciones_pais']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
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
