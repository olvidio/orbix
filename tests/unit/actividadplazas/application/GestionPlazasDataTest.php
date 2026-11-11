<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadplazas\application\GestionPlazasData;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Cuadro de gestión de plazas: actividades del periodo y plazas concedidas/pedidas por dl del grupo. */
final class GestionPlazasDataTest extends myTest
{
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadRepositoryInterface&MockObject $actividades;
    private ActividadPlazasRepositoryInterface&MockObject $plazas;
    private GestionPlazasData $uc;
    private mixed $sesionAnterior;

    /** @var list<Criteria> */
    private array $criteriosActividad = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv', 'sfsv' => 1];
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->plazas = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $this->uc = new GestionPlazasData($this->delegaciones, $this->actividades, $this->plazas);
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

    private function dl(int $id, string $dl, ?string $grupo = null): Delegacion
    {
        $d = new Delegacion();
        $d->setIdDlVo($id);
        $d->setDl($dl);
        $d->setRegion('H');
        $d->setGrupoEstudiosVo($grupo);

        return $d;
    }

    private function actividad(int $id, string $nom, string $dlOrg, ?int $plazas): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getNom_activ')->willReturn($nom);
        $a->method('getDl_org')->willReturn($dlOrg);
        $a->method('getPlazas')->willReturn($plazas);
        $a->method('getId_ubi')->willReturn(null);

