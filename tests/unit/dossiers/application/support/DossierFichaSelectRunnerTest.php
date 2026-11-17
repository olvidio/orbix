<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application\support;

use src\dossiers\application\support\DossierFichaSelectRunner;
use src\profesores\domain\InfoProfesorLatin;
use src\shared\domain\DatosInfoRepo;
use Tests\myTest;

/** Widget de mentira con todos los setters que conoce el runner; anota cada llamada. */
class WidgetSegmentoDePrueba
{
    /** @var list<array{string, mixed}> */
    public static array $llamadas = [];
    /** @var mixed lo que devuelve getSegmentData */
    public static mixed $segmento = ['segment_tipo' => 'select_demo'];

    public function setId_dossier(mixed $v): void { self::$llamadas[] = ['setId_dossier', $v]; }
    public function setPau(mixed $v): void { self::$llamadas[] = ['setPau', $v]; }
    public function setObj_pau(mixed $v): void { self::$llamadas[] = ['setObj_pau', $v]; }
    public function setId_pau(mixed $v): void { self::$llamadas[] = ['setId_pau', $v]; }
    public function setPermiso(mixed $v): void { self::$llamadas[] = ['setPermiso', $v]; }
    public function setBloque(mixed $v): void { self::$llamadas[] = ['setBloque', $v]; }
    public function setQueSel(mixed $v): void { self::$llamadas[] = ['setQueSel', $v]; }
    public function setStackActual(mixed $v): void { self::$llamadas[] = ['setStackActual', $v]; }
    public function setQId_sel(mixed $v): void { self::$llamadas[] = ['setQId_sel', $v]; }
    public function setQScroll_id(mixed $v): void { self::$llamadas[] = ['setQScroll_id', $v]; }
    public function setModo_curso(mixed $v): void { self::$llamadas[] = ['setModo_curso', $v]; }
    public function setQId_activ(mixed $v): void { self::$llamadas[] = ['setQId_activ', $v]; }
    public function setTodos(mixed $v): void { self::$llamadas[] = ['setTodos', $v]; }

    public function getSegmentData(): mixed
    {
        return self::$segmento;
    }
}

/** Widget antiguo: solo sabe pintarse. */
class WidgetHtmlDePrueba
{
    public function getHtml(): string
    {
        return '<p>widget</p>';
    }
}

/** Clase que no es un widget. */
class WidgetMudoDePrueba
{
}

