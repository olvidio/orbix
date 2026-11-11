<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\dossiers\application\DossiersListaFichasData;
use src\dossiers\application\DossiersVerPantallaData;
use src\dossiers\application\DossierTipoFileSuffixResolver;
use src\dossiers\application\support\DossierFichaSelectRunner;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierCodigo;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\profesores\domain\InfoProfesorLatin;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;
use Tests\myTest;

/**
 * Cuerpo de la pantalla de dossiers: cabecera (persona, actividad o ubi) y, debajo,
 * la relación de dossiers o las fichas pedidas.
 *
 * Sin base de datos: los repositorios son simulados; el widget `Select_*` es una
 * clase de prueba creada en un directorio temporal (el resolutor los busca en disco)
 * y la clase de datos del dossier 1022 se sustituye en el contenedor.
 */
final class DossiersVerPantallaDataTest extends myTest
{
    private const ID_NOM = 10011;
    private const ID_ACTIV = 3001;
    private const WIDGET = 'src\\fixdossier\\application\\Select_demo';

    private TipoDossierRepositoryInterface&MockObject $tipos;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private PersonaNRepositoryInterface&MockObject $personasN;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private DossiersListaFichasData&MockObject $lista;
    private PersonaFinderService&MockObject $finder;
    private InfoProfesorLatin&MockObject $info;
    /** @var array<class-string, object> servicios originales del contenedor */
    private array $originales = [];
    private string $raiz = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->tipos = $this->createMock(TipoDossierRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->personasN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $this->lista = $this->createMock(DossiersListaFichasData::class);
        $this->lista->method('build')->willReturn(['a_filas' => [['descripcion' => 'un dossier']], 'web_icons' => 'x']);

        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->info = $this->createMock(InfoProfesorLatin::class);
        $this->info->method('getPau')->willReturn('p');
        $this->info->method('getId_dossier')->willReturn(1022);
        $this->info->method('getTxtTitulo')->willReturn('Latín');
        $this->info->method('getTxtExplicacion')->willReturn('explicación');
        $this->info->method('getTxtEliminar')->willReturn('¿seguro?');
        $this->info->method('getColeccion')->willReturn([]);
        foreach ([PersonaFinderService::class => $this->finder, InfoProfesorLatin::class => $this->info] as $id => $doble) {
            $this->originales[$id] = $GLOBALS['container']->get($id);
            $GLOBALS['container']->set($id, $doble);
        }

        // Widget de prueba en disco: el resolutor exige que exista src/<app>/application/Select_<codigo>.php
        $this->raiz = sys_get_temp_dir() . '/orbix_dossiers_' . getmypid();
        $dir = $this->raiz . '/src/fixdossier/application';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $fichero = $dir . '/Select_demo.php';
        if (!is_file($fichero)) {
            file_put_contents($fichero, <<<'PHP'
<?php

namespace src\fixdossier\application;

class Select_demo
{
    /** @var array<string, mixed> */
    public static array $recibido = [];

    public function setId_dossier(mixed $v): void { self::$recibido['id_dossier'] = $v; }
    public function setPau(mixed $v): void { self::$recibido['pau'] = $v; }
    public function setObj_pau(mixed $v): void { self::$recibido['obj_pau'] = $v; }
    public function setId_pau(mixed $v): void { self::$recibido['id_pau'] = $v; }
    public function setPermiso(mixed $v): void { self::$recibido['permiso'] = $v; }
    public function setBloque(mixed $v): void { self::$recibido['bloque'] = $v; }
    public function setQueSel(mixed $v): void { self::$recibido['queSel'] = $v; }
    public function setQId_sel(mixed $v): void { self::$recibido['id_sel'] = $v; }
    public function setQScroll_id(mixed $v): void { self::$recibido['scroll_id'] = $v; }
    public function setModo_curso(mixed $v): void { self::$recibido['modo_curso'] = $v; }
    public function setQId_activ(mixed $v): void { self::$recibido['id_activ'] = $v; }

    /** @return array<string, mixed> */
    public function getSegmentData(): array
    {
        return ['segment_tipo' => 'select_demo', 'tabla' => ['valores' => []]];
    }
}
PHP);
        }
        require_once $fichero;
        (self::WIDGET)::$recibido = [];
    }

