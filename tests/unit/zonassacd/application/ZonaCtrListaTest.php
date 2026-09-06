<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\permisos\domain\XPermisos;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\ZonaCtrLista;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;

final class ZonaCtrListaTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['oPerm'] = $this->oPermStub([]);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_no_pide_centros_dl_sin_zona(): void
    {
        $oCentro = $this->centroDlStub(1042, 'Centro DL');

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())
            ->method('getCentros')
            ->with(['active' => 't', '_ordre' => 'nombre_ubi'])
            ->willReturn([$oCentro]);

        $zonaCtr = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zonaCtr->expects($this->once())
            ->method('mapaZonaPorCentro')
            ->with([1042])
            ->willReturn([]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->never())->method('findById');

        $out = (new ZonaCtrLista(
            $centroDlRepo,
            $this->createStub(CentroEllasRepositoryInterface::class),
            $zonaRepo,
            $centrosDeZona,
        ))->execute('no');

        $this->assertSame('tabla', $out['tipo']);
        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('1042', $first['sel']);
        $this->assertSame('Centro DL', $first[1]);
        $this->assertSame('', $first[2]);
    }

    public function test_no_sf_pide_centros_ellas_sin_zona(): void
    {
        $oCentro = $this->centroEllasStub(2055, 'Centro SF');

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())
            ->method('getCentros')
            ->with(['active' => 't', '_ordre' => 'nombre_ubi'])
            ->willReturn([$oCentro]);

        $zonaCtr = $this->createStub(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('mapaZonaPorCentro')->willReturn([]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $out = (new ZonaCtrLista(
            $this->createStub(CentroDlRepositoryInterface::class),
            $centroEllasRepo,
            $this->createStub(ZonaRepositoryInterface::class),
            $centrosDeZona,
        ))->execute('no_sf');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('tono2', $first['clase']);
        $this->assertSame('2055', $first['sel']);
    }

    public function test_default_fusiona_centros_dl_y_sf_de_la_zona(): void
    {
        $oCentroDl = $this->centroDlStub(1042, 'Centro DL');
        $oCentroSf = $this->centroEllasStub(2055, 'Centro SF');

        $zonaCtr = $this->createStub(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('idUbisDeZona')->willReturn([1042, 2055]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $filtro = [
            ['active' => 't', 'id_ubi' => [1042, 2055], '_ordre' => 'nombre_ubi'],
            ['id_ubi' => 'IN'],
        ];
        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())->method('getCentros')->with($filtro[0], $filtro[1])->willReturn([$oCentroDl]);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())->method('getCentros')->with($filtro[0], $filtro[1])->willReturn([$oCentroSf]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->with(9)->willReturn($this->zonaStub('Zona 9'));

        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);
        $out = (new ZonaCtrLista($centroDlRepo, $centroEllasRepo, $zonaRepo, $centrosDeZona))->execute('9');

        $this->assertCount(2, $out['a_valores']);
        $vals = array_values($out['a_valores']);
        $this->assertSame('1042', $vals[0]['sel']);
        $this->assertSame('Zona 9', $vals[0][2]);
        $this->assertSame('2055', $vals[1]['sel']);
        $this->assertSame('tono2', $vals[1]['clase']);
    }

    public function test_con_sel_solo_con_permiso_des_o_vcsd(): void
    {
        $zonaCtr = $this->createStub(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('idUbisDeZona')->willReturn([1042]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $centroDlRepo = $this->createStub(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('getCentros')->willReturn([$this->centroDlStub(1042, 'Centro DL')]);
        $centroEllasRepo = $this->createStub(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->method('getCentros')->willReturn([]);
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($this->zonaStub('Zona 9'));

        $lista = new ZonaCtrLista($centroDlRepo, $centroEllasRepo, $zonaRepo, $centrosDeZona);
        $this->assertFalse($lista->execute('9')['con_sel']);

        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);
        $this->assertTrue((new ZonaCtrLista($centroDlRepo, $centroEllasRepo, $zonaRepo, $centrosDeZona))->execute('9')['con_sel']);
    }

    public function test_sin_permisos_descarta_centros_con_id_ubi_empezando_por_2(): void
    {
        $zonaCtr = $this->createStub(ZonaCtrRepositoryInterface::class);
        $zonaCtr->method('idUbisDeZona')->willReturn([1042, 2055]);
        $centrosDeZona = new CentrosDeZona($zonaCtr);

        $centroDlRepo = $this->createStub(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('getCentros')->willReturn([$this->centroDlStub(1042, 'Centro DL')]);
        $centroEllasRepo = $this->createStub(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->method('getCentros')->willReturn([$this->centroEllasStub(2055, 'Centro SF')]);
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($this->zonaStub('Zona 9'));

        $out = (new ZonaCtrLista($centroDlRepo, $centroEllasRepo, $zonaRepo, $centrosDeZona))->execute('9');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('1042', $first['sel']);
    }

    private function centroDlStub(int $id_ubi, string $nombre): CentroDl
    {
        $stub = $this->createStub(CentroDl::class);
        $stub->method('getId_ubi')->willReturn($id_ubi);
        $stub->method('getNombre_ubi')->willReturn($nombre);
        return $stub;
    }

    private function centroEllasStub(int $id_ubi, string $nombre): CentroEllas
    {
        $stub = $this->createStub(CentroEllas::class);
        $stub->method('getId_ubi')->willReturn($id_ubi);
        $stub->method('getNombre_ubi')->willReturn($nombre);
        return $stub;
    }

    private function zonaStub(string $nombre): Zona
    {
        $stub = $this->createStub(Zona::class);
        $stub->method('getNombre_zona')->willReturn($nombre);
        return $stub;
    }

    /**
     * @param array<string, bool> $perms
     */
    private function oPermStub(array $perms): XPermisos
    {
        $stub = $this->createMock(XPermisos::class);
        $stub->method('have_perm_oficina')->willReturnCallback(
            static fn (string $p): bool => $perms[$p] ?? false
        );
        return $stub;
    }
}
