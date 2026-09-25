<?php

declare(strict_types=1);

namespace Tests\unit\ubiscamas\application;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\shared\security\HashB;
use src\ubiscamas\application\HabitacionesCamaLista;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\TipoLavabo;

final class HabitacionesCamaListaTest extends TestCase
{
    public function test_actividad_inexistente(): void
    {
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(5)->willReturn(null);

        $out = (new HabitacionesCamaLista(
            $actRepo,
            $this->createMock(AsistenteActividadService::class),
            $this->createMock(HabitacionDlRepositoryInterface::class),
            $this->createMock(CamaDlRepositoryInterface::class),
        ))(5);

        $this->assertSame(['error' => 'Actividad not found'], $out);
    }

    public function test_sin_id_ubi(): void
    {
        $act = $this->createMock(ActividadAll::class);
        $act->method('getId_ubi')->willReturn(0);
        $act->method('getDesc_activ')->willReturn('x');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->willReturn($act);

        $out = (new HabitacionesCamaLista(
            $actRepo,
            $this->createMock(AsistenteActividadService::class),
            $this->createMock(HabitacionDlRepositoryInterface::class),
            $this->createMock(CamaDlRepositoryInterface::class),
        ))(1);

        $this->assertSame(['error' => 'No Ubi assigned to activity'], $out);
    }

    public function test_lista_una_cama_sin_asistentes(): void
    {
        $hid = Uuid::uuid4()->toString();
        $cid = Uuid::uuid4()->toString();

        $act = $this->createMock(ActividadAll::class);
        $act->method('getId_ubi')->willReturn(40);
        $act->method('getDesc_activ')->willReturn('normal');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(9)->willReturn($act);

        $hab = $this->createMock(Habitacion::class);
        $hab->method('getIdHabitacionVo')->willReturn(new HabitacionId($hid));
        $hab->method('toArrayForDatabase')->willReturn([]);
        $hab->method('getNombre')->willReturn('Hab 1');
        $hab->method('getPlanta')->willReturn('B');
        $hab->method('isAdaptada')->willReturn(false);
        $hab->method('getTipoLavaboVo')->willReturn(new TipoLavabo(2));
        $hab->method('isSillon')->willReturn(false);
        $hab->method('isDespacho')->willReturn(false);
        $hab->method('getObservacionesVo')->willReturn(null);

        $cama = $this->createMock(Cama::class);
        $cama->method('getIdHabitacionVo')->willReturn(new HabitacionId($hid));
        $cama->method('getIdCamaVo')->willReturn(new CamaId($cid));
        $cama->method('toArrayForDatabase')->willReturn([]);
        $cama->method('getDescripcion')->willReturn('Cama A');
        $cama->method('isLarga')->willReturn(false);
        $cama->method('isVip')->willReturn(false);

        $habRepo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $habRepo->method('getHabitaciones')->willReturn([$hab]);

        $camaRepo = $this->createMock(CamaDlRepositoryInterface::class);
        $camaRepo->method('getCamas')->willReturn([$cama]);

        $asistenteSvc = $this->createMock(AsistenteActividadService::class);
        $asistenteSvc->method('getAsistentesDeActividad')->with(9)->willReturn([]);

        $out = (new HabitacionesCamaLista(
            $actRepo,
            $asistenteSvc,
            $habRepo,
            $camaRepo,
        ))(9);

        $this->assertTrue($out['success']);
        $this->assertSame(9, $out['id_activ']);
        $this->assertSame(40, $out['id_ubi']);
        $this->assertFalse($out['solo_vip']);
        $this->assertArrayHasKey($hid, $out['habitaciones_con_camas']);
        $this->assertCount(1, $out['a_valores']);
        $this->assertSame("$hid#$cid", $out['a_valores'][1]['sel']);
        $this->assertSame(_('completo'), $out['a_valores'][1][4]);
    }

    public function test_asistente_sin_cama_recibe_contexto_firmado_sin_id_nom_plano(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('habitaciones-cama-lista-token-test');

        $act = $this->createMock(ActividadAll::class);
        $act->method('getId_ubi')->willReturn(40);
        $act->method('getDesc_activ')->willReturn('normal');
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(9)->willReturn($act);

        $asistente = $this->createMock(Asistente::class);
        $asistente->method('getCamaVo')->willReturn(null);
        $asistente->method('getId_nom')->willReturn(7);
        $asistenteSvc = $this->createMock(AsistenteActividadService::class);
        $asistenteSvc->method('getAsistentesDeActividad')->with(9)->willReturn(['Doe, Jane' => $asistente]);

        $habRepo = $this->createMock(HabitacionDlRepositoryInterface::class);
        $habRepo->method('getHabitaciones')->willReturn([]);

        $out = (new HabitacionesCamaLista(
            $actRepo,
            $asistenteSvc,
            $habRepo,
            $this->createMock(CamaDlRepositoryInterface::class),
        ))(9);

        $asistenteOut = $out['asistentes_sin_cama'][0];
        $this->assertArrayNotHasKey('id_nom', $asistenteOut);
        $this->assertSame(
            ['id_activ' => 9, 'id_nom' => 7],
            HashB::open($asistenteOut['ctx_update_cama'], 'update_cama_asistente')
        );
    }

    public function test_asistente_de_paso_conserva_id_nom_negativo_en_el_contexto(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('habitaciones-cama-lista-de-paso-test');

        $act = $this->createMock(ActividadAll::class);
        $act->method('getId_ubi')->willReturn(40);
        $act->method('getDesc_activ')->willReturn('normal');
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(9)->willReturn($act);

        $asistente = $this->createMock(Asistente::class);
        $asistente->method('getCamaVo')->willReturn(null);
        $asistente->method('getId_nom')->willReturn(-42);
        $asistenteSvc = $this->createMock(AsistenteActividadService::class);
        $asistenteSvc->method('getAsistentesDeActividad')->with(9)->willReturn(['Paso, Ana' => $asistente]);

        $out = (new HabitacionesCamaLista(
            $actRepo,
            $asistenteSvc,
            $this->createMock(HabitacionDlRepositoryInterface::class),
            $this->createMock(CamaDlRepositoryInterface::class),
        ))(9);

        $this->assertSame(
            ['id_activ' => 9, 'id_nom' => -42],
            HashB::open($out['asistentes_sin_cama'][0]['ctx_update_cama'], 'update_cama_asistente')
        );
    }
}
