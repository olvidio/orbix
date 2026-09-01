<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\application\UbisGuardar;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
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
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Delegacion;

final class UbisGuardarTest extends TestCase
{
    public function test_obj_pau_desconocido_devuelve_mensaje(): void
    {
        $guardar = new UbisGuardar(
            $this->resolver(),
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(TrasladoUbiRepositoryInterface::class),
        );

        $msg = $guardar->execute(['obj_pau' => 'NoExiste', 'id_ubi' => 1, 'nombre_ubi' => 'x']);
        $this->assertStringContainsString('NoExiste', $msg);
    }

    public function test_casa_ex_con_esquema_destino_traslada_desde_resto(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getId_ubi')->willReturn(31101967);
        $casa->method('getDl')->willReturn('dlmE');
        $casa->method('getRegion')->willReturn('H');
        $casa->expects($this->atLeastOnce())->method('setDl');
        $casa->expects($this->atLeastOnce())->method('setRegion');
        $casa->expects($this->atLeastOnce())->method('setNombre_ubi');

        $repo = $this->createMock(CasaExRepositoryInterface::class);
        $repo->method('findById')->with(31101967)->willReturn($casa);
        $repo->expects($this->once())->method('Guardar')->with($casa)->willReturn(true);

        $delegacion = $this->createMock(Delegacion::class);
        $delegacion->method('getRegion')->willReturn('H');
        $dlRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $dlRepo->method('getDelegaciones')->willReturn([$delegacion]);

        $traslado = $this->createMock(TrasladoUbiRepositoryInterface::class);
        $traslado->expects($this->once())->method('existeCdcDl')->with('H-dlmE')->willReturn(true);
        $traslado->expects($this->once())->method('trasladoCdcDesdeResto')->with(31101967, 'H-dlmE')->willReturn(true);

        $guardar = new UbisGuardar($this->resolver($repo), $dlRepo, $traslado);
        $msg = $guardar->execute([
            'obj_pau' => 'CasaEx',
            'id_ubi' => 31101967,
            'tipo_ubi' => 'cdcex',
            'nombre_ubi' => 'Al Tilal',
            'dl' => 'dlmE',
            'region' => 'Li',
            'active' => 'true',
            'tipo_casa' => 'cdc',
            'plazas' => 10,
            'plazas_min' => 1,
            'num_sacd' => 0,
            'sv' => 'true',
            'sf' => '',
        ]);
        $this->assertSame('', $msg);
    }

    public function test_status_legado_se_usa_como_active(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getId_ubi')->willReturn(31101967);
        $casa->method('getDl')->willReturn('cr');
        $casa->method('getRegion')->willReturn('Li');
        $casa->expects($this->once())->method('setActive')->with(true);

        $repo = $this->createMock(CasaExRepositoryInterface::class);
        $repo->method('findById')->willReturn($casa);
        $repo->method('Guardar')->willReturn(true);

        $traslado = $this->createMock(TrasladoUbiRepositoryInterface::class);
        $traslado->method('existeCdcDl')->willReturn(false);
        $traslado->expects($this->never())->method('trasladoCdcDesdeResto');

        $guardar = new UbisGuardar(
            $this->resolver($repo),
            $this->createMock(DelegacionRepositoryInterface::class),
            $traslado,
        );
        $msg = $guardar->execute([
            'obj_pau' => 'CasaEx',
            'id_ubi' => 31101967,
            'tipo_ubi' => 'cdcex',
            'nombre_ubi' => 'Al Tilal',
            'dl' => 'cr',
            'region' => 'Li',
            'status' => 'on',
            'tipo_casa' => 'cdc',
        ]);
        $this->assertSame('', $msg);
    }

    private function resolver(?CasaExRepositoryInterface $casaEx = null): UbiRepositoryResolver
    {
        return new UbiRepositoryResolver(
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $casaEx ?? $this->createMock(CasaExRepositoryInterface::class),
            $this->createMock(TelecoCtrRepositoryInterface::class),
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
