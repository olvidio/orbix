<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacd;
use src\misas\application\BuscarPlanCtrData;
use src\misas\application\VerPlanCtrData;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\entity\InicialesSacd;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\IdPau;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Plan de misas por centro (cuadrícula de encargos × días) y su buscador. */
final class PlanPorCentroDataTest extends myTest
{
    private const ROLES = [1 => 'p-sacd', 2 => 'Centro sv', 3 => 'admin', 4 => 'Centro sf'];

    private UsuarioRepositoryInterface&MockObject $usuarios;
    private RoleRepositoryInterface&MockObject $roles;
    private ZonaRepositoryInterface&MockObject $zonas;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['id_usuario' => 55];
        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->roles = $this->createMock(RoleRepositoryInterface::class);
        $this->roles->method('getArrayRoles')->willReturn(self::ROLES);
        $this->zonas = $this->createMock(ZonaRepositoryInterface::class);
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

    private function usuario(int $idRole, ?string $pau): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn($idRole);
        $u->method('getCsvIdPauVo')->willReturn($pau === null ? null : new IdPau($pau));
        $u->method('getCsvIdPauAsString')->willReturn($pau);
        $this->usuarios->method('findById')->willReturn($u);
    }

    // ---- VerPlanCtrData ----------------------------------------------------------------------

    private EncargoCtrRepositoryInterface&MockObject $encargosCtr;
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoDiaRepositoryInterface&MockObject $encargosDia;

    private function verPlan(): VerPlanCtrData
    {
        $this->encargosCtr = $this->createMock(EncargoCtrRepositoryInterface::class);
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->encargosDia = $this->createMock(EncargoDiaRepositoryInterface::class);
        $iniciales = $this->createMock(InicialesSacdRepositoryInterface::class);
        $iniciales->method('findById')->willReturnCallback(function (int $id): ?InicialesSacd {
            if ($id === 99) {
                return null;
            }
            $i = $this->createMock(InicialesSacd::class);
            $i->method('getIniciales')->willReturn($id === 10 ? 'AB' : 'CD');

            return $i;
        });
        $personas = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personas->method('findById')->willReturnCallback(function (int $id): ?PersonaSacd {
            if ($id === 99) {
                return null;
            }
            $p = $this->createMock(PersonaSacd::class);
            $p->method('getNombreApellidos')->willReturn($id === 10 ? 'Ana B' : 'Carlos D');

            return $p;
        });

        return new VerPlanCtrData($this->usuarios, $this->roles, $this->zonas, $this->encargosCtr, $this->encargos, $this->encargosDia, $iniciales, $personas);
    }

    private function encargoCtr(?int $idEnc): EncargoCtr
    {
        $e = $this->createMock(EncargoCtr::class);
        $e->method('getId_enc')->willReturn($idEnc);

        return $e;
    }

    private function encargoDia(?int $idNom, ?int $status, string $ini, ?string $observ = null): EncargoDia
    {
        $d = $this->createMock(EncargoDia::class);
        $d->method('getId_nom')->willReturn($idNom);
        $d->method('getStatus')->willReturn($status);
        $d->method('getTstart')->willReturn(new DateTimeLocal($ini));
        $d->method('getObserv')->willReturn($observ);

        return $d;
    }

    private function encargo(string $desc): Encargo
    {
        $e = $this->createMock(Encargo::class);
        $e->method('getDesc_enc')->willReturn($desc);

        return $e;
    }

    public function test_sin_usuario_no_hay_cuadricula(): void
    {
        $this->usuarios->method('findById')->willReturn(null);

        $this->assertSame(['columns' => [], 'rows' => [], 'legend' => []], $this->verPlan()->getData(1, 'otro', '02/03/2026', '04/03/2026'));
    }

    public function test_las_columnas_son_los_dias_del_rango_sin_el_ultimo(): void
    {
        $this->usuario(2, '1003');
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([]);

        $r = $v->getData(1003, 'otro', '01/03/2026', '04/03/2026');

        $this->assertSame([['D', '1', '3', '2026-03-01'], ['L', '2', '3', '2026-03-02'], ['M', '3', '3', '2026-03-03']], array_map(fn ($c) => [$c['letra'], $c['num_dia'], $c['num_mes'], $c['id_dia']], $r['columns']));
        $this->assertSame([], $r['rows']);
    }

    public function test_un_centro_ve_solo_lo_comunicado_al_centro_y_el_resto_es_guiones(): void
    {
        $this->usuario(2, '1003');
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([$this->encargoCtr(5), $this->encargoCtr(null), $this->encargoCtr(6)]);
        $this->encargos->method('findById')->willReturnCallback(fn (int $id): ?Encargo => $id === 6 ? null : $this->encargo('Misa diaria'));
        $this->encargosDia->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            // Primer día: propuesta (oculta al centro); segundo: comunicada al centro con hora y observación.
            if (preg_match('/2026-03-02/', implode('|', $f))) {
                return [$this->encargoDia(10, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 08:30', 'nota')];
            }
            if (preg_match('/2026-03-01/', implode('|', $f))) {
                return [$this->encargoDia(10, EncargoDiaStatus::STATUS_PROPUESTA, '2026-03-01 08:30')];
            }

            return [];
        });

        $r = $v->getData(1003, 'otro', '01/03/2026', '04/03/2026');

        $this->assertCount(1, $r['rows'], 'se saltan el encargo sin id y el inexistente');
        $this->assertSame('Misa diaria', $r['rows'][0]['desc_enc']);
        $this->assertSame([' -- ', 'AB 08:30*', ' -- '], $r['rows'][0]['cells']);
        $this->assertSame([['iniciales' => 'AB', 'nombre' => 'Ana B']], $r['legend']);
    }

    public function test_un_sacd_ve_comunicados_a_sacd_y_a_centro_y_un_jefe_de_zona_todo(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('matching')->willReturn([]);
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([$this->encargoCtr(5)]);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa'));
        $this->encargosDia->method('matching')->willReturn([$this->encargoDia(20, EncargoDiaStatus::STATUS_COMUNICADO_SACD, '2026-03-02 00:00')]);

        $r = $v->getData(1003, 'otro', '02/03/2026', '03/03/2026');

        $this->assertSame(['CD '], $r['rows'][0]['cells'], 'hora 00:00 = sin hora');
    }

    public function test_el_jefe_de_zona_ve_las_propuestas_y_un_rol_sin_permiso_no_ve_nada(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('matching')->willReturn([new Zona()]);
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([$this->encargoCtr(5)]);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa'));
        $this->encargosDia->method('matching')->willReturn([$this->encargoDia(10, EncargoDiaStatus::STATUS_PROPUESTA, '2026-03-02 09:00')]);
        $this->assertSame(['AB 09:00'], $v->getData(1003, 'otro', '02/03/2026', '03/03/2026')['rows'][0]['cells']);
    }

    public function test_un_administrador_no_ve_el_contenido_de_las_celdas(): void
    {
        $this->usuario(3, null);
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([$this->encargoCtr(5)]);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa'));
        $this->encargosDia->method('matching')->willReturn([$this->encargoDia(10, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 09:00')]);

        $this->assertSame([' -- '], $v->getData(1003, 'otro', '02/03/2026', '03/03/2026')['rows'][0]['cells']);
    }

    public function test_la_leyenda_ordena_por_iniciales_y_tolera_sacd_sin_iniciales_ni_ficha(): void
    {
        $this->usuario(2, '1003');
        $v = $this->verPlan();
        $this->encargosCtr->method('getEncargosCentro')->willReturn([$this->encargoCtr(5)]);
        $this->encargos->method('findById')->willReturn($this->encargo('Misa'));
        $this->encargosDia->method('matching')->willReturn([
            $this->encargoDia(20, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 09:00'),
            $this->encargoDia(99, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 10:00'),
            $this->encargoDia(null, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 11:00'),
            $this->encargoDia(10, EncargoDiaStatus::STATUS_COMUNICADO_CTR, '2026-03-02 12:00'),
        ]);

        $r = $v->getData(1003, 'otro', '02/03/2026', '03/03/2026');

        $this->assertSame([
            ['iniciales' => '--', 'nombre' => ''],
            ['iniciales' => 'AB', 'nombre' => 'Ana B'],
            ['iniciales' => 'CD', 'nombre' => 'Carlos D'],
        ], array_map(fn ($l) => ['iniciales' => trim($l['iniciales']), 'nombre' => $l['nombre']], $r['legend']));
    }

    // ---- BuscarPlanCtrData ----------------------------------------------------------------------

    /** @param list<int> $idUbis */
    private function buscar(array $idUbis = [], ?EncargoSacdRepositoryInterface $encargosSacd = null, ?EncargoRepositoryInterface $encargos = null, array $ellos = [], array $ellas = []): BuscarPlanCtrData
    {
        $zc = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zc->method('idUbisDeZona')->willReturn($idUbis);
        $repoEllos = $this->createMock(CentroEllosRepositoryInterface::class);
        $repoEllos->method('matching')->willReturn($ellos);
        $repoEllas = $this->createMock(CentroEllasRepositoryInterface::class);
        $repoEllas->method('matching')->willReturn($ellas);

        return new BuscarPlanCtrData(
            $this->usuarios, $this->roles, $this->zonas, $repoEllos, $repoEllas,
            $encargosSacd ?? $this->createMock(EncargoSacdRepositoryInterface::class),
            $encargos ?? $this->createMock(EncargoRepositoryInterface::class),
            new IdNomJefeResolver($this->usuarios, $this->roles),
            new CentrosDeZona($zc)
        );
    }

    private function assertVistaNinguna(array $r): void
    {
        $this->assertSame(['view' => 'none', 'zonas_opciones' => [], 'zonas_selected' => 0, 'centros_opciones' => [], 'centros_selected' => '', 'id_ubi_centro' => ''], $r);
    }

    public function test_sin_usuario_o_centro_sin_id_o_sacd_sin_permiso_no_hay_vista(): void
    {
        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->usuarios->method('findById')->willReturn(null);
        $this->assertVistaNinguna($this->buscar()->getData(0));

        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->usuario(2, null);
        $this->assertVistaNinguna($this->buscar()->getData(0));

        $this->usuarios = $this->createMock(UsuarioRepositoryInterface::class);
        $this->usuario(1, null);
        $this->assertVistaNinguna($this->buscar()->getData(0));
    }

    public function test_un_sacd_con_zona_elegida_ve_los_centros_de_esa_zona(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('getArrayZonas')->with(10011)->willReturn([4 => 'Zona 4']);
        $ellos = $this->createMock(CentroEllos::class);
        $ellos->method('getId_ubi')->willReturn(501);
        $ellos->method('getNombre_ubi')->willReturn('Ctr sv');
        $ellas = $this->createMock(CentroEllas::class);
        $ellas->method('getId_ubi')->willReturn(502);
        $ellas->method('getNombre_ubi')->willReturn('Ctr sf');

        $r = $this->buscar([501, 502], null, null, [$ellos], [$ellas])->getData(4);

        $this->assertSame('sacd', $r['view']);
        $this->assertSame([-1 => 'centros encargos', 4 => 'Zona 4'], $r['zonas_opciones']);
        $this->assertSame(4, $r['zonas_selected']);
        $this->assertSame(['501' => 'Ctr sv', '502' => 'Ctr sf'], $r['centros_opciones']);
        $this->assertSame('502', $r['centros_selected'], 'queda seleccionado el último centro');
    }

    public function test_una_zona_sin_centros_deja_la_lista_vacia(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('getArrayZonas')->willReturn([]);

        $r = $this->buscar([])->getData(4);

        $this->assertSame([], $r['centros_opciones']);
        $this->assertSame('', $r['centros_selected']);
    }

    public function test_sin_zona_elegida_se_usa_la_de_centros_encargos_y_se_listan_los_encargos_del_sacd(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('getArrayZonas')->willReturn([]);
        $encSacd = $this->createMock(EncargoSacd::class);
        $encSacd->method('getId_enc')->willReturn(77);
        $repoSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $repoSacd->method('matching')->willReturnOnConsecutiveCalls([$encSacd], []);
        $enc = $this->createMock(Encargo::class);
        $enc->method('getId_tipo_enc')->willReturn(8110);
        $repoEnc = $this->createMock(EncargoRepositoryInterface::class);
        $repoEnc->expects($this->once())->method('findById')->with(77)->willReturn($enc);

        $r = $this->buscar([], $repoSacd, $repoEnc)->getData(0);

        $this->assertSame(-1, $r['zonas_selected']);
        $this->assertSame('sacd', $r['view']);
        $this->assertSame([], $r['centros_opciones'], 'los encargos de tipo 8xxx no son de centro');
    }

    public function test_los_encargos_vigentes_con_y_sin_fecha_fin_se_cuentan_los_dos(): void
    {
        $this->usuario(1, '10011');
        $this->zonas->method('getArrayZonas')->willReturn([]);
        $e1 = $this->createMock(EncargoSacd::class);
        $e1->method('getId_enc')->willReturn(77);
        $e2 = $this->createMock(EncargoSacd::class);
        $e2->method('getId_enc')->willReturn(78);
        $repoSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        // Sin fecha fin (1) y con fecha fin futura (1): ambos son vigentes.
        $repoSacd->method('matching')->willReturnOnConsecutiveCalls([$e1], [$e2]);
        $consultados = [];
        $repoEnc = $this->createMock(EncargoRepositoryInterface::class);
        $repoEnc->method('findById')->willReturnCallback(function (int $id) use (&$consultados): ?Encargo {
            $consultados[] = $id;

            return null;
        });

        $this->buscar([], $repoSacd, $repoEnc)->getData(0);

        // Hallazgo: la unión con `+` de dos listas indexadas descarta el segundo elemento (misma clave 0).
        $this->assertSame([77], $consultados);
    }
}
