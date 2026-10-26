<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application\services;

use PHPUnit\Framework\TestCase;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\CentroNombreResolver;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;

final class CentroNombreResolverTest extends TestCase
{
    private bool $previousDmz;
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousDmz = ConfigGlobal::$dmz;
        $this->previousSession = $_SESSION ?? null;
        unset($_SESSION['private']);
    }

    protected function tearDown(): void
    {
        ConfigGlobal::$dmz = $this->previousDmz;
        if ($this->previousSession === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->previousSession;
        }
        parent::tearDown();
    }

    public function test_sin_dmz_usa_centro_dl(): void
    {
        ConfigGlobal::$dmz = false;
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $centro = $this->createMock(CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Centro dl');
        $dl->expects($this->once())->method('findById')->with(10018154)->willReturn($centro);
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');

        $this->assertSame('Centro dl', (new CentroNombreResolver($dl, $ellos, $ellas))->nombre(10018154));
    }

    public function test_dmz_sv_usa_centro_ellos(): void
    {
        ConfigGlobal::$dmz = true;
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $dl->expects($this->never())->method('findById');
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $centro = $this->createMock(CentroEllos::class);
        $centro->method('getNombre_ubi')->willReturn('Centro ellos');
        $ellos->expects($this->once())->method('findById')->with(10018154)->willReturn($centro);
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');

        $this->assertSame('Centro ellos', (new CentroNombreResolver($dl, $ellos, $ellas))->nombre(10018154));
    }

    public function test_dmz_sf_usa_centro_ellas(): void
    {
        ConfigGlobal::$dmz = true;
        $dl = $this->createMock(CentroDlRepositoryInterface::class);
        $dl->expects($this->never())->method('findById');
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $centro = $this->createMock(CentroEllas::class);
        $centro->method('getNombre_ubi')->willReturn('Centro ellas');
        $ellas->expects($this->once())->method('findById')->with(20018154)->willReturn($centro);

        $this->assertSame('Centro ellas', (new CentroNombreResolver($dl, $ellos, $ellas))->nombre(20018154));
    }

    public function test_centro_inexistente_devuelve_cadena_vacia(): void
    {
        ConfigGlobal::$dmz = true;
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->method('findById')->willReturn(null);

        $resolver = new CentroNombreResolver(
            $this->createMock(CentroDlRepositoryInterface::class),
            $ellos,
            $this->createMock(CentroEllasRepositoryInterface::class),
        );

        $this->assertSame('', $resolver->nombre(10099999));
    }
}