    public function tearDown(): void
    {
        foreach ($this->originales as $id => $original) {
            $GLOBALS['container']->set($id, $original);
        }
        @unlink($this->raiz . '/src/fixdossier/application/Select_demo.php');
        @rmdir($this->raiz . '/src/fixdossier/application');
        @rmdir($this->raiz . '/src/fixdossier');
        @rmdir($this->raiz . '/src');
        @rmdir($this->raiz);
        parent::tearDown();
    }

    /**
     * Instancia una clase `final` dando un doble a cada dependencia del constructor.
     *
     * @template T of object
     * @param class-string<T> $clase
     * @param array<string, object> $fijos dobles concretos por nombre de parámetro
     * @return T
     */
    private function conDobles(string $clase, array $fijos): object
    {
        $args = [];
        foreach ((new \ReflectionClass($clase))->getConstructor()?->getParameters() ?? [] as $p) {
            $tipo = $p->getType();
            if (!$tipo instanceof \ReflectionNamedType) {
                self::fail('dependencia sin tipo simple en ' . $clase . '::' . $p->getName());
            }
            $args[] = $fijos[$p->getName()] ?? $this->createMock($tipo->getName());
        }

        return new $clase(...$args);
    }

    private function sut(): DossiersVerPantallaData
    {
        return new DossiersVerPantallaData(
            $this->tipos,
            $this->actividades,
            $this->conDobles(PersonaRepositoryResolver::class, ['personaNRepository' => $this->personasN]),
            $this->conDobles(UbiRepositoryResolver::class, ['centroDlRepository' => $this->centrosDl]),
            $this->lista,
            new DossierTipoFileSuffixResolver($this->raiz),
            new DossierFichaSelectRunner(),
        );
    }

    private function hayActividad(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('crt n Barcelona');
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    private function hayPersonaN(): void
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getNombreApellidos')->willReturn('Juan Pérez');
        $this->personasN->method('findById')->with(self::ID_NOM)->willReturn($p);
    }

    /** Tipos de dossier: 9001 = widget de prueba; 1022 = clase de datos (latín); 9002 = nada configurado. */
    private function hayTipos(): void
    {
        $this->tipos->method('findById')->willReturnCallback(function (int $id): ?TipoDossier {
            if (!in_array($id, [9001, 9002, 1022, 1004], true)) {
                return null;
            }
            $t = $this->createMock(TipoDossier::class);
            $t->method('getId_tipo_dossier')->willReturn($id === 1004 ? 1022 : $id);
            if ($id === 9001) {
                $t->method('getApp')->willReturn('fixdossier');
                $t->method('getCodigoVo')->willReturn(new TipoDossierCodigo('demo'));
            }

            return $t;
        });
    }

    /** @return array<string, mixed> */
    private static function actividadPost(): array
    {
        return ['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'obj_pau' => 'Actividad'];
    }

    /** @return array<string, mixed> */
    private static function personaPost(): array
    {
        return ['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN'];
    }

    // ---- cabecera ----------------------------------------------------------------------------------------

    public function test_un_pau_desconocido_devuelve_error(): void
    {
        $this->assertSame(['error' => 'pau desconocido', 'ficha_segmentos' => []], $this->sut()->build(['pau' => 'z', 'id_pau' => 1]));
        $this->assertSame(['error' => 'pau desconocido', 'ficha_segmentos' => []], $this->sut()->build([]));
    }

    public function test_actividad_cabecera_con_su_nombre_y_enlaces_sin_firmar(): void
    {
        $this->hayActividad();

        $top = $this->sut()->build(self::actividadPost())['top_data'];

        $this->assertSame('crt n Barcelona', $top['nom_cabecera']);
        $this->assertSame(ConfigGlobal::getWeb_icons(), $top['web_icons']);
        $this->assertSame([
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'obj_pau' => 'Actividad'],
        ], $top['go_dossiers_link_spec']);
        $this->assertSame([
            'path' => 'frontend/actividades/controller/actividad_ver.php',
            'query' => ['id_activ' => self::ID_ACTIV, 'obj_pau' => 'Actividad'],
        ], $top['go_home_link_spec']);
    }

