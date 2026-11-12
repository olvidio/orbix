<?php

declare(strict_types=1);

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadessacd\application\SolapesSacdData;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaSacd;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/** Sacd con actividades que se solapan: consultas, filas y clase de cada actividad. */
final class SolapesSacdFilasTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private ActividadDlRepositoryInterface&MockObject $actividadesDl;
    private PersonaSacdRepositoryInterface&MockObject $sacds;
    private CargoOAsistenteInterface&MockObject $solapes;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ActividadProcesoTareaRepositoryInterface&MockObject $procesos;

    public function setUp(): void
    {
        parent::setUp();
        $this->actividadesDl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->sacds = $this->createMock(PersonaSacdRepositoryInterface::class);
        $this->solapes = $this->createMock(CargoOAsistenteInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->procesos = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if ($id === 99) {
                return null;
            }
            $p = new PersonaDl();
            $p->setNom('Sacd');
            $p->setApellido1("Ap$id");
            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function uc(): SolapesSacdData
    {
        $fases = $this->createMock(ActividadFaseRepositoryInterface::class);
        $f = new ActividadFase();
        $f->setDesc_fase('ok sacd');
        $fases->method('findById')->willReturn($f);
        return new SolapesSacdData($fases, $this->actividadesDl, $this->sacds, $this->solapes, $this->actividades, $this->procesos);
    }

    private function actividad(int $id, ?int $ubi, int $status = 2): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ("act $id");
        $a->setId_ubi($ubi);
        $a->setStatus($status);
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        return $a;
    }

    public function test_consulta_actividades_y_sacd_de_mi_dl_y_monta_las_filas(): void
    {
        $lista = [$this->actividad(1, 10200)];
        $this->actividadesDl->method('matching')->willReturnCallback(function ($c) use ($lista): array {
            $this->assertSame(['f_ini between [2027-03-01,2027-03-31]', 'status lt 3'], DescribeCriteria::filtros($c));
            return $lista;
        });
        $sacd = new PersonaSacd();
        $this->sacds->method('matching')->willReturnCallback(function ($c) use ($sacd): array {
            $this->assertSame(['id_tabla in [n,a]', 'sacd eq true', 'dl eq dlb'], DescribeCriteria::filtros($c));
            $this->assertSame(['apellido1 asc', 'apellido2 asc', 'nom asc'], DescribeCriteria::orden($c));
            return [$sacd];
        });
        $this->solapes->expects($this->once())->method('getSolapes')->with([$sacd], $lista)->willReturn([20 => [1, 2, 3, 4], 99 => [5]]);
        $act = [1 => $this->actividad(1, 10200), 2 => $this->actividad(2, 10200, 1), 3 => $this->actividad(3, 10201), 5 => $this->actividad(5, null)];
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $act[$id] ?? null);
        $this->procesos->method('getSacdAprobado')->willReturnCallback(fn (int $id): ?bool => $id === 1);

        $out = $this->uc()->execute(['periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027']);

        $this->assertSame('ok sacd', $out['texto_fase_ok_sacd']);
        $this->assertSame([20, 99], array_column($out['filas'], 'id_nom'));
        $this->assertStringContainsString('Ap20', $out['filas'][0]['nom_sacd']);
        $this->assertSame('', $out['filas'][1]['nom_sacd'], 'persona no encontrada');
        // 1 aprobada; 2 en proyecto y en la misma casa que la anterior (tachada); 3 otra casa; 4 no existe.
        $this->assertSame([
            ['clase' => 'plaza4', 'nom_activ' => 'act 1'],
            ['clase' => 'wrong-soft tachado', 'nom_activ' => 'act 2'],
            ['clase' => '', 'nom_activ' => 'act 3'],
        ], $out['filas'][0]['actividades']);
    }

    public function test_sin_solapes_no_hay_filas(): void
    {
        $this->actividadesDl->method('matching')->willReturn([]);
        $this->sacds->method('matching')->willReturn([]);
        $this->solapes->method('getSolapes')->willReturn([]);
        $this->assertSame([], $this->uc()->execute([])['filas']);
    }
}
