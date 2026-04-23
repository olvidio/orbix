<?php

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\zonassacd\application\ZonaCtrLista;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;

/**
 * Unitarios para {@see ZonaCtrLista::execute()}.
 *
 * Cubre los tres caminos del switch:
 *  - `'no'`    -> centros DL sin zona asignada.
 *  - `'no_sf'` -> centros SF/Ellas sin zona asignada.
 *  - default   -> centros DL + SF de la zona, fusionados.
 *
 * Ademas verifica que los centros cuyo `id_ubi` empieza por `2` solo
 * aparezcan si el usuario tiene permisos `des` o `vcsd` (y en ese caso
 * se marquen con la clase `tono2`).
 */
final class ZonaCtrListaTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['oPerm'] = $this->oPermStub([]);
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_no_pide_centros_dl_sin_zona_y_no_busca_zona_null(): void
    {
        // Regresion: en esta rama `getId_zona()` es `null` y
        // `findById(int)` reventaba con TypeError al recibir null.
        $oCentro = $this->centroDlStub(1042, 'Centro DL', null);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())
            ->method('getCentros')
            ->with(
                $this->equalTo([
                    'active' => 't',
                    'id_zona' => '',
                    '_ordre' => 'nombre_ubi',
                ]),
                $this->equalTo(['id_zona' => 'IS NULL'])
            )
            ->willReturn([$oCentro]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class
                => $this->createStub(CentroEllasRepositoryInterface::class),
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $out = ZonaCtrLista::execute('no');

        $this->assertSame('tabla', $out['tipo']);
        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('1042', $first['sel']);
        $this->assertSame('Centro DL', $first[1]);
        $this->assertSame('', $first[2]);
    }

    public function test_no_sf_pide_centros_ellas_sin_zona(): void
    {
        $oCentro = $this->centroEllasStub(2055, 'Centro SF', null);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())
            ->method('getCentros')
            ->with(
                $this->equalTo([
                    'active' => 't',
                    'id_zona' => '',
                    '_ordre' => 'nombre_ubi',
                ]),
                $this->equalTo(['id_zona' => 'IS NULL'])
            )
            ->willReturn([$oCentro]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->never())->method('findById');

        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);
        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class
                => $this->createStub(CentroDlRepositoryInterface::class),
            CentroEllasRepositoryInterface::class => $centroEllasRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $out = ZonaCtrLista::execute('no_sf');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('tono2', $first['clase']);
        $this->assertSame('2055', $first['sel']);
    }

    public function test_default_fusiona_centros_dl_y_sf_de_la_zona(): void
    {
        $oCentroDl = $this->centroDlStub(1042, 'Centro DL', 9);
        $oCentroSf = $this->centroEllasStub(2055, 'Centro SF', 9);

        $centroDlRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDlRepo->expects($this->once())->method('getCentros')->willReturn([$oCentroDl]);

        $centroEllasRepo = $this->createMock(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->expects($this->once())->method('getCentros')->willReturn([$oCentroSf]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($this->zonaStub('Zona 9'));

        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);
        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class => $centroEllasRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $out = ZonaCtrLista::execute('9');

        $this->assertCount(2, $out['a_valores']);
        $vals = array_values($out['a_valores']);
        $this->assertSame('1042', $vals[0]['sel']);
        $this->assertSame('Zona 9', $vals[0][2]);
        $this->assertSame('2055', $vals[1]['sel']);
        $this->assertSame('tono2', $vals[1]['clase']);
    }

    public function test_sin_permisos_descarta_centros_con_id_ubi_empezando_por_2(): void
    {
        $oCentroDl = $this->centroDlStub(1042, 'Centro DL', 9);
        $oCentroSf = $this->centroEllasStub(2055, 'Centro SF', 9);

        $centroDlRepo = $this->createStub(CentroDlRepositoryInterface::class);
        $centroDlRepo->method('getCentros')->willReturn([$oCentroDl]);
        $centroEllasRepo = $this->createStub(CentroEllasRepositoryInterface::class);
        $centroEllasRepo->method('getCentros')->willReturn([$oCentroSf]);

        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturn($this->zonaStub('Zona 9'));

        // Sin permisos `des` ni `vcsd` -> el centro SF (id `2055`) se filtra.
        $GLOBALS['container'] = $this->containerFromMap([
            CentroDlRepositoryInterface::class => $centroDlRepo,
            CentroEllasRepositoryInterface::class => $centroEllasRepo,
            ZonaRepositoryInterface::class => $zonaRepo,
        ]);

        $out = ZonaCtrLista::execute('9');

        $this->assertCount(1, $out['a_valores']);
        $first = reset($out['a_valores']);
        $this->assertSame('1042', $first['sel']);
    }

    private function centroDlStub(int $id_ubi, string $nombre, ?int $id_zona): CentroDl
    {
        $stub = $this->createStub(CentroDl::class);
        $stub->method('getId_ubi')->willReturn($id_ubi);
        $stub->method('getNombre_ubi')->willReturn($nombre);
        $stub->method('getId_zona')->willReturn($id_zona);
        return $stub;
    }

    private function centroEllasStub(int $id_ubi, string $nombre, ?int $id_zona): CentroEllas
    {
        $stub = $this->createStub(CentroEllas::class);
        $stub->method('getId_ubi')->willReturn($id_ubi);
        $stub->method('getNombre_ubi')->willReturn($nombre);
        $stub->method('getId_zona')->willReturn($id_zona);
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
    private function oPermStub(array $perms): object
    {
        return new class ($perms) {
            /** @param array<string, bool> $perms */
            public function __construct(private readonly array $perms) {}
            public function have_perm_oficina(string $p): bool
            {
                return $this->perms[$p] ?? false;
            }
        };
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
