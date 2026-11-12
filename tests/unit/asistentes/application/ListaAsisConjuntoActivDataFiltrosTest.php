<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use src\actividades\domain\value_objects\StatusId;
use src\asistentes\application\ListaAsisConjuntoActivData;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\criteria\Criteria;
use Tests\support\DescribeCriteria;

/**
 * Filtros de la relación de asistentes de un conjunto de actividades: estado, tipo, casa, periodo, nombre,
 * «publicar» y dl organizadora (bloque de la dl y bloque de otras dl).
 */
final class ListaAsisConjuntoActivDataFiltrosTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $sesionPrevia = [];
    /** @var \ArrayObject<int, array{filtros: list<string>, orden: list<string>, sacd: bool, tipo: string}> */
    private \ArrayObject $listas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionPrevia = $_SESSION ?? [];
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv', 'sfsv' => 1, 'idioma' => 'es_ES.UTF-8'];
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new ConfigSnapshot(
            gesCalendario: null, ceLugar: null, regionLatin: null, vstgr: null, lugarFirma: null, dirStgr: null,
            ambito: 'dl', notaCorte: null, notaMax: null, caducaCursada: null, idiomaDefault: 'es_ES.UTF-8',
            iniContadorCertificados: null, jefeCalendario: null, aCursoStgr: $curso, aCursoCrt: $curso,
        );
        $this->listas = new \ArrayObject();
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesionPrevia;
        parent::tearDown();
    }

    /** @param array<string, mixed> $entrada */
    private function construir(array $entrada): string
    {
        $listas = $this->listas;
        $falsa = new class($listas) {
            private ?Criteria $criteria = null;
            private bool $sacd = false;
            private string $tipo = '';

            public function __construct(private \ArrayObject $listas)
            {
            }

            public function setMi_dele(string $miDele): void
            {
            }

            public function setCriteria(Criteria $criteria): void
            {
                $this->criteria = $criteria;
            }

            public function setId_tipo_activ(string $tipo): void
            {
                $this->tipo = $tipo;
            }

            public function setSacd(bool $sacd): void
            {
                $this->sacd = $sacd;
            }

            public function getLista(): object
            {
                $this->listas[] = [
                    'filtros' => DescribeCriteria::filtros($this->criteria),
                    'orden' => DescribeCriteria::orden($this->criteria),
                    'sacd' => $this->sacd,
                    'tipo' => $this->tipo,
                ];

                return new class {
                    public function listaPaginada(): string
                    {
                        return '[lista]';
                    }
                };
            }
        };
        $container = new class($falsa) implements ContainerInterface {
            public function __construct(private object $falsa)
            {
            }

            public function get(string $id): object
            {
                return $this->falsa;
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        return (new ListaAsisConjuntoActivData($container))->build($entrada)['content_html'];
    }

    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '1/1/2026', 'empiezamax' => '31/12/2026'];

    public function test_por_defecto_actividades_actuales_del_tipo_en_el_periodo_de_mi_dl_y_de_otras(): void
    {
        $html = $this->construir(['id_tipo_activ' => '112025'] + self::PERIODO);

        $this->assertSame(2, substr_count($html, '[lista]'));
        $this->assertSame(2, substr_count($html, '<h3>'));
        [$dl, $otras] = [$this->listas[0], $this->listas[1]];
        $comunes = ['status eq ' . StatusId::ACTUAL, 'id_tipo_activ matches ^112025', 'f_ini between [2026-01-01,2026-12-31]'];
        $this->assertSame(array_merge($comunes, ['dl_org eq dlb']), $dl['filtros']);
        $this->assertSame(array_merge($comunes, ['dl_org neq dlb']), $otras['filtros']);
        $this->assertSame(['f_ini asc'], $dl['orden']);
        $this->assertSame(['112025', '112025'], [$dl['tipo'], $otras['tipo']]);
    }

    public function test_todos_los_estados_no_filtra_por_estado(): void
    {
        $this->construir(['id_tipo_activ' => '112025', 'status' => (string) StatusId::ALL] + self::PERIODO);

        $this->assertStringStartsWith('id_tipo_activ', $this->listas[0]['filtros'][0]);
    }

    public function test_el_tipo_comodin_no_filtra_por_tipo(): void
    {
        $this->construir(['id_tipo_activ' => '......'] + self::PERIODO);

        $this->assertStringStartsWith('f_ini between', $this->listas[0]['filtros'][1]);
    }

    /**
     * @return iterable<string, array{array<string, string>, string}>
     */
    public static function tipoPorTextosProvider(): iterable
    {
        yield 'sin nada: todo lo de mi sección' => [[], '1.....'];
        yield 'sv, n, ca' => [['sfsv' => 'sv', 'sasistentes' => 'n', 'sactividad' => 'ca'], '112...'];
    }

    #[DataProvider('tipoPorTextosProvider')]
    public function test_sin_id_de_tipo_se_compone_con_los_textos_del_formulario(array $textos, string $tipo): void
    {
        $this->construir($textos + self::PERIODO);

        $this->assertSame($tipo, $this->listas[0]['tipo']);
        $this->assertSame('id_tipo_activ matches ^' . $tipo, $this->listas[0]['filtros'][1]);
    }

    public function test_filtros_opcionales_de_casa_nombre_y_publicar(): void
    {
        $this->construir(['id_tipo_activ' => '......', 'id_ubi' => '1077', 'nom_activ' => 'ca (julio)', 'modo' => 'publicar'] + self::PERIODO);

        $filtros = $this->listas[0]['filtros'];
        $this->assertContains('id_ubi eq 1077', $filtros);
        $this->assertContains('nom_activ matches_insensitive ca \(julio\)', $filtros, 'el texto se busca literal');
        $this->assertContains('publicado eq false', $filtros);
    }

    public function test_desde_hoy_filtra_por_fecha_de_fin(): void
    {
        $this->construir(['id_tipo_activ' => '......', 'periodo' => 'desdeHoy']);

        // `Periodo` da estas fechas con «/» (Y/m/d); PostgreSQL las acepta igual.
        $this->assertStringStartsWith('f_fin between [' . date('Y/m/d'), $this->listas[0]['filtros'][1]);
    }

    public function test_con_mi_dl_como_organizadora_solo_sale_el_bloque_de_la_dl(): void
    {
        $html = $this->construir(['id_tipo_activ' => '......', 'dl_org' => 'dlb'] + self::PERIODO);

        $this->assertCount(1, $this->listas);
        $this->assertSame(1, substr_count($html, '<h3>'));
        $this->assertContains('dl_org eq dlb', $this->listas[0]['filtros']);
    }

    public function test_con_otra_dl_como_organizadora_solo_sale_el_bloque_de_otras_con_esa_dl(): void
    {
        $this->construir(['id_tipo_activ' => '......', 'dl_org' => 'dlp'] + self::PERIODO);

        $this->assertCount(1, $this->listas);
        $this->assertContains('dl_org eq dlp', $this->listas[0]['filtros']);
    }

    /**
     * Hallazgo: con «solo sacd» (`list_cjto_sacd`) el bloque de otras dl muestra a todos los asistentes: al clonar
     * la lista de la dl se pone `setSacd(false)` expresamente.
     */
    public function test_solo_sacd_filtra_el_bloque_de_la_dl_pero_no_el_de_otras(): void
    {
        $this->construir(['que' => 'list_cjto_sacd', 'id_tipo_activ' => '......'] + self::PERIODO);

        $this->assertSame([true, false], [$this->listas[0]['sacd'], $this->listas[1]['sacd']]);
    }
}