        return $a;
    }

    private function plaza(string $dlTabla, int $n): ActividadPlazas
    {
        $p = $this->createMock(ActividadPlazas::class);
        $p->method('getDl_tabla')->willReturn($dlTabla);
        $p->method('getPlazas')->willReturn((string) $n);

        return $p;
    }

    /** @param array<string, list<ActividadAll>> $porDl actividades devueltas según el dl_org filtrado */
    private function preparar(array $delegacionesGrupo, array $porDl, array $plazasPorActiv = []): void
    {
        $this->delegaciones->method('matching')->willReturnOnConsecutiveCalls([$this->dl(1, 'dlb', 'g1')], $delegacionesGrupo);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c) use ($porDl): array {
            $this->criteriosActividad[] = $c;
            foreach (DescribeCriteria::filtros($c) as $f) {
                if (preg_match('/^dl_org eq (.+)$/', $f, $m)) {
                    return $porDl[$m[1]] ?? [];
                }
            }

            return [];
        });
        $this->plazas->method('matching')->willReturnCallback(function (Criteria $c) use ($plazasPorActiv): array {
            $f = DescribeCriteria::filtros($c);
            preg_match('/id_dl eq (\d+)/', $f[0], $dl);
            preg_match('/id_activ eq (\d+)/', $f[1], $ac);

            return $plazasPorActiv[$ac[1] . '-' . $dl[1]] ?? [];
        });
    }

    private function entrada(): array
    {
        return ['id_tipo_activ' => '112', 'periodo' => 'otro', 'empiezamin' => '1/1/2027', 'empiezamax' => '31/12/2027', 'year' => '2027'];
    }

    public function test_si_mi_dl_no_existe_devuelve_el_cuadro_vacio_conservando_los_parametros(): void
    {
        $this->delegaciones->method('matching')->willReturn([]);

        $r = $this->uc->execute($this->entrada());

        $this->assertSame([[], [], [], '112', '2027'], [$r['a_cabeceras'], $r['a_valores'], $r['a_grupo'], $r['id_tipo_activ'], $r['year']]);
    }

    public function test_el_cuadro_incluye_una_columna_c_y_p_por_cada_dl_del_grupo_y_las_actividades_por_dl_org(): void
    {
        $this->preparar(
            [$this->dl(1, 'dlb', 'g1'), $this->dl(2, 'dlx', 'g1')],
            ['dlb' => [$this->actividad(100, 'Ca propia', 'dlb', 30)], 'dlx' => [$this->actividad(200, 'Ca ajena', 'dlx', 20)]],
            ['100-1' => [$this->plaza('dlb', 12)], '100-2' => [$this->plaza('dlx', 5)], '200-1' => [$this->plaza('dlx', 4)]],
        );

        $r = $this->uc->execute($this->entrada());

        $this->assertSame(['dlb' => 1, 'dlx' => 2], $r['a_grupo']);
        $this->assertSame(
            ['id_activ', 'actividad', 'org', 'total', 'dlb-c', 'dlb-p', 'dlx-c', 'dlx-p'],
            array_map(static fn (array $c): string => $c['name'], $r['a_cabeceras']),
        );
        $this->assertCount(2, $this->criteriosActividad);
        $filtros = DescribeCriteria::filtros($this->criteriosActividad[0]);
        $this->assertSame(['dl_org eq dlb', 'id_tipo_activ matches ^112'], array_slice($filtros, 0, 2));
        $this->assertSame('f_ini between [2027-01-01,2027-12-31]', $filtros[3]);
    }

    public function test_en_mi_actividad_las_concedidas_son_editables_y_el_total_tambien_y_la_fila_se_marca(): void
    {
        $this->preparar(
            [$this->dl(1, 'dlb', 'g1'), $this->dl(2, 'dlx', 'g1')],
            ['dlb' => [$this->actividad(100, 'Ca propia', 'dlb', 30)]],
            ['100-1' => [$this->plaza('dlb', 12)], '100-2' => [$this->plaza('dlb', 5), $this->plaza('dlx', 3)]],
        );

        $fila = $this->uc->execute($this->entrada())['a_valores'][1];

        $this->assertSame(['tono2', 100, 'dlb'], [$fila['clase'], $fila['id'], $fila['dlorg']]);
        $this->assertSame(['editable' => 'true', 'valor' => 30], $fila['tot']);
        $this->assertSame(['editable' => 'true', 'valor' => '12'], $fila['dlb-c']);
        $this->assertSame(['editable' => 'false', 'valor' => '-'], $fila['dlb-p']);
        $this->assertSame(['editable' => 'true', 'valor' => '5'], $fila['dlx-c'], 'concedida = fila cuyo dl_tabla es la dl organizadora; el resto se toma como pedida');
        $this->assertSame(['id_activ' => 100], HashB::open($fila['ctx_update'], 'gestion_plazas_update'));
    }

    public function test_en_actividad_ajena_mi_dl_edita_solo_las_pedidas_y_el_total_no_es_editable(): void
    {
        $this->preparar(
            [$this->dl(1, 'dlb', 'g1'), $this->dl(2, 'dlx', 'g1')],
            ['dlx' => [$this->actividad(200, 'Ca ajena', 'dlx', 20)]],
            ['200-1' => [$this->plaza('dlx', 4)], '200-2' => [$this->plaza('dlx', 9)]],
        );

        $fila = $this->uc->execute($this->entrada())['a_valores'][1];

        $this->assertArrayNotHasKey('clase', $fila);
        $this->assertSame('false', $fila['tot']['editable']);
        $this->assertSame(['editable' => 'false', 'valor' => '4'], $fila['dlb-c']);
        $this->assertSame(['editable' => 'true', 'valor' => '-'], $fila['dlb-p']);
        $this->assertSame(['editable' => 'false', 'valor' => '9'], $fila['dlx-c']);
        $this->assertSame(['editable' => 'false', 'valor' => '-'], $fila['dlx-p']);
    }

    public function test_una_actividad_sin_plazas_totales_las_muestra_como_interrogante(): void
    {
        $this->preparar([$this->dl(1, 'dlb', 'g1')], ['dlb' => [$this->actividad(100, 'A', 'dlb', null)]]);

        $this->assertSame('?', $this->uc->execute($this->entrada())['a_valores'][1]['tot']['valor']);
    }

    public function test_sin_grupo_de_estudios_solo_se_muestra_mi_dl(): void
    {
        $this->delegaciones->expects($this->once())->method('matching')->willReturn([$this->dl(1, 'dlb', null)]);
        $this->actividades->method('matching')->willReturn([]);

        $r = $this->uc->execute($this->entrada());

        $this->assertSame(['dlb' => 1], $r['a_grupo']);
    }

    public function test_sin_tipo_se_construye_desde_sv_asistentes_y_actividad(): void
    {
        $this->delegaciones->method('matching')->willReturn([]);

        $r = $this->uc->execute(['sasistentes' => 'n', 'sactividad' => 'ca', 'periodo' => '']);

        $this->assertSame('112', substr($r['id_tipo_activ'], 0, 3));
        $this->assertSame('ca', $r['sactividad']);
        $this->assertFalse($r['extendida']);
        $this->assertSame('curso_ca', $r['periodo']);
    }
}
