<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\permisos\domain\XPermisos;
use src\personas\application\PersonasSelectData;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Listado de personas de la pantalla de selección: criterio según filtros, permisos y filas. */
final class PersonasSelectDataTest extends myTest
{
    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaPubRepositoryInterface&MockObject $repoPub;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private PreferenciaRepositoryInterface&MockObject $prefs;
    private PersonasSelectData $uc;
    private array $sesion;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesion = $_SESSION;
        $_SESSION['session_auth'] = ['role_pau' => 'ctr', 'id_usuario' => 7];
        unset($_SESSION['oPerm'], $_SESSION['oConfig']);

        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->repoPub = $this->createMock(PersonaPubRepositoryInterface::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->prefs = $this->createMock(PreferenciaRepositoryInterface::class);
        $this->uc = new PersonasSelectData(
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class),
            $this->repoN,
            $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaAgdRepositoryInterface::class),
            $this->repoPub,
            $this->centrosDl,
            $this->createMock(CentroRepositoryInterface::class),
            $this->prefs,
        );
    }

    public function tearDown(): void
    {
        $_SESSION = $this->sesion;
        parent::tearDown();
    }

    private function persona(int $id, string $nom, ?int $ctr): PersonaN
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getId_tabla')->willReturn('n');
        $p->method('getPrefApellidosNombre')->willReturn($nom);
        $p->method('getId_ctr')->willReturn($ctr);
        $p->method('getNivel_stgr')->willReturn(1);

        return $p;
    }

    /** @return list<string> */
    private function filtrosDeLaBusqueda(): array
    {
        $visto = null;
        $this->repoN->expects($this->once())->method('matching')->willReturnCallback(function (Criteria $c) use (&$visto): array {
            $visto = $c;

            return [];
        });
        $this->uc->execute($this->entrada);

        return DescribeCriteria::filtros($visto);
    }

    private array $entrada = ['tabla' => 'p_numerarios'];

    public function test_por_defecto_solo_busca_personas_con_situacion_activa(): void
    {
        $this->assertSame(['situacion eq A'], $this->filtrosDeLaBusqueda());
    }

    public function test_los_apellidos_y_el_nombre_se_buscan_por_prefijo_sin_acentos_o_exactos(): void
    {
        $this->entrada = ['tabla' => 'p_numerarios', 'apellido1' => 'Pe', 'nombre' => 'Ana'];
        $f = $this->filtrosDeLaBusqueda();
        $this->assertSame(['apellido1 matches_unaccented ^Pe', 'nom matches_unaccented ^Ana', 'situacion eq A'], $f);
    }

    public function test_con_exacto_se_compara_por_igualdad_y_el_sacd_filtra(): void
    {
        $this->entrada = ['tabla' => 'p_numerarios', 'apellido2' => 'Gil', 'exacto' => '1', 'es_sacd' => 1];
        $this->assertSame(['apellido2 eq Gil', 'situacion eq A', 'sacd eq true'], $this->filtrosDeLaBusqueda());
    }

    public function test_con_cambios_se_quitan_las_bajas_salvo_para_el_director(): void
    {
        $this->entrada = ['tabla' => 'p_numerarios', 'cmb' => '1'];
        $this->assertSame(['situacion neq B'], $this->filtrosDeLaBusqueda());
    }

    public function test_el_filtro_por_centro_resuelve_los_ids_de_centro(): void
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn(1003);
        $this->centrosDl->method('matching')->willReturn([$c]);
        $this->entrada = ['tabla' => 'p_numerarios', 'centro' => 'Cen'];

        $this->assertSame(['situacion eq A', 'id_ctr in [1003]'], $this->filtrosDeLaBusqueda());
    }

    /** @param list<string> $vistos */
    private function centrosVistos(array &$vistos): void
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getId_ubi')->willReturn(1003);
        $this->centrosDl->method('matching')->willReturnCallback(function (Criteria $cr) use (&$vistos, $c): array {
            $vistos[] = \Tests\support\DescribeCriteria::filtros($cr)[0];
            return [$c];
        });
    }

    public function test_los_textos_se_escapan_en_la_regex(): void
    {
        $vistos = [];
        $this->centrosVistos($vistos);
        $this->entrada = ['tabla' => 'p_numerarios', 'apellido1' => 'Gil (x).', 'centro' => 'Terrassa (sss+)'];
        $this->assertSame('apellido1 matches_unaccented ^Gil \\(x\\)\\.', $this->filtrosDeLaBusqueda()[0]);
        $this->assertSame(['nombre_ubi matches_unaccented ^Terrassa \\(sss\\+\\)'], $vistos);
    }

    public function test_el_centro_exacto_se_compara_tal_cual(): void
    {
        $vistos = [];
        $this->centrosVistos($vistos);
        $this->entrada = ['tabla' => 'p_numerarios', 'centro' => "D'Aran", 'exacto' => '1'];
        $this->filtrosDeLaBusqueda();
        $this->assertSame(["nombre_ubi eq D'Aran"], $vistos);
    }

    public function test_un_centro_sin_coincidencias_avisa(): void
    {
        $this->centrosDl->method('matching')->willReturn([]);
        $r = $this->uc->execute(['tabla' => 'p_numerarios', 'centro' => 'Zzz']);

        $this->assertArrayHasKey('error', $r);
    }

    public function test_una_tabla_desconocida_devuelve_lista_vacia(): void
    {
        $r = $this->uc->execute(['tabla' => 'otra']);

        $this->assertSame(['otra', '', 0, []], [$r['tabla'], $r['obj_pau'], $r['total'], $r['personas']]);
    }

    public function test_las_filas_salen_ordenadas_sin_acentos_con_centro_nivel_y_enlace(): void
    {
        $this->repoN->method('matching')->willReturn([$this->persona(2, 'Zamora', 1003), $this->persona(1, 'Álvarez', null)]);
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centrosDl->method('findById')->with(1003)->willReturn($c);

        $r = $this->uc->execute(['tabla' => 'p_numerarios']);

        $this->assertSame(2, $r['total']);
        $this->assertSame(['Álvarez', 'Zamora'], array_column($r['personas'], 'nom'));
        $this->assertSame(['', 'Centro A'], array_column($r['personas'], 'nombre_ubi'));
        $this->assertSame('PersonaN', $r['obj_pau']);
        $this->assertSame(
            ['id_nom' => 2, 'id_tabla' => 'n', 'obj_pau' => 'PersonaN'],
            $r['personas'][1]['home_link_spec']['query'],
        );
        $this->assertNotSame('', $r['personas'][0]['nivel_stgr'], 'numerarios muestran nivel stgr');
    }

    public function test_el_nivel_stgr_se_oculta_en_planning_y_el_permiso_depende_de_la_oficina(): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(static fn (string $p): bool => $p === 'sm');
        $_SESSION['oPerm'] = $perm;
        $this->repoN->method('matching')->willReturn([$this->persona(1, 'A', null)]);

        $r = $this->uc->execute(['tabla' => 'p_numerarios', 'tipo' => 'planning']);

        $this->assertSame(3, $r['permiso']);
        $this->assertSame('', $r['personas'][0]['nivel_stgr']);
    }

    public function test_sin_permiso_de_oficina_el_permiso_es_1_y_se_lee_la_preferencia_del_usuario(): void
    {
        $pref = $this->createMock(\src\usuarios\domain\entity\Preferencia::class);
        $pref->method('getPreferencia')->willReturn('{"cols":1}');
        $this->prefs->expects($this->once())->method('findById')->with(7, 'tabla_presentacion')->willReturn($pref);
        $this->repoN->method('matching')->willReturn([]);

        $r = $this->uc->execute(['tabla' => 'p_numerarios']);

        $this->assertSame([1, '{"cols":1}'], [$r['permiso'], $r['sPrefs']]);
    }

    public function test_las_personas_de_paso_filtran_por_tabla_y_muestran_la_dl_en_lugar_del_centro(): void
    {
        $p = $this->createMock(\src\personas\domain\entity\PersonaEx::class);
        $p->method('getId_nom')->willReturn(-5);
        $p->method('getId_tabla')->willReturn('pn');
        $p->method('getPrefApellidosNombre')->willReturn('Visitante');
        $p->method('getDl')->willReturn('dlp');
        $visto = null;
        $this->repoPub->method('getPersonasParaListado')->willReturnCallback(function (Criteria $c) use (&$visto, $p): array {
            $visto = $c;

            return [$p];
        });

        $r = $this->uc->execute(['tabla' => 'p_de_paso_ex', 'na' => 'n']);

        $this->assertSame('PersonaEx', $r['obj_pau']);
        $this->assertSame('dlp', $r['personas'][0]['nombre_ubi']);
        $this->assertSame(['situacion eq A', 'id_tabla eq pn'], DescribeCriteria::filtros($visto));
    }

    public function test_un_usuario_de_persona_sin_usuario_en_sesion_avisa(): void
    {
        $_SESSION['session_auth'] = ['role_pau' => 'nom'];

        $this->assertArrayHasKey('error', $this->uc->execute([]));
    }
}
