<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\UbisEditarLoadData;
use src\ubis\application\UbisEditarNormalizeDlData;
use src\ubis\application\services\UbiRepositoryResolver;
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
use src\ubis\domain\entity\CentroDl;

final class UbisEditarLoadDataTest extends TestCase
{
    private CentroDlRepositoryInterface $centroDlRepository;
    private UbisEditarLoadData $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->centroDlRepository = $this->createMock(CentroDlRepositoryInterface::class);

        $resolver = new UbiRepositoryResolver(
            $this->createMock(CentroRepositoryInterface::class),
            $this->centroDlRepository,
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
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

        $normalizeDl = new UbisEditarNormalizeDlData(
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
        );

        $this->useCase = new UbisEditarLoadData($resolver, $normalizeDl);
    }

    public function test_nuevo_emite_ctx_guardar_y_ctx_eliminar_con_id_ubi_cero(): void
    {
        $result = $this->useCase->execute([
            'nuevo' => '1',
            'obj_pau' => 'CentroDl',
            'tipo_ubi' => 'ctrdl',
        ]);

        $this->assertSame('', $result['id_ubi']);

        $ctxGuardar = HashB::open($result['ctx_guardar'], 'ubis_guardar');
        $this->assertSame(['obj_pau' => 'CentroDl', 'id_ubi' => 0], $ctxGuardar);

        $ctxEliminar = HashB::open($result['ctx_eliminar'], 'ubis_eliminar');
        $this->assertSame(['obj_pau' => 'CentroDl', 'id_ubi' => 0], $ctxEliminar);
    }

    public function test_existente_emite_ctx_guardar_y_ctx_eliminar_con_obj_pau_e_id_ubi_reales(): void
    {
        $centro = $this->createMock(CentroDl::class);
        $centro->method('getTipo_ubi')->willReturn('ctrdl');
        $centro->method('getDl')->willReturn('');
        $centro->method('getId_ubi')->willReturn(42);
        $centro->method('getRegion')->willReturn('');
        $centro->method('getNombre_ubi')->willReturn('Centro test');
        $centro->method('isActive')->willReturn(true);
        $centro->method('isCdc')->willReturn(false);
        $centro->method('getTipo_labor')->willReturn(0);
        $centro->method('getId_ctr_padre')->willReturn(0);
        $centro->method('getTipo_ctr')->willReturn('');
        $centro->method('getNum_pi')->willReturn(0);
        $centro->method('getNum_cartas')->willReturn(0);
        $centro->method('getNum_cartas_mensuales')->willReturn(0);
        $centro->method('getNum_habit_indiv')->willReturn(0);
        $centro->method('getPlazas')->willReturn(0);
        $centro->method('getN_buzon')->willReturn(0);
        $centro->method('getObserv')->willReturn('');

        $this->centroDlRepository->method('findById')->with(42)->willReturn($centro);

        $result = $this->useCase->execute([
            'id_ubi' => 42,
            'obj_pau' => 'CentroDl',
        ]);

        // Sin `dl` asignada, `UbiPermisos::dlPerteneceAMiDelegacion` devuelve
        // false y el caso de uso normaliza el centro a `CentroEx` (solo
        // lectura); la cápsula debe reflejar ese `obj_pau` final, no el que
        // llegó por POST.
        $this->assertSame(42, $result['id_ubi']);
        $this->assertSame('CentroEx', $result['obj_pau']);

        $ctxGuardar = HashB::open($result['ctx_guardar'], 'ubis_guardar');
        $this->assertSame(['obj_pau' => 'CentroEx', 'id_ubi' => 42], $ctxGuardar);

        $ctxEliminar = HashB::open($result['ctx_eliminar'], 'ubis_eliminar');
        $this->assertSame(['obj_pau' => 'CentroEx', 'id_ubi' => 42], $ctxEliminar);
    }
}
