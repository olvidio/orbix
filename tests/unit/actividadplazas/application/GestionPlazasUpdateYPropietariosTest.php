<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadplazas\application\GestionPlazasUpdate;
use src\actividadplazas\application\PlazasCalendarioMensaje;
use src\actividadplazas\application\PlazasDlEdicion;
use src\actividadplazas\application\PosiblesPropietariosData;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionId;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Edición de celdas de la tabla de gestión de plazas y desplegable de propietarios. */
final class GestionPlazasUpdateYPropietariosTest extends myTest
{
    private ActividadDlRepositoryInterface&MockObject $actividades;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadPlazasDlRepositoryInterface&MockObject $plazasDl;
    private ActividadPlazasRepositoryInterface&MockObject $plazasCal;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv', 'sfsv' => 1];
        $this->actividades = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->plazasDl = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $this->plazasCal = $this->createMock(ActividadPlazasRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    private function update(): GestionPlazasUpdate
    {
        return new GestionPlazasUpdate(
            $this->actividades,
            $this->delegaciones,
            $this->plazasDl,
            new PlazasDlEdicion($this->plazasDl, $this->plazasCal),
        );
    }

    /** @return array<string, string> */
    private function celda(array $fila, string $col): array
    {
        return ['data' => json_encode($fila), 'colName' => json_encode($col)];
    }

    private function delegacion(int $id): Delegacion
    {
        $d = new Delegacion();
        $d->setIdDlVo(new DelegacionId($id));

        return $d;
    }

    /** @dataProvider entradasIgnoradasProvider */
    public function test_entradas_incompletas_o_mal_formadas_se_ignoran(array $input): void
    {
        $this->actividades->expects($this->never())->method('Guardar');
        $this->plazasDl->expects($this->never())->method('Guardar');

        $this->assertSame('', $this->update()->execute($input));
    }

    public static function entradasIgnoradasProvider(): iterable
    {
        yield 'vacía' => [[]];
        yield 'sin columna' => [['data' => '{"id":1}']];
        yield 'datos no objeto' => [['data' => '[1]', 'colName' => '"tot"']];
        yield 'columna no texto' => [['data' => '{"id":1}', 'colName' => '5']];
        yield 'sin id de actividad' => [['data' => '{"id":0,"tot":3}', 'colName' => '"tot"']];
    }

    public function test_las_plazas_totales_de_una_actividad_propia_se_guardan_en_la_actividad(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->expects($this->once())->method('setPlazas')->with(25);
        $this->actividades->method('findById')->with(77)->willReturn($a);
        $this->actividades->expects($this->once())->method('Guardar')->with($a)->willReturn(true);

        $this->assertSame('', $this->update()->execute($this->celda(['id' => 77, 'dlorg' => 'dlb', 'tot' => 25], 'tot')));
    }

    public function test_totales_de_actividad_inexistente_o_con_fallo_de_guardado_devuelven_error(): void
    {
        $this->actividades->method('findById')->willReturnOnConsecutiveCalls(null, $this->createMock(ActividadAll::class));
        $this->actividades->method('Guardar')->willReturn(false);
        $this->actividades->method('getErrorTxt')->willReturn('bloqueo');
        $uc = $this->update();
        $in = $this->celda(['id' => 77, 'dlorg' => 'dlb', 'tot' => 25], 'tot');

        $this->assertNotSame('', $uc->execute($in));
        $this->assertStringContainsString('bloqueo', $uc->execute($in));
    }

    public function test_las_plazas_de_una_dl_ajena_se_guardan_en_el_registro_local_de_la_dl_de_la_columna(): void
    {
        $this->actividades->expects($this->never())->method('Guardar');
        $criterio = null;
        $this->delegaciones->method('matching')->willReturnCallback(function ($c) use (&$criterio): array {
            $criterio = $c;

            return [$this->delegacion(9)];
        });
        $local = new ActividadPlazas();
        $local->setPlazasVo(4);
        $this->plazasDl->method('matching')->willReturn([$local]);
        $this->plazasDl->expects($this->once())->method('Guardar')->with($local)->willReturn(true);

        $r = $this->update()->execute($this->celda(['id' => 77, 'dlorg' => 'dlx', 'dlx-c' => 6], 'dlx-c'));

        $this->assertSame('', $r);
        $this->assertSame(['dl eq dlx'], DescribeCriteria::filtros($criterio));
        $this->assertSame(6, $local->getPlazasVo()?->value());
    }

    public function test_en_sf_se_quita_la_f_final_del_codigo_de_la_dl_de_la_columna(): void
    {
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbf', 'sfsv' => 2];
        $criterio = null;
        $this->delegaciones->method('matching')->willReturnCallback(function ($c) use (&$criterio): array {
            $criterio = $c;

            return [];
        });
        $this->plazasDl->method('matching')->willReturn([]);
        $this->plazasCal->method('matching')->willReturn([]);
        $this->plazasDl->method('Guardar')->willReturn(true);

        $this->update()->execute($this->celda(['id' => 77, 'dlorg' => 'dlx', 'dlxf-p' => 1], 'dlxf-p'));

        $this->assertSame(['dl eq dlx'], DescribeCriteria::filtros($criterio));
    }

    public function test_si_no_se_puede_crear_el_registro_local_avisa_de_la_falta_de_calendario(): void
    {
        $this->delegaciones->method('matching')->willReturn([$this->delegacion(9)]);
        $this->plazasDl->method('matching')->willReturn([]);
        $this->plazasCal->method('matching')->willReturn([]);
        $this->plazasDl->method('Guardar')->willReturn(false);

        $r = $this->update()->execute($this->celda(['id' => 77, 'dlorg' => 'dlx', 'dlx-c' => 6], 'dlx-c'));

        $this->assertSame(PlazasCalendarioMensaje::faltaRegistro(), $r);
    }

    public function test_un_fallo_al_guardar_las_plazas_locales_devuelve_el_error_del_repositorio(): void
    {
        $this->delegaciones->method('matching')->willReturn([$this->delegacion(9)]);
        $local = new ActividadPlazas();
        $local->setPlazasVo(4);
        $this->plazasDl->method('matching')->willReturn([$local]);
        $this->plazasDl->method('Guardar')->willReturn(false);
        $this->plazasDl->method('getErrorTxt')->willReturn('constraint');

        $r = $this->update()->execute($this->celda(['id' => 77, 'dlorg' => 'dlx', 'dlx-c' => 6], 'dlx-c'));

        $this->assertStringContainsString('constraint', $r);
    }

    // ---- PosiblesPropietariosData ----------------------------------------------------------------

    private ResumenPlazasService&MockObject $resumen;
    private PersonaFinderService&MockObject $finder;

    private function propietarios(): PosiblesPropietariosData
    {
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->finder = $this->createMock(PersonaFinderService::class);

        return new PosiblesPropietariosData($this->resumen, $this->finder);
    }

    public function test_faltan_parametros_o_persona(): void
    {
        $uc = $this->propietarios();
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);

        $this->assertArrayHasKey('error', $uc->execute(['id_nom' => 5]));
        $this->assertArrayHasKey('error', $uc->execute(['id_activ' => 5]));
        $this->assertStringContainsString('5', $uc->execute(['id_nom' => 5, 'id_activ' => 3])['error']);
    }

