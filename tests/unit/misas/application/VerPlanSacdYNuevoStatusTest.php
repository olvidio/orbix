<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoTipo;
use src\misas\application\NuevoStatusPeriodo;
use src\misas\application\VerPlanSacdData;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Plan de misas de un sacd en un rango y cambio masivo de estado de los encargos de una zona. */
final class VerPlanSacdYNuevoStatusTest extends myTest
{
    private EncargoDiaRepositoryInterface&MockObject $encargosDia;
    private EncargoRepositoryInterface&MockObject $encargos;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['id_usuario' => 55];
        $this->encargosDia = $this->createMock(EncargoDiaRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
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

    // ---- VerPlanSacdData ----------------------------------------------------------------------

    private function planSacd(bool $jefeZona, bool $usuarioExiste = true, bool $conSacd = true): VerPlanSacdData
    {
        $usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        if ($usuarioExiste) {
            $u = $this->createMock(Usuario::class);
            $u->method('getCsvIdPauAsString')->willReturn($conSacd ? '10011' : null);
            $usuarios->method('findById')->willReturn($u);
        } else {
            $usuarios->method('findById')->willReturn(null);
        }
        $zonas = $this->createMock(ZonaRepositoryInterface::class);
        if ($conSacd) {
            $zonas->method('matching')->willReturn($jefeZona ? [new \src\zonassacd\domain\entity\Zona()] : []);
        } else {
            $zonas->expects($this->never())->method('matching');
        }

        return new VerPlanSacdData($usuarios, $zonas, $this->encargosDia, $this->encargos);
    }

    private function encargoDia(?int $status, string $ini, string $fin, ?int $idEnc = 5, ?string $observ = null): EncargoDia
    {
        $d = $this->createMock(EncargoDia::class);
        $d->method('getStatus')->willReturn($status);
        $d->method('getTstart')->willReturn(new DateTimeLocal($ini));
        $d->method('getTend')->willReturn(new DateTimeLocal($fin));
        $d->method('getId_enc')->willReturn($idEnc);
        $d->method('getObserv')->willReturn($observ);

        return $d;
    }

    public function test_sin_usuario_no_hay_plan(): void
    {
        $this->assertSame(['rows' => []], $this->planSacd(false, false)->getData('10#x', 'otro', '01/03/2026', '04/03/2026'));
    }

    /** Usuario sin sacd asociado (p. ej. un administrador): antes, excepción al filtrar las zonas con null. */
    public function test_usuario_sin_sacd_asociado_ve_el_plan_como_no_jefe(): void
    {
        $this->encargosDia->method('matching')->willReturn([]);
        $r = $this->planSacd(false, true, false)->getData('10#AB', 'otro', '02/03/2026', '04/03/2026');
        $this->assertCount(2, $r['rows']);
    }

    public function test_cada_dia_del_rango_sin_el_ultimo_tiene_su_fila_aunque_este_libre(): void
    {
        $this->encargosDia->method('matching')->willReturn([]);

        $r = $this->planSacd(false)->getData('10#AB', 'otro', '01/03/2026', '04/03/2026');

        $this->assertSame([
            ['dia' => 'D 1.3', 'encargo' => '', 'observ' => ''],
            ['dia' => 'L 2.3', 'encargo' => '', 'observ' => ''],
            ['dia' => 'M 3.3', 'encargo' => '', 'observ' => ''],
        ], $r['rows']);
    }

    public function test_la_consulta_usa_el_id_del_sacd_antes_de_la_almohadilla_y_el_dia_completo(): void
    {
        $filtros = [];
        $this->encargosDia->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros[] = DescribeCriteria::filtros($c)[0];

            return [];
        });

        $this->planSacd(false)->getData('10#AB', 'otro', '02/03/2026', '03/03/2026');

