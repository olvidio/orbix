<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\application\Select_cargos_de_actividad;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\entity\Cargo;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use frontend\shared\config\OrbixRuntime;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Widget del dossier 3102: personas con cargo en una actividad (devuelve el HTML ya pintado). */
final class SelectCargosDeActividadTest extends myTest
{
    private const ID_ACTIV = 3001;
    private const TIPO_N = 111001;
    private const TIPO_S = 141001;

    private ActividadCargoRepositoryInterface&MockObject $cargosActiv;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private CargoRepositoryInterface&MockObject $cargos;
    private PersonaFinderService&MockObject $finder;
    /** @var array<string, mixed> */
    private array $anterior = [];
    private ?string $docRootAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'oPerm'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        // La vista se busca en DOCUMENT_ROOT + web_path, que en CLI no viene informado.
        $this->docRootAnterior = $_SERVER['DOCUMENT_ROOT'] ?? null;
        $_SERVER['DOCUMENT_ROOT'] = substr(dirname(__DIR__, 4), 0, -strlen(OrbixRuntime::webPath()));
        $this->ambito('dl');
        $this->permiso([]);
        $this->cargosActiv = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $this->finder = $this->createMock(PersonaFinderService::class);

        $director = new Cargo();
        $director->setId_cargo(1);
        $director->setCargo('director');
        $director->setTipoCargoVo('d');
        $this->cargos->method('findById')->willReturn($director);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        $_SERVER['DOCUMENT_ROOT'] = $this->docRootAnterior ?? '';
        parent::tearDown();
    }

    private function ambito(string $ambito): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
    }

    /** @param list<string> $oficinas */
    private function permiso(array $oficinas): void
    {
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $p;
    }

    private function widget(): Select_cargos_de_actividad
    {
        $w = new Select_cargos_de_actividad($this->cargosActiv, $this->actividades, $this->cargos, $this->finder);
        $w->setPau('a');
        $w->setObj_pau('Actividad');
        $w->setId_pau(self::ID_ACTIV);
        $w->setId_dossier(3102);
        $w->setBloque('#ficha_activ');
        $w->setQueSel('sel');

        return $w;
    }

    private function hayActividad(int $tipo, ?string $dlOrg = null): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getDl_org')->willReturn($dlOrg ?? \src\shared\config\ConfigGlobal::mi_delef());
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    /** Un cargo de la actividad cuya persona es de la tabla `$idTabla` (n, a, s…). */
    private function hayUnCargo(string $idTabla = 'n'): void
    {
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(self::ID_ACTIV);
        $c->setId_cargo(1);
        $c->setId_nom(10011);
        $c->setObserv('una nota');
        $this->cargosActiv->method('matching')->willReturn([$c]);

        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $p->method('getCentro_o_dl')->willReturn('Centro A');
        $p->method('getId_tabla')->willReturn($idTabla);
        $this->finder->method('findPersonaEnGlobalODePaso')->with(10011)->willReturn($p);
    }

    /**
     * Filas que recibe la rejilla (el JSON `data_select_cargos_de_actividad` del HTML).
     *
     * @return list<array<string, mixed>>
     */
    private function filas(string $html): array
    {
        if (str_contains($html, 'no hay ninguna fila')) {
            return [];
        }
        $this->assertSame(1, preg_match('/var data_select_cargos_de_actividad = (\[.*?\]);/s', $html, $m), 'el HTML no trae los datos de la rejilla');

        return json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
    }

    /** Texto del `confirm()` de «quitar cargo», tal como lo verá el usuario. */
    private function textoConfirmar(string $html): string
    {
        $this->assertSame(1, preg_match('/if \(!confirm\((".*?")\)\) \{/', $html, $m));

        return json_decode($m[1], false, 512, JSON_THROW_ON_ERROR);
    }

    // ---- sin datos ---------------------------------------------------------------------------------------

    public function test_si_la_actividad_no_existe_pinta_la_tabla_vacia_sin_enlaces(): void
    {
        $this->actividades->method('findById')->willReturn(null);
        $this->cargosActiv->expects($this->never())->method('matching');

        $html = $this->widget()->getHtml();

        $this->assertSame([], $this->filas($html));
        $this->assertStringNotContainsString('links-insert-bar', $html);
        $this->assertSame('', $this->textoConfirmar($html), 'hallazgo: sin actividad el confirm sale vacío');
    }

    public function test_una_actividad_sin_cargos_pinta_la_tabla_vacia_pero_con_los_enlaces_de_alta(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->cargosActiv->method('matching')->willReturn([]);

        $html = $this->widget()->getHtml();

        $this->assertSame([], $this->filas($html));
        $this->assertStringContainsString('añadir n</span>', $html);
    }

    // ---- filas -------------------------------------------------------------------------------------------

    public function test_los_cargos_se_piden_por_la_actividad_del_dossier(): void
    {
        $this->hayActividad(self::TIPO_N);
        $filtros = [];
        $this->cargosActiv->expects($this->once())->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($c);

            return [];
        });

        $this->widget()->getHtml();

        $this->assertSame(['id_activ eq ' . self::ID_ACTIV], $filtros);
    }

    public function test_pinta_cargo_persona_centro_y_observaciones(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();
        $filas = $this->filas($html);

        $this->assertCount(1, $filas);
        $this->assertSame('director', $filas[0]['cargo']);
        $this->assertSame('Pérez, Juan  (Centro A)', $filas[0]['nombreyapellidos']);
        $this->assertSame('no', $filas[0]['¿Puedeseragd?']);
        $this->assertSame('una nota', $filas[0]['observaciones']);
        $this->assertStringContainsString('<h2 class="titulo">Relación de cargos</h2>', $html);
    }

    public function test_con_permiso_sobre_el_tipo_de_persona_la_fila_es_seleccionable(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo('n');

        // id_nom#id_item#elim_asis#id_schema (elim_asis 1 = solo el cargo)
        $this->assertStringContainsString('10011#77#1#', $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    public function test_sin_permiso_sobre_el_tipo_de_persona_la_fila_no_se_puede_seleccionar(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo('a'); // sm no gestiona agd en actividades de n

        $this->assertStringNotContainsString('10011', (string) $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    public function test_sin_ninguna_oficina_no_hay_filas_seleccionables_ni_enlaces(): void
    {
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();

        $this->assertStringNotContainsString('10011', (string) $this->filas($html)[0]['sel']);
        $this->assertStringNotContainsString('links-insert-bar', $html);
    }

    public function test_la_fila_que_venia_seleccionada_se_conserva(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo();
        $w = $this->widget();
        $w->setQid_sel('10011#77');
        $w->setQscroll_id(4);

        $html = $w->getHtml();

        $this->assertStringContainsString('name="id_sel" value="10011#77"', $html);
        $this->assertStringContainsString('name="scroll_id_select_cargos_de_actividad" data-tabla="select_cargos_de_actividad" value="4"', $html);
    }

    // ---- borrar también la asistencia --------------------------------------------------------------------

    /**
     * @dataProvider elimAsisProvider
     * @param list<string> $oficinas
     */
    public function test_solo_des_o_vcsd_en_actividades_de_s_o_sg_borran_tambien_la_asistencia(array $oficinas, int $tipo, bool $borraAsistencia): void
    {
        $this->permiso($oficinas);
        $this->hayActividad($tipo);
        $this->hayUnCargo('n');

        $html = $this->widget()->getHtml();
        $confirmar = $this->textoConfirmar($html);

        $this->assertStringStartsWith('¿Está seguro que desea quitar este cargo a esta persona?', $confirmar);
        if ($borraAsistencia) {
            $this->assertSame("¿Está seguro que desea quitar este cargo a esta persona?\nesto también borrará a esta persona de la lista de asistentes", $confirmar, 'el aviso va en una segunda línea');
            // la fila solo es seleccionable si además hay permiso sobre ese tipo de persona
            $sel = (string) $this->filas($html)[0]['sel'];
            if (str_contains($sel, '10011')) {
                $this->assertStringContainsString('10011#77#2#', $sel);
            }
        } else {
            $this->assertStringNotContainsString('asistentes', $confirmar);
            $this->assertStringNotContainsString('#77#2#', (string) $this->filas($html)[0]['sel']);
        }
    }

    /** @return array<string, array{list<string>, int, bool}> */
    public static function elimAsisProvider(): array
    {
        return [
            'des en actividad de s' => [['des'], self::TIPO_S, true],
            'vcsd en actividad de sg' => [['vcsd'], 151001, true],
            'des en actividad de n' => [['des'], self::TIPO_N, false],
            'sm en actividad de s' => [['sm'], self::TIPO_S, false],
        ];
    }

    // ---- enlaces de alta ---------------------------------------------------------------------------------

    public function test_hay_un_enlace_de_alta_firmado_por_cada_tipo_de_persona_permitido(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->cargosActiv->method('matching')->willReturn([]);

        $html = $this->widget()->getHtml();

        $this->assertSame(2, substr_count($html, 'links-insert-btn'), 'sm en actividad de n: n y n de paso');
        $this->assertMatchesRegularExpression(
            "~fnjs_update_div\\('#ficha_activ','[^']*/frontend/actividadcargos/controller/form_cargos_de_actividad\\.php\\?id_dossier=3102&id_pau=3001&mod=nuevo&obj_pau=PersonaN&pau=a&h=[0-9a-f]{32}'\\);\">añadir n</span>~",
            $html
        );
        $this->assertStringContainsString('añadir n de paso</span>', $html);
        $this->assertStringNotContainsString('añadir agd', $html);
    }

    public function test_des_en_una_actividad_de_s_marca_la_fila_para_borrar_tambien_la_asistencia(): void
    {
        $this->permiso(['des']);
        $this->hayActividad(self::TIPO_S);
        $this->hayUnCargo('n');

        $this->assertStringContainsString('10011#77#2#', $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    public function test_en_una_actividad_de_otra_dl_no_se_pueden_añadir_personas_de_paso(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N, 'otra');
        $this->cargosActiv->method('matching')->willReturn([]);

        $html = $this->widget()->getHtml();

        $this->assertStringContainsString('añadir n</span>', $html);
        $this->assertStringNotContainsString('de paso', $html);
    }

    public function test_en_ambito_rstgr_no_hay_botones_ni_enlaces_de_alta(): void
    {
        $this->ambito('rstgr');
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();

        $this->assertCount(1, $this->filas($html), 'la relación se sigue viendo');
        $this->assertStringNotContainsString("onClick='fnjs_mod_cargo(this.form)'", $html);
        $this->assertStringNotContainsString("onClick='fnjs_borrar_cargo(this.form)'", $html);
        $this->assertStringNotContainsString('links-insert-bar', $html);
    }

    // ---- formulario --------------------------------------------------------------------------------------

    public function test_el_formulario_lleva_los_botones_el_contexto_del_dossier_y_las_urls(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();

        $this->assertStringContainsString("value=\"modificar cargo\" onClick='fnjs_mod_cargo(this.form)'", $html);
        $this->assertStringContainsString("value=\"quitar cargo\" onClick='fnjs_borrar_cargo(this.form)'", $html);
        foreach (['pau' => 'a', 'id_pau' => '3001', 'obj_pau' => 'Actividad', 'queSel' => 'sel', 'id_dossier' => '3102', 'permiso' => '3'] as $campo => $valor) {
            $this->assertStringContainsString(sprintf('<input type="hidden" id="%1$s" name="%1$s" value="%2$s">', $campo, $valor), $html);
        }
        $this->assertMatchesRegularExpression('/name="hh" value="[0-9a-f]{32}"/', $html, 'firma de los campos ocultos');
        $this->assertStringContainsString('attr(\'action\', "frontend/actividadcargos/controller/form_cargos_de_actividad.php")', $html);
        $this->assertMatchesRegularExpression("~url: '[^']*/src/actividadcargos/cargo_eliminar'~", $html);
        $this->assertStringContainsString("fnjs_enviar_formulario(formulario, '#ficha_activ')", $html);
    }

    public function test_los_datos_se_cargan_una_sola_vez_aunque_se_pinte_dos_veces(): void
    {
        $this->permiso(['sm']);
        $this->hayActividad(self::TIPO_N);
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(self::ID_ACTIV);
        $c->setId_cargo(1);
        $c->setId_nom(10011);
        $this->cargosActiv->expects($this->once())->method('matching')->willReturn([$c]);
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_tabla')->willReturn('n');
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn($p);

        $w = $this->widget();
        $w->getHtml();
        $w->getHtml();
    }

    public function test_los_setters_normalizan_nulos(): void
    {
        $w = $this->widget();
        $w->setPau(null);
        $w->setObj_pau(null);
        $w->setId_pau(null);
        $w->setPermiso('2');

        $this->assertSame(['', '', 0, 2, 3102], [$w->getPau(), $w->getObj_pau(), $w->getId_pau(), $w->getPermiso(), $w->getId_dossier()]);
    }
}
