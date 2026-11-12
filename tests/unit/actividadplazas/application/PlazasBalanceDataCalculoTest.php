<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\PlazasBalanceData;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\asistentes\application\services\AsistenteActividadService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Balance de plazas entre mi dl (A) y otra (B): plazas que cada una concede a la
 * otra en sus actividades, libres según las ocupadas, qué celdas se pueden editar,
 * y periodo del curso según el tipo (ca/cv: curso de estudios; crt: curso crt).
 */
final class PlazasBalanceDataCalculoTest extends myTest
{
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadRepositoryInterface&MockObject $actividades;
    private ActividadPlazasRepositoryInterface&MockObject $plazas;
    private AsistenteActividadService&MockObject $ocupadas;
    /** @var list<list<string>> */
    private array $consultasActividades = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->delegaciones->method('matching')->willReturnCallback(function ($c): array {
            $dl = substr(DescribeCriteria::filtros($c)[0], strlen('dl eq '));
            $ids = ['dlb' => 1, 'dlp' => 2];
            if (!isset($ids[$dl])) {
                return [];
            }
            $d = new Delegacion();
            $d->setId_dl($ids[$dl]);
            $d->setDl($dl);
            return [$d];
        });
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->plazas = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $this->ocupadas = $this->createMock(AsistenteActividadService::class);
    }

    private function uc(): PlazasBalanceData
    {
        return new PlazasBalanceData($this->delegaciones, $this->actividades, $this->plazas, $this->ocupadas);
    }

    private function actividad(int $id, string $dl): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ("ca $id");
        $a->setDl_org($dl);
        $a->setF_ini(new DateTimeLocal('2027-07-01'));
        $a->setF_fin(new DateTimeLocal('2027-07-20'));
        return $a;
    }

    private function fila(int $idDl, string $tabla, int $plazas): ActividadPlazas
    {
        $p = new ActividadPlazas();
        $p->setId_dl($idDl);
        $p->setDlTablaVo($tabla);
        $p->setPlazas($plazas);
        return $p;
    }

    private function escenario(): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultasActividades[] = $f;
            return $f[0] === 'dl_org eq dlb' ? [$this->actividad(10, 'dlb')] : [$this->actividad(20, 'dlp')];
        });
        $tabla = [
            '1-10' => [$this->fila(1, 'dlb', 20)],
            // La fila de la tabla de la otra dl no cuenta: solo la de quien organiza.
            '2-10' => [$this->fila(2, 'dlb', 5), $this->fila(2, 'dlp', 99)],
            '2-20' => [$this->fila(2, 'dlp', 30)],
            '1-20' => [$this->fila(1, 'dlp', 4)],
        ];
        $this->plazas->method('matching')->willReturnCallback(function ($c) use ($tabla): array {
            [$dl, $activ] = DescribeCriteria::filtros($c);
            return $tabla[substr($dl, 9) . '-' . substr($activ, 12)] ?? [];
        });
        $this->ocupadas->method('getPlazasOcupadasPorDl')->willReturnCallback(
            fn (int $id, string $dl): int => ['10dlb' => 12, '10dlp' => -1, '20dlp' => 30, '20dlb' => 1][$id . $dl]
        );
    }

    public function test_balance_entre_mi_dl_y_otra(): void
    {
        $this->escenario();
        $out = $this->uc()->execute(['dl' => 'dlp', 'id_tipo_activ' => '112']);

        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame(['dlb', 'dlp', 5, 4], [$out['dlA'], $out['dlB'], $out['concedidasA2B'], $out['concedidasB2A']]);
        $this->assertSame(['id', 'actividad', 'dlorg', 'dlb-c', 'dlb-l', 'dlp-c', 'dlp-l'], array_column($out['a_cabeceras'], 'field'));

        [$mia, $suya] = array_values($out['a_valores']);
        $this->assertSame(10, $mia['id']);
        $this->assertSame('ca 10', $mia['actividad']);
        $this->assertSame('tono1', $mia['clase']);
        $this->assertSame(['editable' => 'true', 'valor' => 20], $mia['dlb-c']);
        $this->assertSame(['editable' => 'false', 'valor' => 8], $mia['dlb-l']);
        $this->assertSame(['editable' => 'true', 'valor' => 5], $mia['dlp-c']);
        $this->assertSame(['editable' => 'false', 'valor' => '-'], $mia['dlp-l'], 'ocupadas negativas: sin dato');
        $this->assertSame(['id_activ' => 10], HashB::open($mia['ctx_update'], 'gestion_plazas_update'));

        $this->assertSame(20, $suya['id']);
        $this->assertSame('tono2', $suya['clase']);
        $this->assertSame(['editable' => 'false', 'valor' => 4], $suya['dlb-c'], 'lo que la otra dl me concede no lo edito yo');
        $this->assertSame(['editable' => 'false', 'valor' => 3], $suya['dlb-l']);
        $this->assertSame(['editable' => 'true', 'valor' => 30], $suya['dlp-c']);
        $this->assertSame(['editable' => 'false', 'valor' => 0], $suya['dlp-l']);
    }

    public function test_consulta_actividades_actuales_del_tipo_en_el_curso_de_estudios(): void
    {
        $this->escenario();
        $this->uc()->execute(['dl' => 'dlp', 'id_tipo_activ' => '112']);
        $any = $_SESSION['oConfig']->any_final_curs('est');
        $ini = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any, 'est')->format('Y-m-d');
        $fin = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('fin', $any, 'est')->format('Y-m-d');
        $this->assertSame(['dl_org eq dlb', 'id_tipo_activ matches ^112', 'status eq 2', "f_ini between [$ini,$fin]"], $this->consultasActividades[0]);
        $this->assertSame('dl_org eq dlp', $this->consultasActividades[1][0]);
    }

    public function test_los_crt_usan_el_curso_crt(): void
    {
        $this->escenario();
        $this->uc()->execute(['dl' => 'dlp', 'id_tipo_activ' => '111']);
        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $ini = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any, 'crt')->format('Y-m-d');
        $this->assertStringStartsWith("f_ini between [$ini,", $this->consultasActividades[0][3]);
    }

    /**
     * Hallazgo: para un tipo que no es ca, cv ni crt (p. ej. cve) el periodo queda
     * vacío y la consulta filtra `f_ini BETWEEN '' AND ''` (error SQL en PostgreSQL).
     */
    public function test_otro_tipo_consulta_con_periodo_vacio(): void
    {
        $this->escenario();
        $this->uc()->execute(['dl' => 'dlp', 'id_tipo_activ' => '114']);
        $this->assertSame('f_ini between [,]', $this->consultasActividades[0][3]);
    }

    public function test_delegacion_desconocida_usa_id_cero_y_sin_actividades_no_hay_filas(): void
    {
        $this->actividades->method('matching')->willReturn([]);
        $out = $this->uc()->execute(['dl' => 'dlz', 'id_tipo_activ' => '112']);
        $this->assertSame([], $out['a_valores']);
        $this->assertSame([0, 0], [$out['concedidasA2B'], $out['concedidasB2A']]);
    }
}
