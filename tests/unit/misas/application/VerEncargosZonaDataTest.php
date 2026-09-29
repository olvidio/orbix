<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoTipo;
use src\misas\application\VerEncargosZonaData;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

final class VerEncargosZonaDataTest extends TestCase
{
    public function test_sin_tipos_de_encargo_devuelve_filas_vacias_y_ctx_nuevo(): void
    {
        $useCase = $this->makeUseCase([]);

        $out = $useCase->getData(9, 'orden');

        $this->assertSame([], $out['rows']);
        $this->assertSame(
            ['id_enc' => 0],
            HashB::open($out['ctx_nuevo'], 'guardar_encargo_zona')
        );
    }

    public function test_fila_incluye_ctx_guardar_y_ctx_eliminar_atados_al_id_enc(): void
    {
        $encargoTipo = $this->createMock(EncargoTipo::class);
        $encargoTipo->method('getId_tipo_enc')->willReturn(8100);
        $encargoTipo->method('getTipo_enc')->willReturn('Misa dominical');

        $encargo = $this->createMock(Encargo::class);
        $encargo->method('getId_enc')->willReturn(77);
        $encargo->method('getId_ubi')->willReturn(null);
        $encargo->method('getId_tipo_enc')->willReturn(8100);
        $encargo->method('getIdioma_enc')->willReturn(null);
        $encargo->method('getDesc_enc')->willReturn('Encargo test');
        $encargo->method('getDesc_lugar')->willReturn('');
        $encargo->method('getOrden')->willReturn(1);
        $encargo->method('getPrioridad')->willReturn(1);
        $encargo->method('getObserv')->willReturn('');

        $encargoTipoRepository = $this->createMock(EncargoTipoRepositoryInterface::class);
        $encargoTipoRepository->method('getEncargoTipos')->willReturn([$encargoTipo]);
        $encargoTipoRepository->method('findById')->with(8100)->willReturn($encargoTipo);

        $encargoRepository = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepository->method('getEncargos')->willReturn([$encargo]);

        $useCase = $this->makeUseCase([], $encargoTipoRepository, $encargoRepository);
        $out = $useCase->getData(9, 'orden');

        $this->assertCount(1, $out['rows']);
        $row = $out['rows'][0];
        $this->assertSame(77, $row['id_enc']);
        $this->assertSame(
            ['id_enc' => 77],
            HashB::open($row['ctx_guardar'], 'guardar_encargo_zona')
        );
        $this->assertSame(
            ['id_enc' => 77],
            HashB::open($row['ctx_eliminar'], 'eliminar_encargo_zona')
        );
    }

    /**
     * @param list<int> $idUbisDeZona
     */
    private function makeUseCase(
        array $idUbisDeZona,
        ?EncargoTipoRepositoryInterface $encargoTipoRepository = null,
        ?EncargoRepositoryInterface $encargoRepository = null,
    ): VerEncargosZonaData {
        $encargoTipoRepository = $encargoTipoRepository ?? $this->createMock(EncargoTipoRepositoryInterface::class);
        $encargoRepository = $encargoRepository ?? $this->createMock(EncargoRepositoryInterface::class);

        $localRepository = $this->createMock(LocalRepositoryInterface::class);
        $localRepository->method('getArrayLocales')->willReturn([]);

        $centroEllosRepository = $this->createMock(CentroEllosRepositoryInterface::class);
        $centroEllosRepository->method('getCentros')->willReturn([]);

        $centroEllasRepository = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepository->method('getCentros')->willReturn([]);

        $zonaCtrRepository = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtrRepository->method('idUbisDeZona')->willReturn($idUbisDeZona);

        return new VerEncargosZonaData(
            $encargoTipoRepository,
            $encargoRepository,
            $localRepository,
            $centroEllosRepository,
            $centroEllasRepository,
            new CentrosDeZona($zonaCtrRepository),
        );
    }
}
