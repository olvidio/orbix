<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\dbextern\application\BajaPersonaUseCase;
use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\dbextern\application\DesunirPersonaUseCase;
use src\dbextern\application\RefrescarBduUseCase;
use src\dbextern\application\SincroPersonas;
use src\dbextern\application\support\SincroDBFactory;
use src\dbextern\application\TrasladarPersonaUseCase;
use src\dbextern\application\UnirPersonaUseCase;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\CopiarBDU;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\SincroDB;
use src\dbextern\domain\VincularIdMatch;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\Trasladar;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\RegionCode;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Casos de uso de la sincronización con la BDU (listas): alta, baja, unión, traslado y sincronización masiva. */
final class DbexternCasosDeUsoTest extends myTest
{
    private IdMatchPersonaRepositoryInterface&MockObject $idMatch;
    private SincroDB&MockObject $sincro;
    private SincroDBFactory&MockObject $factory;
    private Trasladar&MockObject $trasladar;

    public function setUp(): void
    {
        parent::setUp();
        $this->idMatch = $this->createMock(IdMatchPersonaRepositoryInterface::class);
        $this->sincro = $this->createMock(SincroDB::class);
        $this->factory = $this->createMock(SincroDBFactory::class);
        $this->factory->method('create')->willReturn($this->sincro);
        $this->trasladar = $this->createMock(Trasladar::class);
    }

    private function bdu(int $identif, string $apenom = 'Gil, Ana'): PersonaBDU&MockObject
    {
        $p = $this->createMock(PersonaBDU::class);
        $p->method('getIdentif')->willReturn($identif);
        $p->method('getApenom')->willReturn($apenom);
        $p->method('getNombre')->willReturn('Ana');
        $p->method('getNx1')->willReturn('');
        $p->method('getApellido1_sinprep')->willReturn('Gil');
        $p->method('getNx2')->willReturn('');
        $p->method('getApellido2_sinprep')->willReturn('Paz');
        $p->method('getFecha_Naci')->willReturn('15/3/1980');
        $p->method('getLugar_Naci')->willReturn('Madrid');
        $p->method('getDl')->willReturn('dlb');

        return $p;
    }

    private function match(int $listas, ?int $orbix): IdMatchPersona
    {
        $m = $this->createMock(IdMatchPersona::class);
        $m->method('getId_listas')->willReturn($listas);
        $m->method('getId_orbix')->willReturn($orbix);

        return $m;
    }

    // ---- unión, desunión y refresco ----------------------------------------------------------------------

    public function test_unir_delega_en_el_vinculador_y_devuelve_su_error(): void
    {
        $v = $this->createMock(VincularIdMatch::class);
        $v->expects($this->once())->method('vincular')->with(990001, 10011, 'n')->willReturn('ya unida');

        $this->assertSame('ya unida', (new UnirPersonaUseCase($v))(990001, 10011, 'n'));
    }

    public function test_desunir_elimina_el_vinculo_o_explica_por_que_no(): void
    {
        $uc = new DesunirPersonaUseCase($this->idMatch);
        $existente = $this->match(990001, 10011);
        $this->idMatch->method('findById')->willReturnMap([[990001, $existente], [990002, null], [990003, $existente]]);
        $this->idMatch->method('Eliminar')->willReturnOnConsecutiveCalls(true, false);
        $this->idMatch->method('getErrorTxt')->willReturn('fk');

        $this->assertSame('', $uc(990001, 'n'));
        $this->assertNotSame('', $uc(990002, 'n'), 'no existe');
        $this->assertStringContainsString('fk', $uc(990003, 'n'));
    }

    public function test_refrescar_recrea_la_tabla_temporal_de_la_bdu(): void
    {
        $copiar = $this->createMock(CopiarBDU::class);
        $copiar->expects($this->once())->method('crearTablaTmp')->willReturn(true);

        (new RefrescarBduUseCase($copiar))();
    }

    // ---- baja y traslados ----------------------------------------------------------------------

    public function test_la_baja_traslada_a_la_dl_indicada_con_situacion_b(): void
    {
        $this->trasladar->expects($this->once())->method('setId_nom')->with(10011);
        $this->trasladar->expects($this->once())->method('setReg_dl_dst')->with($this->matchesRegularExpression('/^H-dlx[vf]$/'));
        $situacion = null;
        $this->trasladar->method('setSituacionVo')->willReturnCallback(function ($s) use (&$situacion): void {
            $situacion = $s->value();
        });
        $this->trasladar->method('cambiarFichaPersona')->willReturn(true);

        $this->assertSame('', (new BajaPersonaUseCase($this->trasladar))(10011, 'n', 'dlx'));
        $this->assertSame('B', $situacion);
    }

    public function test_si_no_se_cambia_la_ficha_la_baja_avisa(): void
    {
        $this->trasladar->method('cambiarFichaPersona')->willReturn(false);

        $this->assertNotSame('', (new BajaPersonaUseCase($this->trasladar))(10011, 'n', 'dlx'));
    }

