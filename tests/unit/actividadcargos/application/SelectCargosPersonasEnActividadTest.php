<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\application\Select_cargos_personas_en_actividad;
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

/** Widget del dossier 1302: actividades en las que una persona tiene cargo (devuelve el HTML ya pintado). */
final class SelectCargosPersonasEnActividadTest extends myTest
{
    private const ID_ACTIV = 3001;
    private const ID_NOM = 10011;
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

    private function widget(int|string|null $modoCurso = null): Select_cargos_personas_en_actividad
    {
        $w = new Select_cargos_personas_en_actividad($this->cargosActiv, $this->cargos, $this->actividades, $this->finder);
        $w->setPau('p');
        $w->setObj_pau('PersonaN');
        $w->setId_pau(self::ID_NOM);
        $w->setId_dossier(1302);
        $w->setBloque('#ficha_personas');
        $w->setQueSel('sel');
        if ($modoCurso !== null) {
            $w->setModo_curso($modoCurso);
        }

        return $w;
    }

    private function hayPersona(string $idTabla = 'n'): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_tabla')->willReturn($idTabla);
        $this->finder->method('findPersonaEnGlobalODePaso')->with(self::ID_NOM)->willReturn($p);
    }

    /** Un cargo de la persona en una actividad del tipo dado. */
    private function hayUnCargo(int $tipo = self::TIPO_N): void
    {
        $c = new ActividadCargo();
        $c->setId_item(77);
        $c->setId_activ(self::ID_ACTIV);
        $c->setId_cargo(1);
        $c->setId_nom(self::ID_NOM);
        $c->setObserv('una nota');
        $this->cargosActiv->method('getActividadCargosDeAsistente')->willReturn([$c]);

        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getNom_activ')->willReturn('crt n Barcelona');
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function filas(string $html): array
    {
        if (str_contains($html, 'no hay ninguna fila')) {
            return [];
        }
        $this->assertSame(1, preg_match('/var data_select_cargos_personas_en_actividad = (\[.*?\]);/s', $html, $m), 'el HTML no trae los datos de la rejilla');

        return json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
    }

    private function textoConfirmar(string $html): string
    {
        $this->assertSame(1, preg_match('/if \(!confirm\((".*?")\)\) \{/', $html, $m));

        return json_decode($m[1], false, 512, JSON_THROW_ON_ERROR);
    }

    // ---- consulta ----------------------------------------------------------------------------------------

    /**
     * @dataProvider modosProvider
     * @param list<string> $prefijos comienzo de cada filtro esperado sobre la actividad
     */
    public function test_el_modo_de_curso_decide_el_filtro_de_actividades(int|string|null $modo, array $prefijos, string $radioMarcado): void
    {
        $this->hayPersona();
        $filtros = null;
        $this->cargosActiv->expects($this->once())->method('getActividadCargosDeAsistente')
            ->willReturnCallback(function (int $idNom, Criteria $c) use (&$filtros): array {
                $this->assertSame(self::ID_NOM, $idNom);
                $filtros = DescribeCriteria::filtros($c);

                return [];
            });

        $html = $this->widget($modo)->getHtml();

        $this->assertCount(count($prefijos), $filtros);
        foreach ($prefijos as $i => $prefijo) {
            $this->assertStringStartsWith($prefijo, $filtros[$i]);
        }
        $this->assertMatchesRegularExpression("/id='" . $radioMarcado . "' name='modo_curso' value=\\d checked/", $html);
        $this->assertSame(1, substr_count($html, ' checked onclick=fnjs_actualizar'), 'solo un modo marcado');
    }

    /** @return array<string, array{int|string|null, list<string>, string}> */
    public static function modosProvider(): array
    {
        return [
            'por defecto: actuales del curso' => [null, ['status eq 2', 'f_ini between'], 'modo_curso_1'],
            'modo 0 se trata como 1' => [0, ['status eq 2', 'f_ini between'], 'modo_curso_1'],
            'todas las del curso' => [2, ['f_ini between'], 'modo_curso_2'],
            'todas' => ['3', [], 'modo_curso_3'],
        ];
    }

    public function test_el_periodo_del_filtro_es_un_curso_completo(): void
    {
        $this->hayPersona();
        $filtros = [];
        $this->cargosActiv->method('getActividadCargosDeAsistente')->willReturnCallback(function (int $idNom, Criteria $c) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($c);

            return [];
        });

        $this->widget(2)->getHtml();

        $this->assertSame(1, preg_match('/^f_ini between \[(\d{4})-(\d\d-\d\d),(\d{4})-(\d\d-\d\d)\]$/', $filtros[0], $m), $filtros[0]);
        $this->assertSame((int) $m[1] + 1, (int) $m[3], 'las fechas llegan sin las comillas del WHERE antiguo y abarcan un curso');
    }

    // ---- filas -------------------------------------------------------------------------------------------

    public function test_una_persona_sin_cargos_pinta_la_tabla_vacia(): void
    {
        $this->hayPersona();
        $this->cargosActiv->method('getActividadCargosDeAsistente')->willReturn([]);

        $html = $this->widget()->getHtml();

        $this->assertSame([], $this->filas($html));
        $this->assertStringContainsString('<h2 class="titulo">Relación de cargos</h2>', $html);
    }

    public function test_pinta_cargo_actividad_y_observaciones(): void
    {
        $this->permiso(['sm']);
        $this->hayPersona();
        $this->hayUnCargo();

        $filas = $this->filas($this->widget()->getHtml());

        $this->assertCount(1, $filas);
        $this->assertSame('director', $filas[0]['cargo']);
        $this->assertSame('crt n Barcelona', $filas[0]['actividad']);
        $this->assertSame('no', $filas[0]['¿Puedeseragd?']);
        $this->assertSame('una nota', $filas[0]['observaciones']);
    }

    public function test_con_permiso_sobre_el_tipo_de_actividad_la_fila_es_seleccionable_con_id_item_y_elim_asis(): void
    {
        $this->permiso(['sm']);
        $this->hayPersona('n');
        $this->hayUnCargo(self::TIPO_N);

        // "id_item#elim_asis": el formato que espera ActividadCargoEliminar para esta lista
        $this->assertStringEndsWith('#77#1', $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    public function test_sin_permiso_sobre_el_tipo_de_actividad_la_fila_no_se_puede_seleccionar(): void
    {
        $this->permiso(['sm']);
        $this->hayPersona('n');
        $this->hayUnCargo(161001); // crt sss+: sm no lo gestiona

        $this->assertStringNotContainsString('77', (string) $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    public function test_sin_ninguna_oficina_no_hay_filas_seleccionables_ni_enlaces(): void
    {
        $this->hayPersona();
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();

        $this->assertStringNotContainsString('77', (string) $this->filas($html)[0]['sel']);
        $this->assertStringNotContainsString('links-insert-bar', $html);
    }

    // ---- borrar también la asistencia --------------------------------------------------------------------

    /**
     * A diferencia de la lista 3102, aquí no se mira el tipo de actividad: des/vcsd
     * avisan siempre (el caso de uso solo borra la asistencia si la actividad es de s/sg).
     *
     * @dataProvider oficinasProvider
     * @param list<string> $oficinas
     */
    public function test_des_y_vcsd_avisan_de_que_tambien_se_borra_la_asistencia(array $oficinas, bool $avisa): void
    {
        $this->permiso($oficinas);
        $this->hayPersona();
        $this->cargosActiv->method('getActividadCargosDeAsistente')->willReturn([]);

        $confirmar = $this->textoConfirmar($this->widget()->getHtml());

        $this->assertSame(
            '¿Está seguro que desea quitar este cargo a esta persona?' . ($avisa ? "\nesto también borrará a esta persona de la lista de asistentes" : ''),
            $confirmar
        );
    }

    /** @return array<string, array{list<string>, bool}> */
    public static function oficinasProvider(): array
    {
        return ['des' => [['des'], true], 'vcsd' => [['vcsd'], true], 'sm' => [['sm'], false], 'ninguna' => [[], false]];
    }

    public function test_des_marca_las_filas_para_borrar_tambien_la_asistencia(): void
    {
        $this->permiso(['des', 'sm']);
        $this->hayPersona('n');
        $this->hayUnCargo(self::TIPO_N);

        $this->assertStringEndsWith('#77#2', $this->filas($this->widget()->getHtml())[0]['sel']);
    }

    // ---- enlaces de alta ---------------------------------------------------------------------------------

    public function test_hay_dos_barras_de_alta_con_un_enlace_firmado_por_tipo_de_actividad_permitido(): void
    {
        $this->permiso(['sm']);
        $this->hayPersona('n');
        $this->cargosActiv->method('getActividadCargosDeAsistente')->willReturn([]);

        $html = $this->widget()->getHtml();

        $this->assertSame(2, substr_count($html, 'links-insert-bar'));
        $this->assertStringContainsString('Añadir cargo de la dl', $html);
        $this->assertStringContainsString('Añadir cargo de otra dl', $html);
        // el de la dl lleva que_dl; el de otra dl, no
        $miDl = \src\shared\config\ConfigGlobal::mi_delef();
        $this->assertMatchesRegularExpression(
            "~/frontend/actividadcargos/controller/form_cargos_personas_en_actividad\\.php\\?id_dossier=1302&id_pau=10011&id_tipo=111&mod=nuevo&obj_pau=PersonaN&pau=p&que_dl=" . preg_quote($miDl, '~') . "&h=[0-9a-f]{32}'\\);\">\\+ crt n</span>~",
            $html
        );
        $this->assertMatchesRegularExpression(
            "~/frontend/actividadcargos/controller/form_cargos_personas_en_actividad\\.php\\?id_dossier=1302&id_pau=10011&id_tipo=111&mod=nuevo&obj_pau=PersonaN&pau=p&h=[0-9a-f]{32}'\\);\">\\+ crt n</span>~",
            $html
        );
        $this->assertStringNotContainsString('>+ crt sss+</span>', $html, 'sm no gestiona sss+');
    }

    // ---- formulario --------------------------------------------------------------------------------------

    public function test_el_formulario_lleva_los_botones_el_contexto_del_dossier_y_las_urls(): void
    {
        $this->permiso(['sm']);
        $this->hayPersona();
        $this->hayUnCargo();

        $html = $this->widget()->getHtml();

        $this->assertStringContainsString("value=\"modificar cargo\" onClick='fnjs_mod_cargo(this.form)'", $html);
        $this->assertStringContainsString("value=\"quitar cargo\" onClick='fnjs_borrar_cargo(this.form)'", $html);
        foreach (['pau' => 'p', 'id_pau' => '10011', 'obj_pau' => 'PersonaN', 'queSel' => 'sel', 'id_dossier' => '1302', 'permiso' => '3'] as $campo => $valor) {
            $this->assertStringContainsString(sprintf('<input type="hidden" id="%1$s" name="%1$s" value="%2$s">', $campo, $valor), $html);
        }
        $this->assertMatchesRegularExpression('/name="hh" value="[0-9a-f]{32}"/', $html);
        $this->assertStringContainsString('attr(\'action\', "frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php")', $html);
        $this->assertMatchesRegularExpression("~url: '[^']*/src/actividadcargos/cargo_eliminar'~", $html);
        $this->assertStringContainsString("fnjs_enviar_formulario(formulario, '#ficha_personas')", $html);
    }

    public function test_los_setters_normalizan_nulos_y_el_modo_de_curso(): void
    {
        $w = $this->widget();
        $w->setPau(null);
        $w->setObj_pau(null);
        $w->setId_pau(null);
        $w->setPermiso('2');
        $w->setModo_curso(null);

        $this->assertSame(['', '', 0, 2, 1302, 1], [$w->getPau(), $w->getObj_pau(), $w->getId_pau(), $w->getPermiso(), $w->getId_dossier(), $w->getModo_curso()]);
        $w->setModo_curso('3');
        $this->assertSame(3, $w->getModo_curso());
    }
}
