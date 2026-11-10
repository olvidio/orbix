<?php

declare(strict_types=1);

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\shared\domain\DatosCampo;
use Tests\myTest;

/**
 * Trait {@see \src\actividades\domain\entity\ActividadAllDatosCampos}, probado a través de ActividadAll.
 */
final class ActividadAllDatosCamposTest extends myTest
{
    private const CAMPOS_EN_ORDEN = [
        'id_tipo_activ', 'dl_org', 'nom_activ', 'id_ubi', 'f_ini', 'h_ini', 'f_fin', 'h_fin', 'precio',
        'num_asistentes', 'status', 'observ', 'nivel_stgr', 'observ_material', 'lugar_esp', 'tarifa',
        'id_repeticion', 'id_tabla', 'plazas', 'publicado',
    ];

    /** @var array<string, DatosCampo> */
    private array $campos = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->campos = [];
        foreach ((new ActividadAll())->getDatosCampos() as $c) {
            $this->campos[(string) $c->getNom_camp()] = $c;
        }
    }

    public function test_devuelve_los_campos_esperados_en_orden_y_sin_duplicados(): void
    {
        $lista = (new ActividadAll())->getDatosCampos();

        $this->assertContainsOnlyInstancesOf(DatosCampo::class, $lista);
        $this->assertSame(self::CAMPOS_EN_ORDEN, array_map(static fn(DatosCampo $c) => $c->getNom_camp(), $lista));
    }

    public function test_todos_apuntan_a_a_actividades_all_y_tienen_etiqueta(): void
    {
        foreach ($this->campos as $nombre => $c) {
            $this->assertSame('a_actividades_all', $c->getNom_tabla(), $nombre);
            $this->assertNotEmpty($c->getEtiqueta(), "etiqueta de $nombre");
        }
    }

    /**
     * @dataProvider camposNoAvisablesProvider
     */
    public function test_campos_que_no_generan_aviso(string $campo): void
    {
        $this->assertFalse($this->campos[$campo]->getAviso(), $campo);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function camposNoAvisablesProvider(): iterable
    {
        foreach (['id_tipo_activ', 'dl_org', 'num_asistentes', 'id_tabla'] as $c) {
            yield $c => [$c];
        }
    }

    public function test_el_resto_de_campos_generan_aviso_por_defecto(): void
    {
        $noAvisables = ['id_tipo_activ', 'dl_org', 'num_asistentes', 'id_tabla'];
        foreach ($this->campos as $nombre => $c) {
            if (!in_array($nombre, $noAvisables, true)) {
                $this->assertTrue($c->getAviso(), $nombre);
            }
        }
    }

    public function test_fechas_tienen_tipo_fecha(): void
    {
        $this->assertSame('fecha', $this->campos['f_ini']->getTipo());
        $this->assertSame('fecha', $this->campos['f_fin']->getTipo());
        $this->assertNull($this->campos['h_ini']->getTipo());
        $this->assertNull($this->campos['nom_activ']->getTipo());
    }

    public function test_status_es_un_array_con_la_lista_de_status(): void
    {
        $status = $this->campos['status'];

        $this->assertSame('array', $status->getTipo());
        $this->assertSame(StatusId::getArrayStatus(true), $status->getLista());
        $this->assertNotEmpty($status->getLista());
    }

    /**
     * @dataProvider preciosValidosProvider
     */
    public function test_precio_acepta_importes_validos(string $valor): void
    {
        $this->assertSame(1, preg_match($this->campos['precio']->getRegExp(), $valor), $valor);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function preciosValidosProvider(): iterable
    {
        yield 'entero' => ['1254'];
        yield 'con punto y dos decimales' => ['1254.56'];
        yield 'con coma' => ['1254,56'];
        yield 'un decimal' => ['10.5'];
        yield 'separador sin decimales' => ['10.'];
        yield 'cero' => ['0'];
    }

    /**
     * @dataProvider preciosInvalidosProvider
     */
    public function test_precio_rechaza_importes_invalidos(string $valor): void
    {
        $this->assertSame(0, preg_match($this->campos['precio']->getRegExp(), $valor), $valor);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function preciosInvalidosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'tres decimales' => ['10.123'];
        yield 'separador de miles' => ['1.254,56'];
        yield 'negativo' => ['-5'];
        yield 'letras' => ['abc'];
        yield 'dos separadores' => ['1.2.3'];
        yield 'espacio' => ['10 '];
        yield 'solo separador' => ['.5'];
    }

    public function test_precio_explica_el_formato_en_el_mensaje_de_error(): void
    {
        $txt = (string) $this->campos['precio']->getRegExpText();

        $this->assertStringContainsString('1254.56', $txt);
        $this->assertGreaterThan(1, substr_count($txt, "\n"));
    }

    public function test_solo_precio_tiene_expresion_regular(): void
    {
        foreach ($this->campos as $nombre => $c) {
            if ($nombre !== 'precio') {
                $this->assertNull($c->getRegExp(), $nombre);
            }
        }
    }
}