/** Cómo se configura y se invoca el widget de un dossier, y cómo se resuelve su clase de datos. */
final class DossierFichaSelectRunnerTest extends myTest
{
    public function setUp(): void
    {
        parent::setUp();
        WidgetSegmentoDePrueba::$llamadas = [];
        WidgetSegmentoDePrueba::$segmento = ['segment_tipo' => 'select_demo'];
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>|null
     */
    private function segmento(
        string $fqcn = WidgetSegmentoDePrueba::class,
        string $idDossier = '3102',
        mixed $idSel = '',
        int $scrollId = 0,
        int $idActiv = 0,
        int $modoCurso = 0,
        array $post = [],
    ): ?array {
        return (new DossierFichaSelectRunner())->buildSelectSegment(
            $fqcn,
            'ficha' . $idDossier,
            $idDossier,
            'a',
            'Actividad',
            3001,
            '3',
            '#ficha' . $idDossier,
            'carg',
            '7',
            $idSel,
            $scrollId,
            $idActiv,
            $modoCurso,
            $post,
        );
    }

    /** @return array<string, mixed> setter => valor recibido */
    private function llamadas(): array
    {
        return array_column(WidgetSegmentoDePrueba::$llamadas, 1, 0);
    }

    // ---- buildSelectSegment ------------------------------------------------------------------------------

    public function test_una_clase_que_no_existe_no_da_segmento(): void
    {
        $this->assertNull($this->segmento('src\\noexiste\\application\\Select_nada'));
    }

    public function test_una_clase_sin_getSegmentData_ni_getHtml_no_da_segmento(): void
    {
        $this->assertNull($this->segmento(WidgetMudoDePrueba::class));
    }

    public function test_el_widget_recibe_el_contexto_del_dossier(): void
    {
        $this->segmento(post: ['stack_actual' => '4']);

        $this->assertSame([
            'setId_dossier' => '3102',
            'setPau' => 'a',
            'setObj_pau' => 'Actividad',
            'setId_pau' => 3001,
            'setPermiso' => '3',
            'setBloque' => '#ficha3102',
            'setQueSel' => 'carg',
            'setStackActual' => 4,
        ], $this->llamadas());
    }

    public function test_el_stack_actual_es_cero_si_no_llega_o_no_es_numerico(): void
    {
        $this->segmento();
        $this->assertSame(0, $this->llamadas()['setStackActual']);

        WidgetSegmentoDePrueba::$llamadas = [];
        $this->segmento(post: ['stack_actual' => 'x']);
        $this->assertSame(0, $this->llamadas()['setStackActual']);
    }

    /**
     * @dataProvider seleccionesProvider
     */
    public function test_la_fila_seleccionada_solo_se_pasa_si_trae_algo(mixed $idSel, bool $sePasa): void
    {
        $this->segmento(idSel: $idSel);

        $this->assertSame($sePasa, array_key_exists('setQId_sel', $this->llamadas()));
        if ($sePasa) {
            $this->assertSame($idSel, $this->llamadas()['setQId_sel']);
        }
    }

    /** @return array<string, array{mixed, bool}> */
    public static function seleccionesProvider(): array
    {
        return [
            'vacío' => ['', false],
            'nulo' => [null, false],
            'lista vacía' => [[], false],
            'texto' => ['10011#77', true],
            'lista' => [['10011#77'], true],
            'cero' => [0, true],
        ];
    }

    public function test_el_scroll_solo_se_pasa_si_es_positivo(): void
    {
        $this->segmento(scrollId: 0);
        $this->assertArrayNotHasKey('setQScroll_id', $this->llamadas());

        $this->segmento(scrollId: 12);
        $this->assertSame(12, $this->llamadas()['setQScroll_id']);
    }

    /**
     * @dataProvider dossiersDeCursoProvider
     */
    public function test_solo_los_dossiers_1301_y_1302_reciben_el_modo_de_curso(string $idDossier, bool $recibe): void
    {
        $this->segmento(idDossier: $idDossier, modoCurso: 2);

        $this->assertSame($recibe, array_key_exists('setModo_curso', $this->llamadas()));
    }

    /** @return array<string, array{string, bool}> */
    public static function dossiersDeCursoProvider(): array
    {
        return ['1301' => ['1301', true], '1302' => ['1302', true], '1303' => ['1303', false], '3102' => ['3102', false]];
    }

    public function test_el_dossier_1303_recibe_la_actividad_si_la_hay_y_el_indicador_todos(): void
    {
        $this->segmento(idDossier: '1303', idActiv: 3001, post: ['todos' => '1']);
        $this->assertSame(3001, $this->llamadas()['setQId_activ']);
        $this->assertSame('1', $this->llamadas()['setTodos']);

        WidgetSegmentoDePrueba::$llamadas = [];
        $this->segmento(idDossier: '1303');
        $this->assertArrayNotHasKey('setQId_activ', $this->llamadas());
        $this->assertArrayHasKey('setTodos', $this->llamadas());
        $this->assertNull($this->llamadas()['setTodos']);
    }

    public function test_el_segmento_de_datos_lleva_el_tipo_que_declara_el_widget_y_el_id_del_bloque(): void
    {
        WidgetSegmentoDePrueba::$segmento = ['segment_tipo' => 'select_demo', 'tabla' => ['valores' => [1]], 'msg_err' => ''];

        $this->assertSame(
            ['tipo' => 'select_demo', 'id' => 'ficha3102', 'tabla' => ['valores' => [1]], 'msg_err' => ''],
            $this->segmento()
        );
    }

    /** El widget puede pisar `tipo` e `id` si los devuelve en su payload: se mezclan después. */
    public function test_el_payload_del_widget_prevalece_sobre_tipo_e_id(): void
    {
        WidgetSegmentoDePrueba::$segmento = ['segment_tipo' => 'select_demo', 'id' => 'otro'];

        $this->assertSame(['tipo' => 'select_demo', 'id' => 'otro'], $this->segmento());
    }

    /**
     * Un widget con getSegmentData que no declara `segment_tipo` (o lo declara mal, o no devuelve un array) es un
     * error de programación: se avisa con el nombre de la clase (antes se suponía que era el de habitaciones).
     *
     * @dataProvider segmentosSinTipoProvider
     */
    public function test_sin_tipo_declarado_es_un_error_con_el_nombre_del_widget(mixed $segmento): void
    {
        WidgetSegmentoDePrueba::$segmento = $segmento;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('WidgetSegmentoDePrueba');
        $this->segmento();
    }

    /** @return array<string, array{mixed}> */
    public static function segmentosSinTipoProvider(): array
    {
        return [
            'sin segment_tipo' => [['tabla' => []]],
            'no es un array' => ['no es un array'],
            'segment_tipo no es texto' => [['segment_tipo' => 42]],
            'segment_tipo vacío' => [['segment_tipo' => '']],
        ];
    }

    /** Guardia: todos los widgets de dossier con getSegmentData declaran su `segment_tipo`. */
    public function test_todos_los_widgets_con_segmento_declaran_su_tipo(): void
    {
        $raiz = dirname(__DIR__, 5) . '/src';
        $sinTipo = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f->getExtension() !== 'php') {
                continue;
            }
            $codigo = (string) file_get_contents($f->getPathname());
            if (str_contains($codigo, 'function getSegmentData') && !str_contains($codigo, "'segment_tipo' =>")) {
                $sinTipo[] = substr($f->getPathname(), strlen($raiz) + 1);
            }
        }
        $this->assertSame([], $sinTipo);
    }

    public function test_un_widget_que_solo_sabe_pintarse_da_un_segmento_html(): void
    {
        $this->assertSame(
            ['tipo' => 'html', 'id' => 'ficha3102', 'html' => '<p>widget</p>'],
            $this->segmento(WidgetHtmlDePrueba::class)
        );
    }

    // ---- resolveDatosInfo --------------------------------------------------------------------------------

    public function test_la_clase_de_datos_se_instancia_con_la_persona_y_se_devuelve_codificada_para_url(): void
    {
        $r = (new DossierFichaSelectRunner())->resolveDatosInfo(InfoProfesorLatin::class, 10011, 'PersonaN');

        $this->assertInstanceOf(InfoProfesorLatin::class, $r['info']);
        $this->assertInstanceOf(DatosInfoRepo::class, $r['info']);
        $this->assertSame(urlencode(InfoProfesorLatin::class), $r['clase_info_encoded']);
        $this->assertSame(InfoProfesorLatin::class, urldecode($r['clase_info_encoded']));
        $this->assertStringNotContainsString('\\', $r['clase_info_encoded']);
        $this->assertSame(10011, (int) (new \ReflectionProperty(DatosInfoRepo::class, 'id_pau'))->getValue($r['info']));
    }

    public function test_una_clase_que_no_es_de_datos_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(WidgetMudoDePrueba::class);

        (new DossierFichaSelectRunner())->resolveDatosInfo(WidgetMudoDePrueba::class, 1, 'PersonaN');
    }

    public function test_una_clase_que_no_existe_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);

        (new DossierFichaSelectRunner())->resolveDatosInfo('src\\noexiste\\Info', 1, 'PersonaN');
    }
}
