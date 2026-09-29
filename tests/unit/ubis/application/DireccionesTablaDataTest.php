<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\ubis\application\DireccionesResolver;
use src\ubis\application\DireccionesTablaData;
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
use src\ubis\domain\entity\Direccion;

final class DireccionesTablaDataTest extends TestCase
{
    public function test_fila_incluye_script_asignar_con_ctx_atado_al_id_direccion(): void
    {
        $direccion = $this->createMock(Direccion::class);
        $direccion->method('getId_direccion')->willReturn(77);
        $direccion->method('getDireccionVo')->willReturn(null);
        $direccion->method('getC_p')->willReturn('08001');
        $direccion->method('getPoblacion')->willReturn('Barcelona');
        $direccion->method('getProvincia')->willReturn('Barcelona');
        $direccion->method('getA_p')->willReturn(null);
        $direccion->method('getPais')->willReturn('España');
        $direccion->method('getF_direccion')->willReturn(null);
        $direccion->method('getObserv')->willReturn('');

        $direccionRepo = $this->createMock(DireccionCentroRepositoryInterface::class);
        $direccionRepo->method('getDirecciones')->willReturn([$direccion]);

        $useCase = new DireccionesTablaData($this->resolver($direccionRepo));
        $out = $useCase->execute(501, 'DireccionCentro', '', '', '');

        $this->assertCount(1, $out['a_valores']);
        $script = $out['a_valores'][1][2]['script'];
        $this->assertMatchesRegularExpression('/^fnjs_asignar_dir\("[^"]+"\)$/', $script);

        preg_match('/^fnjs_asignar_dir\("([^"]+)"\)$/', $script, $m);
        $this->assertSame(
            ['id_ubi' => 501, 'obj_dir' => 'DireccionCentro', 'id_direccion' => 77],
            HashB::open($m[1], 'direcciones_asignar')
        );
    }

    private function resolver(?DireccionCentroRepositoryInterface $direccionCentroRepo = null): DireccionesResolver
    {
        return new DireccionesResolver(
            $direccionCentroRepo ?? $this->createMock(DireccionCentroRepositoryInterface::class),
            $this->createMock(DireccionCentroDlRepositoryInterface::class),
            $this->createMock(DireccionCentroExRepositoryInterface::class),
            $this->createMock(DireccionCasaRepositoryInterface::class),
            $this->createMock(DireccionCasaDlRepositoryInterface::class),
            $this->createMock(DireccionCasaExRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(CentroExRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(CasaExRepositoryInterface::class),
        );
    }
}
