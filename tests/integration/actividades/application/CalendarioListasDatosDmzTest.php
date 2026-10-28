<?php

declare(strict_types=1);

namespace Tests\integration\actividades\application;

use src\actividades\application\CalendarioListasDatos;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\permisos\domain\PermisosActividadesTrue;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\CentroNombreResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use Tests\myTest;

/**
 * Centros encargados de `lista_cdc` (ver_ctr=si): en DMZ no existe u_centros_dl,
 * se leen de comun (ellos/ellas). Repositorios simulados; usa el contenedor DI
 * porque el caso de uso crea TiposActividades.
 */
final class CalendarioListasDatosDmzTest extends myTest
{
    private bool $previousDmz;

    public function setUp(): void
    {
        parent::setUp();
        $this->previousDmz = ConfigGlobal::$dmz;
    }

    public function tearDown(): void
    {
        ConfigGlobal::$dmz = $this->previousDmz;
        parent::tearDown();
    }

    /**
     * Ejecuta `lista_cdc` + `ver_ctr=si` con una casa que tiene una actividad con un único
     * centro encargado (`$idCtr`), y devuelve el HTML resultante.
     */
    private function ejecutarListaCdcConCentroEncargado(
        int $idCtr,
        CentroDlRepositoryInterface $centroDl,
        CentroEllosRepositoryInterface $centroEllos,
        CentroEllasRepositoryInterface $centroEllas,
    ): string {
        $casa = $this->createMock(Casa::class);
        $casa->method('getId_ubi')->willReturn(1001);
        $casa->method('getNombre_ubi')->willReturn('Casa prueba');
        $casaDl = $this->createMock(CasaDlRepositoryInterface::class);
        $casaDl->method('getCasas')->willReturn([$casa]);

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getId_activ')->willReturn(5001);
        $actividad->method('getId_tipo_activ')->willReturn(112021);
        $actividad->method('getNom_activ')->willReturn('Actividad prueba');
        $actividad->method('getId_ubi')->willReturn(1001);
        $actividad->method('getLugar_esp')->willReturn('');
        $actividades = $this->createMock(ActividadRepositoryInterface::class);
        $actividades->method('matching')->willReturn([$actividad]);

        $encargado = $this->createMock(CentroEncargado::class);
        $encargado->method('getId_ubi')->willReturn($idCtr);
        $encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $encargados->method('getCentrosEncargados')->willReturn([$encargado]);

        $_SESSION['oPermActividades'] = new PermisosActividadesTrue(1);

        $out = (new CalendarioListasDatos(
            $casaDl,
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $actividades,
            $encargados,
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            new CentroNombreResolver($centroDl, $centroEllos, $centroEllas),
            $this->createMock(AsistenteActividadService::class),
        ))->ejecutar([
            'que' => 'lista_cdc',
            'ver_ctr' => 'si',
            'periodo' => 'tot_any',
            'year' => '2026',
        ]);

        return $out['html'];
    }

    public function test_dmz_centros_encargados_sv_se_leen_de_centro_ellos_y_no_de_u_centros_dl(): void
    {
        ConfigGlobal::$dmz = true;

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->expects($this->never())->method('findById');
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $centro = $this->createMock(CentroEllos::class);
        $centro->method('getNombre_ubi')->willReturn('Centro sv ellos');
        $ellos->expects($this->once())->method('findById')->with(10018154)->willReturn($centro);
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');

        $html = $this->ejecutarListaCdcConCentroEncargado(10018154, $centroDl, $ellos, $ellas);

        $this->assertStringContainsString('Centro sv ellos', $html);
    }

    public function test_dmz_centros_encargados_sf_se_leen_de_centro_ellas(): void
    {
        ConfigGlobal::$dmz = true;

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->expects($this->never())->method('findById');
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $centro = $this->createMock(CentroEllas::class);
        $centro->method('getNombre_ubi')->willReturn('Centro sf ellas');
        $ellas->expects($this->once())->method('findById')->with(20018154)->willReturn($centro);

        $html = $this->ejecutarListaCdcConCentroEncargado(20018154, $centroDl, $ellos, $ellas);

        $this->assertStringContainsString('Centro sf ellas', $html);
    }

    public function test_sin_dmz_centros_encargados_se_leen_de_centro_dl(): void
    {
        ConfigGlobal::$dmz = false;

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centro = $this->createMock(CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Centro dl');
        $centroDl->expects($this->once())->method('findById')->with(10018154)->willReturn($centro);
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->expects($this->never())->method('findById');
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->expects($this->never())->method('findById');

        $html = $this->ejecutarListaCdcConCentroEncargado(10018154, $centroDl, $ellos, $ellas);

        $this->assertStringContainsString('Centro dl', $html);
    }
}
