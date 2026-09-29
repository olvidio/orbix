<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoDescText;
use src\misas\application\VerEncargosCentrosData;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroEllos;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class VerEncargosCentrosDataTest extends TestCase
{
    public function test_id_zona_vacio_devuelve_grid_sin_filas_y_ctx_nuevo(): void
    {
        $useCase = $this->makeUseCase([]);

        $data = $useCase->getData(0);

        $this->assertSame([], $data['rows']);
        $this->assertSame(
            ['id_item' => ''],
            HashB::open($data['ctx_nuevo'], 'guardar_encargo_centro')
        );
    }

    public function test_fila_incluye_ctx_guardar_y_ctx_eliminar_atados_al_id_item(): void
    {
        $idUbi = 501;
        $centro = $this->createMock(CentroEllos::class);
        $centro->method('getId_ubi')->willReturn($idUbi);
        $centro->method('getNombre_ubi')->willReturn('Centro Z');

        $uuidItem = EncargoCtrId::random()->value();
        $encargoCtr = $this->createMock(EncargoCtr::class);
        $encargoCtr->method('getUuidItemVo')->willReturn(new EncargoCtrId($uuidItem));
        $encargoCtr->method('getId_enc')->willReturn(7);

        $encargo = $this->createMock(Encargo::class);
        $encargo->method('getDescEncVo')->willReturn(new EncargoDescText('Misa dominical'));

        $encargoCtrRepository = $this->createMock(EncargoCtrRepositoryInterface::class);
        $encargoCtrRepository->method('getEncargosCentro')->with($idUbi)->willReturn([$encargoCtr]);

        $encargoRepository = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepository->method('findById')->with(7)->willReturn($encargo);

        $centroEllosRepository = $this->createMock(CentroEllosRepositoryInterface::class);
        $centroEllosRepository->method('getCentros')->willReturn([$centro]);

        $centroEllasRepository = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepository->method('getCentros')->willReturn([]);

        $zonaCtrRepository = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtrRepository->method('idUbisDeZona')->willReturn([$idUbi]);

        $zonaRepository = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepository->method('getArrayZonas')->willReturn([]);

        $useCase = new VerEncargosCentrosData(
            $centroEllosRepository,
            $centroEllasRepository,
            $encargoCtrRepository,
            $encargoRepository,
            $zonaRepository,
            new CentrosDeZona($zonaCtrRepository),
        );

        $data = $useCase->getData(3);

        $this->assertCount(1, $data['rows']);
        $row = $data['rows'][0];
        $this->assertSame($uuidItem, $row['id_item']);
        $this->assertSame(
            ['id_item' => $uuidItem],
            HashB::open($row['ctx_guardar'], 'guardar_encargo_centro')
        );
        $this->assertSame(
            ['id_item' => $uuidItem],
            HashB::open($row['ctx_eliminar'], 'eliminar_encargo_centro')
        );
    }

    /**
     * @param list<int> $idUbisDeZona
     */
    private function makeUseCase(array $idUbisDeZona): VerEncargosCentrosData
    {
        $centroEllosRepository = $this->createMock(CentroEllosRepositoryInterface::class);
        $centroEllosRepository->method('getCentros')->willReturn([]);

        $centroEllasRepository = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepository->method('getCentros')->willReturn([]);

        $encargoCtrRepository = $this->createMock(EncargoCtrRepositoryInterface::class);
        $encargoRepository = $this->createMock(EncargoRepositoryInterface::class);
        $zonaRepository = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepository->method('getArrayZonas')->willReturn([]);

        $zonaCtrRepository = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtrRepository->method('idUbisDeZona')->willReturn($idUbisDeZona);

        return new VerEncargosCentrosData(
            $centroEllosRepository,
            $centroEllasRepository,
            $encargoCtrRepository,
            $encargoRepository,
            $zonaRepository,
            new CentrosDeZona($zonaCtrRepository),
        );
    }
}
