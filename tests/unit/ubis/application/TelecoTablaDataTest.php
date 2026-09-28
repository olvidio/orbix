<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\application\TelecoTablaData;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;

final class TelecoTablaDataTest extends TestCase
{
    public function test_fila_incluye_ctx_eliminar_atado_a_obj_pau_y_pkey(): void
    {
        $teleco = $this->createMock(TelecoUbi::class);
        $teleco->method('getPrimary_key')->willReturn('id_item');
        $teleco->method('getId_item')->willReturn(42);
        $teleco->method('getDatosCampos')->willReturn([]);

        $repo = $this->createMock(TelecoCtrRepositoryInterface::class);
        $repo->method('getTelecos')->willReturn([$teleco]);

        $useCase = new TelecoTablaData($this->resolver($repo));
        $out = $useCase->execute('Centro', 5);

        $this->assertCount(1, $out['a_valores']);
        $row = $out['a_valores'][0];
        $this->assertArrayHasKey('sel', $row);
        $this->assertSame(
            ['obj_pau' => 'Centro', 'pkey' => 42],
            HashB::open($row['ctx_eliminar'], 'teleco_eliminar')
        );
    }

    public function test_sin_telecos_devuelve_valores_vacios(): void
    {
        $repo = $this->createMock(TelecoCtrRepositoryInterface::class);
        $repo->method('getTelecos')->willReturn([]);

        $useCase = new TelecoTablaData($this->resolver($repo));
        $out = $useCase->execute('Centro', 5);

        $this->assertSame([], $out['a_valores']);
    }

    private function resolver(?TelecoCtrRepositoryInterface $telecoCtr = null): UbiRepositoryResolver
    {
        return new UbiRepositoryResolver(
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
            $telecoCtr ?? $this->createMock(TelecoCtrRepositoryInterface::class),
            $this->createMock(TelecoCtrDlRepositoryInterface::class),
            $this->createMock(TelecoCtrExRepositoryInterface::class),
            $this->createMock(TelecoCdcRepositoryInterface::class),
            $this->createMock(TelecoCdcDlRepositoryInterface::class),
            $this->createMock(TelecoCdcExRepositoryInterface::class),
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $this->createMock(RelacionCentroDireccionRepositoryInterface::class),
            $this->createMock(RelacionCentroDlDireccionRepositoryInterface::class),
            $this->createMock(RelacionCentroExDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaDlDireccionRepositoryInterface::class),
            $this->createMock(RelacionCasaExDireccionRepositoryInterface::class),
        );
    }
}
