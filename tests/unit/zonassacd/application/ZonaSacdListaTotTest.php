<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\zonassacd\application\ZonaSacdListaTot;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use src\zonassacd\domain\entity\ZonaSacd;

final class ZonaSacdListaTotTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth']['esquema'] = 'b-bnv';
        $_SESSION['session_auth']['sfsv'] = 1;
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_sacd_con_zona_propia_y_otra_ordena_propia_primero(): void
    {
        $oPersona = $this->personaSacdStub(501, 'Perez, Juan');

        $personaRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaRepo->expects($this->once())->method('getPersonas')->willReturn([$oPersona]);

        $oZonaSacdPropia = $this->zonaSacdStub(10, propia: true);
        $oZonaSacdOtra = $this->zonaSacdStub(20, propia: false);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->expects($this->once())
            ->method('getZonasSacds')
            ->with(['id_nom' => 501])
            ->willReturn([$oZonaSacdOtra, $oZonaSacdPropia]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('findById')->willReturnMap([
            [10, $this->zonaStub('Centro', 1)],
            [20, $this->zonaStub('Otra', 5)],
        ]);

        $lista = new ZonaSacdListaTot($personaRepo, $zonaSacdRepo, $zonaRepo);
        $out = $lista->execute();

        $this->assertArrayHasKey(0, $out['a_valores']);
        $this->assertArrayHasKey(1, $out['a_valores']);
        $this->assertSame('Perez, Juan', $out['a_valores'][0][1]);
        $this->assertSame('Centro', $out['a_valores'][0][2]);
        $this->assertSame(_("si"), $out['a_valores'][0][3]);
        $this->assertSame('Otra', $out['a_valores'][1][2]);
        $this->assertSame(_("no"), $out['a_valores'][1][3]);
    }

    public function test_sacd_sin_zonas_aparece_con_campos_vacios(): void
    {
        $oPersona = $this->personaSacdStub(600, 'Ruiz, Maria');

        $personaRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaRepo->method('getPersonas')->willReturn([$oPersona]);

        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->method('getZonasSacds')->willReturn([]);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->never())->method('findById');

        $lista = new ZonaSacdListaTot($personaRepo, $zonaSacdRepo, $zonaRepo);
        $out = $lista->execute();

        $first = $out['a_valores'][0] ?? null;
        $this->assertNotNull($first);
        $this->assertSame('Ruiz, Maria', $first[1]);
        $this->assertSame('', $first[2]);
        $this->assertSame('', $first[3]);
    }

    private function personaSacdStub(int $id_nom, string $pref): PersonaSacd
    {
        $stub = $this->createStub(PersonaSacd::class);
        $stub->method('getId_nom')->willReturn($id_nom);
        $stub->method('getPrefApellidosNombre')->willReturn($pref);
        return $stub;
    }

    private function zonaSacdStub(int $id_zona, bool $propia): ZonaSacd
    {
        $stub = $this->createStub(ZonaSacd::class);
        $stub->method('getId_zona')->willReturn($id_zona);
        $stub->method('isPropia')->willReturn($propia);
        return $stub;
    }

    private function zonaStub(string $nombre, ?int $orden): Zona
    {
        $stub = $this->createStub(Zona::class);
        $stub->method('getNombre_zona')->willReturn($nombre);
        $stub->method('getOrden')->willReturn($orden);
        return $stub;
    }
}