    private function trasladarUc(array $delegaciones = []): TrasladarPersonaUseCase
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use ($delegaciones): array {
            return $delegaciones;
        });

        return new TrasladarPersonaUseCase($repo, $this->trasladar);
    }

    public function test_trasladar_usa_el_esquema_activo_como_origen(): void
    {
        $this->trasladar->method('getEsquemas')->willReturn([
            ['schemaname' => 'H-dlbv', 'situacion' => 'L'],
            ['schemaname' => 'H-dlpv', 'situacion' => 'A'],
        ]);
        $this->trasladar->expects($this->once())->method('setReg_dl_org')->with('H-dlpv');
        $this->trasladar->method('trasladar')->willReturn(['success' => true]);

        $this->assertSame(['success' => true], $this->trasladarUc()->trasladar(10011, 'n', 'dlp'));
    }

    public function test_sin_esquema_activo_el_origen_es_el_propio(): void
    {
        $this->trasladar->method('getEsquemas')->willReturn([['schemaname' => 'H-dlbv', 'situacion' => 'L']]);
        $origen = null;
        $this->trasladar->method('setReg_dl_org')->willReturnCallback(function (string $o) use (&$origen): void {
            $origen = $o;
        });
        $this->trasladar->method('trasladar')->willReturn(['success' => true]);

        $this->trasladarUc()->trasladar(10011, 'n', 'dlp');

        $this->assertSame(\src\shared\config\ConfigGlobal::mi_region_dl(), $origen);
    }

    public function test_trasladar_a_una_dl_inexistente_no_traslada(): void
    {
        $this->trasladar->expects($this->never())->method('trasladar');

        $r = $this->trasladarUc([])->trasladarA(10011, 'n', 'dlx');

        $this->assertFalse($r['success']);
    }

    public function test_trasladar_a_otra_region_se_manda_al_dossier_de_traslados(): void
    {
        $d = $this->createMock(Delegacion::class);
        $d->method('getRegionVo')->willReturn(new RegionCode('Otra'));
        $this->trasladar->expects($this->never())->method('trasladar');

        $r = $this->trasladarUc([$d])->trasladarA(10011, 'n', 'dlx');

        $this->assertFalse($r['success']);
        $this->assertStringContainsString("\n", $r['mensaje']);
    }

    // ---- sincronización masiva ----------------------------------------------------------------------

    public function test_crear_todos_solo_crea_las_no_unidas_y_acumula_errores(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001), $this->bdu(100002), $this->bdu(100003)]);
        $this->idMatch->method('matching')->willReturnCallback(function (Criteria $c): array {
            return in_array('id_listas eq 100001', DescribeCriteria::filtros($c), true) ? [$this->match(100001, 10011)] : [];
        });
        $crear = $this->createMock(CrearPersonaDesdeListasUseCase::class);
        $crear->expects($this->exactly(2))->method('__invoke')->willReturnOnConsecutiveCalls('', 'fallo');
        $this->sincro->expects($this->once())->method('setTipo_persona')->with('n');
        $this->sincro->expects($this->once())->method('setRegion')->with('H');
        $this->sincro->expects($this->once())->method('setDlListas')->with('dlb');

        $r = (new CrearTodosDesdeListasUseCase($this->idMatch, $crear, $this->factory))('H', 'dlb', 'n');

        $this->assertSame(['count' => 2, 'errors' => ['fallo']], $r);
    }

    private function sincronizar(): SincroPersonas
    {
        $centros = $this->createMock(CentroDlRepositoryInterface::class);
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn(1003);
        $c->method('getNombre_ubi')->willReturn('Ctr A');
        $centros->method('matching')->willReturn([$c]);

        return new SincroPersonas($this->idMatch, $centros, $this->factory);
    }

    public function test_sincronizar_pasa_los_centros_y_cuenta_las_unidas(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001), $this->bdu(100002), $this->bdu(100003)]);
        $this->idMatch->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            if (in_array('id_listas eq 100001', $f, true)) {
                return [$this->match(100001, 10011)];
            }
            if (in_array('id_listas eq 100002', $f, true)) {
                return [$this->match(100002, null)];
            }

            return [];
        });
        $this->sincro->expects($this->once())->method('setCentros')->with(['Ctr A' => 1003]);
        $this->sincro->expects($this->once())->method('syncro')->with($this->anything(), 10011)->willReturn('');

        $this->assertSame(['count' => 2, 'msg' => ''], ($this->sincronizar())('H', 'dlb', 'n'));
    }

    public function test_sincronizar_acumula_mensajes_y_para_ante_un_aborto(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001), $this->bdu(100002), $this->bdu(100003)]);
        $this->idMatch->method('matching')->willReturnCallback(fn (Criteria $c): array => [$this->match(100001, 10011)]);
        $this->sincro->expects($this->exactly(2))->method('syncro')->willReturnOnConsecutiveCalls('aviso uno', ['error' => 'grave', 'abort' => true]);

        $r = ($this->sincronizar())('H', 'dlb', 'n');

        $this->assertSame("aviso uno\ngrave", $r['msg']);
        $this->assertSame(2, $r['count'], 'la tercera ficha no llega a procesarse');
    }

    public function test_una_excepcion_al_sincronizar_interrumpe_con_un_mensaje_claro(): void
    {
        $this->sincro->method('getPersonasBDU')->willReturn([$this->bdu(100001, 'Gil, Ana')]);
        $this->idMatch->method('matching')->willReturn([$this->match(100001, 10011)]);
        $this->sincro->method('syncro')->willThrowException(new \RuntimeException('sin conexión'));

        $r = ($this->sincronizar())('H', 'dlb', 'n');

        $this->assertStringContainsString('Gil, Ana', $r['msg']);
        $this->assertStringContainsString('sin conexión', $r['msg']);
        $this->assertStringContainsString('100001', $r['msg']);
    }

    // ---- crear persona desde listas ----------------------------------------------------------------------

    private PersonaBDURepositoryInterface&MockObject $bduRepo;
    private PersonaNRepositoryInterface&MockObject $repoN;

    private function crearPersona(): CrearPersonaDesdeListasUseCase
    {
        $this->bduRepo = $this->createMock(PersonaBDURepositoryInterface::class);
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $resolver = new PersonaRepositoryResolver(
            $this->repoN,
            $this->createMock(PersonaAgdRepositoryInterface::class),
            $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->createMock(PersonaExRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
        );

        return new CrearPersonaDesdeListasUseCase($this->bduRepo, $this->idMatch, $resolver, $this->trasladar, $this->factory);
    }

    public function test_crear_persona_inexistente_en_la_bdu_avisa(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn(null);

        $this->assertNotSame('', $uc(100001, 'n'));
    }

    public function test_crear_persona_con_dl_sin_traduccion_o_tipo_desconocido_avisa(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001));
        $this->sincro->method('dlListas2Orbix')->willReturnOnConsecutiveCalls(false, 'dlb');
        $this->repoN->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $uc(100001, 'n'), 'dl sin traducir');
        $this->assertStringContainsString('9', $uc(900001, 'n'), 'tipo de persona 9 no existe');
    }

    public function test_crear_persona_numeraria_guarda_la_ficha_y_el_vinculo(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001));
        $this->sincro->method('dlListas2Orbix')->willReturn('dlb');
        $this->idMatch->method('matching')->willReturn([]);
        $this->repoN->method('getNewId')->willReturn(7);
        $this->repoN->method('getNewIdNom')->with(7)->willReturn(10077);
        $guardada = null;
        $this->repoN->expects($this->once())->method('Guardar')->willReturnCallback(function (PersonaN $p) use (&$guardada): bool {
            $guardada = $p;

            return true;
        });
        $vinculo = null;
        $this->idMatch->expects($this->once())->method('Guardar')->willReturnCallback(function (IdMatchPersona $m) use (&$vinculo): bool {
            $vinculo = $m;

            return true;
        });

        $this->assertSame('', $uc(100001, 'n'));

        $this->assertSame([10077, 'n', 'A', 'Ana', 'Gil', 'Paz', 'Madrid', 'dlb'], [
            $guardada->getId_nom(), $guardada->getId_tabla(), $guardada->getSituacion(), $guardada->getNom(),
            $guardada->getApellido1(), $guardada->getApellido2(), $guardada->getLugar_nacimiento(), $guardada->getDl(),
        ]);
        $this->assertSame('15/3/1980', $guardada->getF_nacimiento()?->getFromLocal());
        $this->assertSame([100001, 10077, 'n'], [$vinculo->getId_listas(), $vinculo->getId_orbix(), $vinculo->getId_tabla()]);
    }

    public function test_si_falla_el_guardado_de_la_ficha_no_se_crea_el_vinculo(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001));
        $this->sincro->method('dlListas2Orbix')->willReturn('dlb');
        $this->idMatch->method('matching')->willReturn([]);
        $this->repoN->method('Guardar')->willReturn(false);
        $this->idMatch->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $uc(100001, 'n'));
    }

    public function test_si_falla_el_guardado_del_vinculo_se_devuelve_su_error(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001));
        $this->sincro->method('dlListas2Orbix')->willReturn('dlb');
        $this->idMatch->method('matching')->willReturn([]);
        $this->repoN->method('Guardar')->willReturn(true);
        $this->idMatch->method('Guardar')->willReturn(false);
        $this->idMatch->method('getErrorTxt')->willReturn('duplicado');

        $this->assertStringContainsString('duplicado', $uc(100001, 'n'));
    }

    public function test_si_ya_hay_un_vinculo_previo_se_consultan_sus_esquemas(): void
    {
        $uc = $this->crearPersona();
        $this->bduRepo->method('findById')->willReturn($this->bdu(100001));
        $this->sincro->method('dlListas2Orbix')->willReturn('dlb');
        $this->idMatch->method('matching')->willReturn([$this->match(100001, 10011)]);
        $this->trasladar->expects($this->once())->method('getEsquemas')->with(10011, 'n')->willReturn([]);
        $this->repoN->method('Guardar')->willReturn(true);
        $this->idMatch->method('Guardar')->willReturn(true);

        $this->assertSame('', $uc(100001, 'n'));
    }
}
