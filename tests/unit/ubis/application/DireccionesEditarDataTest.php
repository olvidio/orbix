<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\DireccionesEditarData;
use src\ubis\application\DireccionesResolver;
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
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroEx;
use src\ubis\domain\entity\Direccion;
use src\ubis\domain\entity\DireccionDetalle;

final class DireccionesEditarDataTest extends TestCase
{
    private mixed $permAnterior = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->permAnterior === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->permAnterior;
        }
        parent::tearDown();
    }

    public function test_quien_puede_modificar_recibe_las_capsulas_del_plano_de_la_direccion_que_ve(): void
    {
        $this->permisoScdl(true);

        $data = $this->editarCentroEx();

        $contexto = ['obj_dir' => 'DireccionCentroEx', 'id_direccion' => 345];
        $this->assertSame($contexto, HashB::open($data['ctx_plano_subir'], 'plano_subir'));
        $this->assertSame($contexto, HashB::open($data['ctx_plano_borrar'], 'plano_borrar'));
    }

    public function test_sin_permiso_para_modificar_no_hay_capsulas_del_plano(): void
    {
        $this->permisoScdl(false);

        $data = $this->editarCentroEx();

        $this->assertSame('', $data['ctx_plano_subir']);
        $this->assertSame('', $data['ctx_plano_borrar']);
    }

    /** @return array<string, mixed> */
    private function editarCentroEx(): array
    {
        $detalle = $this->createMock(DireccionDetalle::class);
        $detalle->method('getDireccionVo')->willReturn($this->createMock(Direccion::class));
        $centro = $this->createMock(CentroEx::class);
        $centro->method('getDl')->willReturn('xyz');
        $centro->method('getUnaDireccionDetallada')->with(345)->willReturn($detalle);
        $centroExRepo = $this->createMock(CentroExRepositoryInterface::class);
        $centroExRepo->method('findById')->with(77)->willReturn($centro);

        $useCase = new DireccionesEditarData($this->resolver(null, $centroExRepo), $this->ubiRepositoryResolver());

        return $useCase->execute(77, 'editar', 'DireccionCentroEx', '345', 0, '');
    }

    private function permisoScdl(bool $tiene): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => $tiene && $o === MenuDlPermissionBits::SCDL);
        $_SESSION['oPerm'] = $perm;
    }

    public function test_modo_nuevo_emite_ctx_guardar_y_ctx_quitar_atados_al_contexto(): void
    {
        $centro = $this->createMock(Centro::class);
        $centro->method('getDl')->willReturn('bcn');

        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('findById')->with(501)->willReturn($centro);

        $useCase = new DireccionesEditarData(
            $this->resolver($centroRepo),
            $this->ubiRepositoryResolver(),
        );

        $data = $useCase->execute(501, 'nuevo', 'DireccionCentro', '', 0, '');

        $this->assertFalse($data['sin_direccion']);
        $this->assertSame(-1, $data['idx']);

        $contexto = ['obj_dir' => 'DireccionCentro', 'id_ubi' => 501, 'idx' => -1, 'id_direccion' => ''];
        $this->assertSame($contexto, HashB::open($data['ctx_guardar'], 'direccion_update'));
        $this->assertSame($contexto, HashB::open($data['ctx_quitar'], 'direcciones_quitar'));
    }

    public function test_ubi_no_encontrado_no_emite_ctx(): void
    {
        $centroRepo = $this->createMock(CentroRepositoryInterface::class);
        $centroRepo->method('findById')->willReturn(null);

        $useCase = new DireccionesEditarData(
            $this->resolver($centroRepo),
            $this->ubiRepositoryResolver(),
        );

        $data = $useCase->execute(999, 'nuevo', 'DireccionCentro', '', 0, '');

        $this->assertTrue($data['sin_direccion']);
        $this->assertArrayNotHasKey('ctx_guardar', $data);
    }

    private function resolver(?CentroRepositoryInterface $centroRepo = null, ?CentroExRepositoryInterface $centroExRepo = null): DireccionesResolver
    {
        return new DireccionesResolver(
            $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $centroRepo ?? $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $centroExRepo ?? $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
        );
    }

    private function ubiRepositoryResolver(): UbiRepositoryResolver
    {
        return new UbiRepositoryResolver(
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
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
    }
}