    public function test_una_actividad_que_no_existe_devuelve_error(): void
    {
        $this->actividades->method('findById')->willReturn(null);

        $this->assertSame(['error' => 'actividad no encontrada', 'ficha_segmentos' => []], $this->sut()->build(self::actividadPost()));
    }

    public function test_persona_con_tipo_conocido_se_busca_en_su_repositorio(): void
    {
        $this->hayPersonaN();
        $this->finder->expects($this->never())->method('findPersonaEnGlobalODePaso');

        $top = $this->sut()->build(self::personaPost())['top_data'];

        $this->assertSame('Juan Pérez', $top['nom_cabecera']);
        $this->assertSame([
            'path' => 'frontend/personas/controller/home_persona.php',
            'query' => ['id_nom' => self::ID_NOM, 'obj_pau' => 'PersonaN'],
        ], $top['go_home_link_spec']);
    }

    public function test_una_persona_que_no_esta_en_su_repositorio_devuelve_error(): void
    {
        $this->personasN->method('findById')->willReturn(null);

        $data = $this->sut()->build(self::personaPost());

        $this->assertSame([], $data['ficha_segmentos']);
        $this->assertStringContainsString('No encuentro a nadie con id_nom: ' . self::ID_NOM, $data['error']);
    }

    /**
     * @dataProvider objPauGenericoProvider
     */
    public function test_persona_sin_tipo_se_busca_en_global_y_el_tipo_se_deduce_de_lo_encontrado(string $objPau): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidos')->willReturn('Ana Ruiz');
        $this->finder->expects($this->once())->method('findPersonaEnGlobalODePaso')->with(self::ID_NOM)->willReturn($p);

        $top = $this->sut()->build(['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => $objPau])['top_data'];

        $claseCorta = substr((string) strrchr('\\' . get_class($p), '\\'), 1);
        $this->assertSame('Ana Ruiz', $top['nom_cabecera']);
        $this->assertSame($claseCorta, $top['go_home_link_spec']['query']['obj_pau']);
        // Hallazgo: el enlace «ver dossiers» se monta antes de deducir el tipo y conserva el genérico.
        $this->assertSame($objPau, $top['go_dossiers_link_spec']['query']['obj_pau']);
    }

    /** @return array<string, array{string}> */
    public static function objPauGenericoProvider(): array
    {
        return ['vacío' => [''], 'Persona' => ['Persona']];
    }

    public function test_persona_sin_tipo_que_no_aparece_en_global_devuelve_error(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);

        $data = $this->sut()->build(['pau' => 'p', 'id_pau' => self::ID_NOM]);

        $this->assertStringContainsString('No encuentro a nadie con id_nom: ' . self::ID_NOM, $data['error']);
        // Hallazgo: el mensaje de error lleva la ruta del fichero PHP del servidor.
        $this->assertStringContainsString('DossiersVerPantallaData.php', $data['error']);
    }