    public function test_una_persona_de_la_dl_ofrece_las_opciones_en_orden_con_su_dl_como_seleccion(): void
    {
        $uc = $this->propietarios();
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn($this->createMock(PersonaDl::class));
        $this->resumen->expects($this->once())->method('setId_activ')->with(3);
        $this->resumen->expects($this->once())->method('getPosiblesPropietariosOpciones')->with(false)
            ->willReturn(['dlx>dlb' => 'dlx (0 de 2)', 'dlb>dlb' => 'dlb (1 de 5)']);

        $r = $uc->execute(['id_nom' => '10011', 'id_activ' => '3']);

        $this->assertSame([['dlx>dlb', 'dlx (0 de 2)'], ['dlb>dlb', 'dlb (1 de 5)']], $r['opciones']);
        $this->assertSame(['propietario', 'dlb>', true, ''], [$r['id'], $r['selected'], $r['blanco'], $r['val_blanco']]);
    }

    public function test_una_persona_de_paso_con_dl_la_pasa_al_servicio_y_la_incluye_en_la_seleccion(): void
    {
        $uc = $this->propietarios();
        $p = new PersonaEx();
        $p->setDl('dlz');
        $this->finder->method('findPersonaEnGlobalODePaso')->with(-7)->willReturn($p);
        $this->resumen->expects($this->once())->method('getPosiblesPropietariosOpciones')->with('dlz')->willReturn(['xxx' => 'no disponibles']);

        $r = $uc->execute(['id_nom' => '-7', 'id_activ' => '3']);

        $this->assertSame('dlb>dlz', $r['selected']);
    }
}