        $this->assertSame(['id_nom eq 10'], $filtros);
    }

    public function test_un_no_jefe_solo_ve_los_encargos_comunicados(): void
    {
        $this->encargosDia->method('matching')->willReturn([
            $this->encargoDia(EncargoDiaStatus::STATUS_PROPUESTA, '2026-03-02 08:00', '2026-03-02 09:00'),
            $this->encargoDia(EncargoDiaStatus::STATUS_COMUNICADO_SACD, '2026-03-02 10:00', '2026-03-02 11:00', 6, 'llevar cáliz'),
            $this->encargoDia(EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 12:00', '2026-03-02 00:00', 7),
        ]);
        $this->encargos->method('findById')->willReturnCallback(function (int $id): ?Encargo {
            $e = $this->createMock(Encargo::class);
            $e->method('getDesc_enc')->willReturn("Misa $id");

            return $id === 7 ? null : $e;
        });

        $r = $this->planSacd(false)->getData('10#AB', 'otro', '02/03/2026', '03/03/2026');

        $this->assertSame([
            ['dia' => 'L 2.3 10:00-11:00', 'encargo' => 'Misa 6', 'observ' => 'llevar cáliz'],
            ['dia' => 'L 2.3 12:00', 'encargo' => '', 'observ' => ''],
        ], $r['rows']);
    }

    public function test_el_jefe_de_zona_ve_tambien_las_propuestas(): void
    {
        $this->encargosDia->method('matching')->willReturn([$this->encargoDia(EncargoDiaStatus::STATUS_PROPUESTA, '2026-03-02 08:00', '2026-03-02 09:00')]);
        $e = $this->createMock(Encargo::class);
        $e->method('getDesc_enc')->willReturn('Misa 5');
        $this->encargos->method('findById')->willReturn($e);

        $r = $this->planSacd(true)->getData('10#AB', 'otro', '02/03/2026', '03/03/2026');

        $this->assertSame('L 2.3 08:00-09:00', $r['rows'][0]['dia']);
    }

    public function test_un_dia_con_encargos_ocultos_no_deja_fila_y_uno_sin_id_de_encargo_se_salta(): void
    {
        $this->encargosDia->method('matching')->willReturn([
            $this->encargoDia(EncargoDiaStatus::STATUS_PROPUESTA, '2026-03-02 08:00', '2026-03-02 09:00'),
            $this->encargoDia(EncargoDiaStatus::STATUS_COMUNICADO_SACD, '2026-03-02 10:00', '2026-03-02 11:00', null),
        ]);

        $this->assertSame([], $this->planSacd(false)->getData('10#AB', 'otro', '02/03/2026', '03/03/2026')['rows']);
    }

    // ---- NuevoStatusPeriodo ----------------------------------------------------------------------

    private function tipo(int $id): EncargoTipo
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getId_tipo_enc')->willReturn($id);

        return $t;
    }

    private function nuevoStatus(array $tipos): NuevoStatusPeriodo
    {
        $tiposRepo = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tiposRepo->method('matching')->willReturn($tipos);

        return new NuevoStatusPeriodo($tiposRepo, $this->encargosDia, $this->createMock(EncargoHorarioRepositoryInterface::class), $this->encargos);
    }

    private function encargoConId(int $id): Encargo
    {
        $e = $this->createMock(Encargo::class);
        $e->method('getId_enc')->willReturn($id);

        return $e;
    }

    public function test_cambia_el_estado_de_los_dias_de_los_encargos_de_la_zona_en_el_rango(): void
    {
        $criteriosEncargos = [];
        $this->encargos->method('matching')->willReturnCallback(function (Criteria $c) use (&$criteriosEncargos): array {
            $criteriosEncargos[] = DescribeCriteria::filtros($c);

            return [$this->encargoConId(20)];
        });
        $dia1 = new EncargoDia();
        $dia2 = new EncargoDia();
        $rango = null;
        $this->encargosDia->method('matching')->willReturnCallback(function (Criteria $c) use (&$rango, $dia1, $dia2): array {
            $rango = DescribeCriteria::filtros($c);

            return [$dia1, $dia2];
        });
        $this->encargosDia->expects($this->exactly(2))->method('Guardar')->willReturn(true);

        $r = $this->nuevoStatus([$this->tipo(8000), $this->tipo(8110), $this->tipo(8200)])->execute(4, 'otro', '02/03/2026', '08/03/2026', 3);

        $this->assertSame(['error' => ''], $r);
        $this->assertSame(3, $dia1->getStatus());
        $this->assertSame(3, $dia2->getStatus());
        $this->assertSame(['id_tipo_enc in [8110,8200]', 'id_zona eq 4'], $criteriosEncargos[0]);
        $this->assertSame('id_enc eq 20', $rango[0]);
    }

    public function test_los_errores_de_guardado_se_acumulan(): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargoConId(20)]);
        $this->encargosDia->method('matching')->willReturn([new EncargoDia(), new EncargoDia()]);
        $this->encargosDia->method('Guardar')->willReturn(false);
        $this->encargosDia->method('getErrorTxt')->willReturn('E;');

        $this->assertSame(['error' => 'E;E;'], $this->nuevoStatus([$this->tipo(8110)])->execute(4, 'otro', '02/03/2026', '08/03/2026', 2));
    }

    public function test_sin_encargos_en_la_zona_no_se_toca_ningun_dia(): void
    {
        $this->encargos->method('matching')->willReturn([]);
        $this->encargosDia->expects($this->never())->method('matching');
        $this->encargosDia->expects($this->never())->method('Guardar');

        $this->assertSame(['error' => ''], $this->nuevoStatus([])->execute(4, 'otro', '02/03/2026', '08/03/2026', 2));
    }

    /**
     * @dataProvider periodosRelativosProvider
     */
    public function test_los_periodos_relativos_funcionan_sin_fechas_del_usuario(string $periodo): void
    {
        $this->encargos->method('matching')->willReturn([$this->encargoConId(20)]);
        $rango = null;
        $this->encargosDia->method('matching')->willReturnCallback(function (Criteria $c) use (&$rango): array {
            $rango = $c;

            return [];
        });

        $r = $this->nuevoStatus([$this->tipo(8110)])->execute(4, $periodo, '', '', 2);

        $this->assertSame(['error' => ''], $r);
        $this->assertInstanceOf(Criteria::class, $rango);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function periodosRelativosProvider(): iterable
    {
        yield 'próxima semana' => ['proxima_semana'];
        yield 'próximo mes' => ['proximo_mes'];
    }
}