    public function test_un_tipo_de_persona_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut()->build(['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaMarciana']);
    }

    public function test_ubi_cabecera_con_su_nombre(): void
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centrosDl->method('findById')->with(1003)->willReturn($c);

        $top = $this->sut()->build(['pau' => 'u', 'id_pau' => 1003, 'obj_pau' => 'CentroDl'])['top_data'];

        $this->assertSame('Centro A', $top['nom_cabecera']);
        $this->assertSame([
            'path' => 'frontend/ubis/controller/home_ubis.php',
            'query' => ['id_ubi' => 1003, 'obj_pau' => 'CentroDl'],
        ], $top['go_home_link_spec']);
    }

    public function test_una_ubi_que_no_existe_devuelve_error(): void
    {
        $this->centrosDl->method('findById')->willReturn(null);

        $this->assertSame(['error' => 'ubi no encontrada', 'ficha_segmentos' => []], $this->sut()->build(['pau' => 'u', 'id_pau' => 1003, 'obj_pau' => 'CentroDl']));
    }

    // ---- de quién es el dossier --------------------------------------------------------------------------

    public function test_la_fila_seleccionada_en_el_listado_anterior_decide_el_id(): void
    {
        $this->actividades->expects($this->once())->method('findById')->with(self::ID_ACTIV)->willReturn($this->createMock(ActividadAll::class));

        $this->sut()->build(['pau' => 'a', 'id_pau' => 5, 'sel' => [self::ID_ACTIV . '#otra#cosa']]);
    }

    public function test_al_refrescar_manda_el_id_pau_aunque_haya_seleccion(): void
    {
        $this->actividades->expects($this->once())->method('findById')->with(self::ID_ACTIV)->willReturn($this->createMock(ActividadAll::class));

        $this->sut()->build(['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'refresh' => 1, 'sel' => ['77#1']]);
    }

    /**
     * Tras eliminar o dar de alta, `sel` es la fila de la tabla del dossier, no la persona/actividad.
     *
     * @dataProvider modosQueDescartanSeleccionProvider
     */
    public function test_tras_eliminar_o_crear_se_ignora_la_seleccion(string $mod): void
    {
        $this->actividades->expects($this->once())->method('findById')->with(self::ID_ACTIV)->willReturn($this->createMock(ActividadAll::class));

        $this->sut()->build(['pau' => 'a', 'id_pau' => self::ID_ACTIV, 'mod' => $mod, 'sel' => ['77#1']]);
    }

    /** @return array<string, array{string}> */
    public static function modosQueDescartanSeleccionProvider(): array
    {
        return ['eliminar' => ['eliminar'], 'nuevo' => ['nuevo']];
    }

    // ---- lista -------------------------------------------------------------------------------------------

    public function test_sin_dossier_concreto_devuelve_la_relacion_de_dossiers(): void
    {
        $this->hayActividad();
        $this->lista->expects($this->once())->method('build')->with('a', self::ID_ACTIV, 'Actividad');

        $data = $this->sut()->build(self::actividadPost());

        $this->assertSame('lista', $data['modo']);
        $this->assertSame([['descripcion' => 'un dossier']], $data['lista_a_filas']);
        $this->assertSame([], $data['ficha_segmentos']);
        $this->assertArrayNotHasKey('error', $data);
        $this->assertArrayNotHasKey('aviso', $data);
    }

    /** Así vuelve la tabla de un dossier de datos tras guardar: solo con `clase_info`. */
    public function test_con_clase_info_y_sin_dossier_el_pau_y_el_dossier_los_dice_la_clase_de_datos(): void
    {
        $this->hayPersonaN();
        $this->hayTipos();
        $this->lista->expects($this->never())->method('build');

        $data = $this->sut()->build(['clase_info' => urlencode(InfoProfesorLatin::class), 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN']);

        $this->assertSame('ficha', $data['modo']);
        $this->assertSame(['ficha1022'], array_column($data['ficha_segmentos'], 'id'));
        $this->assertSame('p', $data['top_data']['go_dossiers_link_spec']['query']['pau']);
    }

    public function test_una_clase_info_que_no_existe_devuelve_error(): void
    {
        $this->assertSame(
            ['error' => 'clase_info invalida', 'ficha_segmentos' => []],
            $this->sut()->build(['clase_info' => urlencode('src\\noexiste\\Info'), 'id_pau' => 1])
        );
    }

    // ---- accesos directos (queSel) -----------------------------------------------------------------------

    /**
     * @dataProvider accesosProvider
     */
    public function test_los_accesos_directos_fijan_actividad_dossier_y_permiso_de_escritura(string $queSel, int $idDossier): void
    {
        $this->hayActividad();
        $pedidos = [];
        $this->tipos->method('findById')->willReturnCallback(function (int $id) use (&$pedidos): ?TipoDossier {
            $pedidos[] = $id;

            return null;
        });

        // llega como si fuera de una persona: el acceso directo lo convierte en actividad
        $data = $this->sut()->build(['pau' => 'p', 'id_pau' => self::ID_ACTIV, 'queSel' => $queSel]);

        $this->assertSame([$idDossier], $pedidos);
        $this->assertSame('a', $data['top_data']['go_dossiers_link_spec']['query']['pau']);
    }

    /** @return array<string, array{string, int}> */
    public static function accesosProvider(): array
    {
        return ['asistentes' => ['asis', 3101], 'asignaturas' => ['asig', 3005], 'cargos' => ['carg', 3102]];
    }

    public function test_que_se_acepta_como_sinonimo_de_queSel(): void
    {
        $this->hayActividad();
        $pedidos = [];
        $this->tipos->method('findById')->willReturnCallback(function (int $id) use (&$pedidos): ?TipoDossier {
            $pedidos[] = $id;

            return null;
        });

        $this->sut()->build(['id_pau' => self::ID_ACTIV, 'que' => 'carg']);

        $this->assertSame([3102], $pedidos);
    }

    public function test_el_acceso_activ_trata_el_id_como_persona(): void
    {
        $this->hayPersonaN();

        $data = $this->sut()->build(['pau' => 'a', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN', 'queSel' => 'activ']);

        $this->assertSame('Juan Pérez', $data['top_data']['nom_cabecera']);
    }

    // ---- fichas ------------------------------------------------------------------------------------------

    public function test_un_dossier_con_widget_da_su_segmento_y_el_widget_recibe_el_contexto(): void
    {
        $this->hayActividad();
        $this->hayTipos();

        $data = $this->sut()->build(self::actividadPost() + [
            'id_dossier' => '9001',
            'permiso' => '2',
            'queSel' => 'otro',
            'id_sel' => '10011#77',
            'scroll_id_select_demo' => '6',
        ]);

        $this->assertSame('ficha', $data['modo']);
        $this->assertSame([['tipo' => 'select_demo', 'id' => 'ficha9001', 'tabla' => ['valores' => []]]], $data['ficha_segmentos']);
        $this->assertSame([
            'id_dossier' => '9001',
            'pau' => 'a',
            'obj_pau' => 'Actividad',
            'id_pau' => self::ID_ACTIV,
            'permiso' => '2',
            'bloque' => '#ficha9001',
            'queSel' => 'otro',
            'id_sel' => '10011#77',
            'scroll_id' => 6,
        ], (self::WIDGET)::$recibido);
    }

    public function test_la_seleccion_y_el_scroll_restaurados_del_historial_prevalecen(): void
    {
        $this->hayActividad();
        $this->hayTipos();

        $this->sut()->build(self::actividadPost() + [
            'id_dossier' => '9001',
            'id_sel' => 'del-post',
            'scroll_id' => 3,
            'restored_id_sel' => ['del-historial'],
            'restored_scroll_id' => 9,
        ]);

        $this->assertSame('del-historial', (self::WIDGET)::$recibido['id_sel']);
        $this->assertSame(9, (self::WIDGET)::$recibido['scroll_id']);
    }

    public function test_un_dossier_de_datos_da_un_segmento_de_tabla_con_su_formulario(): void
    {
        $this->hayPersonaN();
        $this->hayTipos();
        $this->info->expects($this->once())->method('setId_pau')->with(self::ID_NOM);

        $data = $this->sut()->build(self::personaPost() + ['id_dossier' => '1022', 'permiso' => '3', 'id_sel' => 'abc', 'scroll_id' => 2]);

        $this->assertSame('ficha', $data['modo']);
        $this->assertCount(1, $data['ficha_segmentos']);
        $s = $data['ficha_segmentos'][0];
        $claseCodificada = urlencode(InfoProfesorLatin::class);
        $this->assertSame('datos_tabla', $s['tipo']);
        $this->assertSame('ficha1022', $s['id']);
        $this->assertSame('Latín', $s['titulo']);
        $this->assertSame(3, $s['permiso']);
        $this->assertNull($s['ins_traslado_link_spec']);
        $this->assertSame([
            'bloque' => '#ficha1022',
            'action_form' => 'frontend/shared/controller/tablaDB_formulario_ver.php',
            'action_update' => 'src/shared/tablaDB_update',
            'eliminar_txt' => '¿seguro?',
        ], $s['script_ctx']);
        $this->assertSame([
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['clase_info' => $claseCodificada, 'id_pau' => self::ID_NOM, 'bloque' => '#ficha1022', 'permiso' => '3', 'obj_pau' => 'PersonaN'],
        ], $s['action_tabla_link_spec']);
        $this->assertSame([
            'campos_form' => 'mod',
            'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
            'campos_hidden' => ['clase_info' => $claseCodificada, 'pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN', 'permiso' => '3', 'bloque' => '#ficha1022'],
        ], $s['hash']);
        $this->assertSame('datos_sql1022', $s['tabla']['id_tabla']);
        $this->assertCount(2, $s['tabla']['botones']);
        $this->assertSame(['select' => 'abc', 'scroll_id' => 2], $s['tabla']['valores'], 'sin filas: solo la selección y el scroll');
    }

    public function test_el_dossier_de_traslados_1004_añade_el_enlace_al_formulario_de_traslado(): void
    {
        $this->hayPersonaN();
        $this->hayTipos();

        $s = $this->sut()->build(self::personaPost() + ['id_dossier' => '1004'])['ficha_segmentos'][0];

        $this->assertSame([
            'path' => 'frontend/personas/controller/traslado_form.php',
            'query' => ['cabecera' => 'no', 'id_pau' => self::ID_NOM, 'id_dossier' => '1004', 'obj_pau' => 'PersonaN'],
        ], $s['ins_traslado_link_spec']);
    }

    public function test_varios_dossiers_separados_por_y_dan_un_segmento_cada_uno_y_se_saltan_los_no_disponibles(): void
    {
        $this->hayPersonaN();
        $this->hayTipos();

        // 8888 no existe como tipo; 9002 existe pero no tiene widget ni clase de datos
        $data = $this->sut()->build(self::personaPost() + ['id_dossier' => '9001y8888y9002y1022']);

        $this->assertSame(['ficha9001', 'ficha1022'], array_column($data['ficha_segmentos'], 'id'));
        $this->assertSame(['select_demo', 'datos_tabla'], array_column($data['ficha_segmentos'], 'tipo'));
    }

    public function test_si_ningun_dossier_pedido_esta_disponible_devuelve_error_con_la_cabecera(): void
    {
        $this->hayPersonaN();
        $this->hayTipos();

        $data = $this->sut()->build(self::personaPost() + ['id_dossier' => '8888y9002']);

        $this->assertStringContainsString('El dossier 8888y9002 no está disponible', $data['error']);
        $this->assertSame('Juan Pérez', $data['top_data']['nom_cabecera']);
        $this->assertSame([], $data['ficha_segmentos']);
        $this->assertArrayNotHasKey('modo', $data);
    }

    // ---- aviso de configuración de región ----------------------------------------------------------------

    public function test_un_fallo_de_configuracion_de_region_se_convierte_en_aviso_con_la_pantalla_vacia(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')
            ->willThrowException(new RegionStgrConfigException(RegionStgrAviso::TIPO_REGION_STGR_FALTA, 'dlx'));

        $data = $this->sut()->build(['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => '']);

        $this->assertStringContainsString('dlx', $data['aviso']);
        $this->assertSame('lista', $data['modo']);
        $this->assertSame([], $data['lista_a_filas']);
        $this->assertSame([], $data['ficha_segmentos']);
        $this->assertSame('', $data['top_data']['nom_cabecera']);
        $this->assertSame(['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => ''], $data['top_data']['go_dossiers_link_spec']['query']);
        $this->assertArrayNotHasKey('go_home_link_spec', $data['top_data']);
        $this->assertArrayNotHasKey('error', $data);
    }
}
