<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\application\DossiersListaFichasData;
use src\dossiers\application\DossierTipoFileSuffixResolver;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Relación de dossiers de una persona, actividad o ubi (modo lista de dossiers_ver). */
final class DossiersListaFichasDataTest extends myTest
{
    private const ID_NOM = 10011;
    /** Tipo sin `app` que se puede pintar: su clase de datos se resuelve por id (profesores). */
    private const ID_LATIN = 1022;
    private const ID_CURRICULUM = 1017;

    private TipoDossierRepositoryInterface&MockObject $tipos;
    private DossierRepositoryInterface&MockObject $dossiers;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'iPermMenus', 'config'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->ambito('dl');
        $_SESSION['iPermMenus'] = 0;
        $this->appsInstaladas([]);
        $this->tipos = $this->createMock(TipoDossierRepositoryInterface::class);
        $this->dossiers = $this->createMock(DossierRepositoryInterface::class);
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
        parent::tearDown();
    }

    private function ambito(string $ambito): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
    }

    /** @param list<string> $apps */
    private function appsInstaladas(array $apps): void
    {
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $todas = ['actividadcargos' => 901, 'ubiscamas' => 902, 'profesores' => 903];
        $config['a_apps'] = $todas;
        $config['app_installed'] = array_values(array_intersect_key($todas, array_flip($apps)));
        $_SESSION['config'] = $config;
    }

    private function sut(): DossiersListaFichasData
    {
        return new DossiersListaFichasData($this->tipos, $this->dossiers, DossierTipoFileSuffixResolver::fromDefaultProjectRoot());
    }

    private function tipo(
        int $id,
        string $descripcion = 'un dossier',
        ?string $app = null,
        ?string $codigo = null,
        ?string $tablaTo = 'd_tabla',
        int $lectura = 16,
        ?int $escritura = 16,
        bool $depende = false,
    ): TipoDossier {
        $t = $this->createMock(TipoDossier::class);
        $t->method('getId_tipo_dossier')->willReturn($id);
        $t->method('getDescripcion')->willReturn($descripcion);
        $t->method('getTabla_to')->willReturn($tablaTo);
        $t->method('getPermiso_lectura')->willReturn($lectura);
        $t->method('getPermiso_escritura')->willReturn($escritura);
        $t->method('isDepende_modificar')->willReturn($depende);
        $t->method('getApp')->willReturn($app);
        $t->method('getCodigo')->willReturn($codigo);
        $t->method('getCodigoVo')->willReturn(null);
        $t->method('getDbVo')->willReturn(null);
        $t->method('getClass')->willReturn(null);

        return $t;
    }

    /** @param list<TipoDossier> $tipos */
    private function hayTipos(array $tipos): void
    {
        $this->tipos->method('matching')->willReturn($tipos);
    }

    // ---- consulta ----------------------------------------------------------------------------------------

    public function test_los_tipos_se_piden_por_la_tabla_de_origen_y_ordenados_por_descripcion(): void
    {
        $descripcion = [];
        $this->tipos->expects($this->once())->method('matching')->willReturnCallback(function (Criteria $c) use (&$descripcion): array {
            $descripcion = [DescribeCriteria::filtros($c), DescribeCriteria::orden($c)];

            return [];
        });

        $data = $this->sut()->build('p', self::ID_NOM, 'PersonaN');

        $this->assertSame(['tabla_from eq p'], $descripcion[0]);
        $this->assertCount(1, $descripcion[1]);
        $this->assertStringStartsWith('descripcion ', $descripcion[1][0]);
        $this->assertSame(['a_filas' => [], 'web_icons' => ConfigGlobal::getWeb_icons()], $data);
    }

    // ---- qué tipos se muestran ---------------------------------------------------------------------------

    public function test_un_tipo_sin_widget_ni_clase_de_datos_no_se_lista(): void
    {
        $this->hayTipos([$this->tipo(9999), $this->tipo(self::ID_LATIN, 'latín')]);

        $filas = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'];

        $this->assertSame(['latín'], array_column($filas, 'descripcion'));
    }

    public function test_un_tipo_de_una_app_no_instalada_no_se_lista(): void
    {
        $this->hayTipos([$this->tipo(self::ID_LATIN, 'latín', 'profesores')]);

        $this->assertSame([], $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas']);

        $this->appsInstaladas(['profesores']);
        $this->assertCount(1, $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas']);
    }

    /**
     * @dataProvider habitacionesProvider
     */
    public function test_el_dossier_de_habitaciones_depende_de_ubiscamas_aunque_su_app_sea_otra(int $id, ?string $codigo, ?string $tablaTo): void
    {
        // se da por instalada la app que declara el tipo, pero no ubiscamas
        $this->appsInstaladas(['profesores']);
        $this->hayTipos([$this->tipo($id, 'habitaciones', 'profesores', $codigo, $tablaTo)]);
        $this->dossiers->expects($this->never())->method('findByPk');

        $this->assertSame([], $this->sut()->build('u', 2001, 'CasaDl')['a_filas']);
    }

    /** @return array<string, array{int, ?string, ?string}> */
    public static function habitacionesProvider(): array
    {
        return [
            'por id 2006' => [2006, null, 'otra'],
            'por código' => [self::ID_LATIN, 'habitaciones_cdc', 'otra'],
            'por tabla destino' => [self::ID_LATIN, null, 'du_habitaciones_dl'],
        ];
    }

    public function test_un_widget_select_de_una_app_instalada_se_lista(): void
    {
        $this->appsInstaladas(['actividadcargos']);
        $t = $this->createMock(TipoDossier::class);
        $t->method('getId_tipo_dossier')->willReturn(3102);
        $t->method('getDescripcion')->willReturn('cargos');
        $t->method('getApp')->willReturn('actividadcargos');
        $t->method('getCodigo')->willReturn('cargos_de_actividad');
        $t->method('getCodigoVo')->willReturn(new \src\dossiers\domain\value_objects\TipoDossierCodigo('cargos_de_actividad'));
        $t->method('getPermiso_lectura')->willReturn(0);
        $this->hayTipos([$t]);

        $this->assertSame(['cargos'], array_column($this->sut()->build('a', 3001, 'Actividad')['a_filas'], 'descripcion'));
    }

    // ---- filas -------------------------------------------------------------------------------------------

    public function test_la_carpeta_sale_abierta_solo_si_el_dossier_de_la_persona_esta_activo(): void
    {
        $this->hayTipos([$this->tipo(self::ID_LATIN, 'a'), $this->tipo(self::ID_CURRICULUM, 'b'), $this->tipo(1018, 'c')]);
        $pks = [];
        $this->dossiers->method('findByPk')->willReturnCallback(function (DossierPk $pk) use (&$pks): ?Dossier {
            $pks[] = [$pk->tabla(), $pk->idPau(), $pk->idTipoDossier()];
            if ($pk->idTipoDossier() === 1018) {
                return null; // nunca se ha abierto
            }
            $d = $this->createMock(Dossier::class);
            $d->method('isActive')->willReturn($pk->idTipoDossier() === self::ID_LATIN);

            return $d;
        });

        $filas = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'];

        $iconos = ConfigGlobal::getWeb_icons();
        $this->assertSame(
            [$iconos . '/folder.open.gif', $iconos . '/folder.gif', $iconos . '/folder.gif'],
            array_column($filas, 'imagen')
        );
        $this->assertSame([['p', self::ID_NOM, self::ID_LATIN], ['p', self::ID_NOM, self::ID_CURRICULUM], ['p', self::ID_NOM, 1018]], $pks);
        $this->assertSame(['par', 'imp', 'par'], array_column($filas, 'clase'));
    }

    public function test_en_ambito_rstgr_no_se_consulta_el_estado_y_todas_salen_cerradas(): void
    {
        $this->ambito('rstgr');
        $this->hayTipos([$this->tipo(self::ID_LATIN)]);
        $this->dossiers->expects($this->never())->method('findByPk');

        $filas = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'];

        $this->assertSame(ConfigGlobal::getWeb_icons() . '/folder.gif', $filas[0]['imagen']);
    }

    /** Las filas se numeran solo con los tipos visibles: el zebra no salta por los omitidos. */
    public function test_los_tipos_omitidos_no_dejan_hueco_en_la_numeracion(): void
    {
        $this->hayTipos([$this->tipo(self::ID_LATIN, 'a'), $this->tipo(9999, 'x'), $this->tipo(self::ID_CURRICULUM, 'b')]);

        $filas = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'];

        $this->assertSame([0, 1], array_keys($filas));
        $this->assertSame(['par', 'imp'], array_column($filas, 'clase'));
    }

    /**
     * @dataProvider permisosProvider
     */
    public function test_el_permiso_de_cada_fila_sale_de_los_bits_del_usuario(int $bitsUsuario, int $lectura, ?int $escritura, int $esperado): void
    {
        $_SESSION['iPermMenus'] = $bitsUsuario;
        $this->hayTipos([$this->tipo(self::ID_LATIN, lectura: $lectura, escritura: $escritura)]);

        $fila = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'][0];

        $this->assertSame($esperado, $fila['perm_a']);
        $this->assertSame($esperado, $fila['href_ver_link_spec']['query']['permiso']);
        $this->assertSame($esperado, $fila['href_abrir_link_spec']['query']['permiso']);
    }

    /** @return array<string, array{int, int, ?int, int}> */
    public static function permisosProvider(): array
    {
        return [
            'sin permiso' => [8, 16, 16, 1],
            'solo lectura' => [16, 16, 128, 2],
            'escritura' => [16, 16, 16, 3],
            'escritura sin definir se trata como 0' => [16, 16, null, 2],
        ];
    }

    public function test_cada_fila_lleva_los_enlaces_de_ver_y_abrir_sin_firmar(): void
    {
        $_SESSION['iPermMenus'] = 16;
        // `depende` obligaría a buscar a la persona; con permiso de solo lectura no se llega a hacer
        $this->hayTipos([
            $this->tipo(self::ID_LATIN, 'latín', tablaTo: 'd_profesor_latin', escritura: 128, depende: true),
            $this->tipo(self::ID_CURRICULUM, 'currículum', tablaTo: null),
        ]);

        $filas = $this->sut()->build('p', self::ID_NOM, 'PersonaN')['a_filas'];

        $this->assertSame([
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN', 'id_dossier' => self::ID_LATIN, 'permiso' => 2, 'depende' => true],
        ], $filas[0]['href_ver_link_spec']);
        $this->assertSame([
            'path' => 'frontend/dossiers/controller/dossier_abrir.php',
            'query' => ['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN', 'id_dossier' => self::ID_LATIN, 'tabla_to' => 'd_profesor_latin', 'permiso' => 2],
        ], $filas[0]['href_abrir_link_spec']);
        $this->assertSame('', $filas[1]['href_abrir_link_spec']['query']['tabla_to'], 'sin tabla destino va vacío');
        $this->assertSame(['imagen', 'clase', 'descripcion', 'href_ver_link_spec', 'href_abrir_link_spec', 'perm_a'], array_keys($filas[0]));
    }
}
