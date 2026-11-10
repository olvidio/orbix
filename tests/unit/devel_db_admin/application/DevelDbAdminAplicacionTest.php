<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\entity\App;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;
use src\devel_db_admin\application\AbsorberEsquemaResult;
use src\devel_db_admin\application\ApptablesAppsData;
use src\devel_db_admin\application\ApptablesUpdate;
use src\devel_db_admin\application\CopiarEsquema;
use src\devel_db_admin\application\CrearEsquema;
use src\devel_db_admin\application\CrearEsquemaPrecondicionException;
use src\devel_db_admin\application\CrearUsuarios;
use src\devel_db_admin\application\CrearUsuariosResult;
use src\devel_db_admin\application\DbLugarDropdown;
use src\devel_db_admin\application\DbPropiedadesFormData;
use src\devel_db_admin\application\MigracionesEjecutar;
use src\devel_db_admin\application\MigracionesQuitarRegistro;
use src\devel_db_admin\application\MoverTablaResult;
use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\ubis\application\services\RegionDropdown;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Herramientas de administración de BD (devel_db_admin). Solo se prueba lo que no toca esquemas reales:
 * validaciones previas, normalización de parámetros, desplegables y lectura de migraciones con ficheros temporales.
 * Crear/copiar/renombrar/eliminar/absorber esquemas, crear usuarios y mover tablas ejecutan DDL o escriben
 * ficheros desde el primer paso y se dejan para una BD de pruebas aislada (ver plan).
 */
final class DevelDbAdminAplicacionTest extends myTest
{
    private mixed $configAnterior;
    private string $tmpDir = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['config'] ?? null;
        $this->tmpDir = sys_get_temp_dir() . '/orbix_dbadmin_' . uniqid('', true);
        mkdir($this->tmpDir);
    }

    public function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*') ?: [] as $f) {
            unlink($f);
        }
        if (is_dir($this->tmpDir)) {
            rmdir($this->tmpDir);
        }
        if ($this->configAnterior === null) {
            unset($_SESSION['config']);
        } else {
            $_SESSION['config'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    // ---- resultados ----------------------------------------------------------------------

    public function test_los_resultados_guardan_lo_recibido_con_valores_por_defecto(): void
    {
        $this->assertSame([[], []], [(new AbsorberEsquemaResult())->lines, (new AbsorberEsquemaResult())->errores]);
        $this->assertSame('', (new MoverTablaResult())->fatalError);
        $this->assertSame(['x'], (new MoverTablaResult('', ['x']))->lines);
        $u = new CrearUsuariosResult('a', 'p1', 'av', 'p2', 'af', 'p3');
        $this->assertSame(['a', 'p1', 'av', 'p2', 'af', 'p3'], [$u->esquema, $u->esquemaPwd, $u->esquemav, $u->esquemavPwd, $u->esquemaf, $u->esquemafPwd]);
    }

    // ---- apps y desplegables ----------------------------------------------------------------------

    public function test_la_lista_de_apps_es_id_a_nombre(): void
    {
        $a = $this->createMock(App::class);
        $a->method('getIdAppVo')->willReturn(new AppId(3));
        $a->method('getNomVo')->willReturn(AppName::fromString('procesos'));
        $repo = $this->createMock(AppRepositoryInterface::class);
        $repo->method('matching')->willReturn([$a]);

        $this->assertSame(['a_apps' => [3 => 'procesos']], (new ApptablesAppsData($repo))->build());
    }

    private function delegacion(?string $dl): Delegacion&MockObject
    {
        $d = $this->createMock(Delegacion::class);
        $d->method('getDlVo')->willReturn(new DelegacionCode($dl));

        return $d;
    }

    public function test_sin_region_el_desplegable_de_dl_va_vacio_sin_consultar(): void
    {
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->expects($this->never())->method('matching');

        $this->assertSame(['id' => 'dl', 'opciones' => [], 'selected' => '', 'blanco' => false], DbLugarDropdown::getData('', $repo));
    }

    public function test_las_dl_de_una_region_activas_y_la_opcion_global_al_final(): void
    {
        $criterio = null;
        $repo = $this->createMock(DelegacionRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function ($c) use (&$criterio): array {
            $criterio = $c;

            return [$this->delegacion('dlb'), $this->delegacion(null), $this->delegacion('dlp')];
        });

        $r = DbLugarDropdown::getData('H', $repo);

        $this->assertSame(['active eq true', 'region eq H'], DescribeCriteria::filtros($criterio));
        $this->assertSame(['dlb', 'dlp', 'H'], array_column($r['opciones'], 0));
        $this->assertSame('dlb', $r['opciones'][0][1]);
        $this->assertNotSame('H', $r['opciones'][2][1], 'la región se etiqueta «para gestión global»');
    }

    public function test_el_formulario_de_propiedades_rechaza_una_operacion_desconocida(): void
    {
        $uc = new DbPropiedadesFormData(new RegionDropdown($this->createMock(\src\ubis\domain\contracts\RegionRepositoryInterface::class)));

        $this->assertSame(['error' => 'op no válida'], $uc->build(['op' => 'borrar_todo']));
        $this->assertSame(['error' => 'op no válida'], $uc->build([]));
        $this->assertSame(['error' => 'op no válida'], $uc->build(['op' => ['x']]));
    }

    // ---- contexto de renombrado ----------------------------------------------------------------------

    /**
     * @dataProvider camposOrigenProvider
     */
    public function test_el_campo_origen_se_reduce_al_nombre_base(string $campo, string $base): void
    {
        $this->assertSame($base, RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen($campo));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function camposOrigenProvider(): iterable
    {
        yield 'vacío' => ['', ''];
        yield 'espacios' => ['  ', ''];
        yield 'base' => ['ct-01', 'ct-01'];
        yield 'sufijo v' => ['ct-01v', 'ct-01'];
        yield 'sufijo f con espacios' => [' ct-01f ', 'ct-01'];
        yield 'una sola letra v' => ['v', 'v'];
    }

    /**
     * @dataProvider entradasInvalidasProvider
     */
    public function test_los_parametros_incompletos_devuelven_un_diagnostico_de_error(string $origen, string $region, string $dl): void
    {
        $r = RenombrarEsquemaVerificacionContexto::desdeEntrada($origen, $region, $dl, 1, 1, 1);

        $this->assertIsArray($r);
        $this->assertFalse($r['listo']);
        $this->assertSame('error', $r['bloques'][0]['items'][0]['estado']);
        $this->assertSame(trim($region) !== '' && trim($dl) !== '' ? trim($region) . '-' . trim($dl) : '', $r['meta']['objetivo_base']);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function entradasInvalidasProvider(): iterable
    {
        yield 'sin origen' => ['', 'H', 'dlb'];
        yield 'sin región' => ['ct-01', '', 'dlb'];
        yield 'sin dl' => ['ct-01', 'H', ' '];
    }

    public function test_el_contexto_normaliza_origen_y_destino(): void
    {
        $c = RenombrarEsquemaVerificacionContexto::desdeEntrada(' ct-01v ', ' H ', ' dlb ', 1, 0, 1);

        $this->assertInstanceOf(RenombrarEsquemaVerificacionContexto::class, $c);
        $this->assertSame(['ct-01v', 'ct-01', 'ct-01v', 'ct-01f'], [$c->esquemaOrigenCampo, $c->esquemaOld, $c->esquemaOldv, $c->esquemaOldf]);
        $this->assertSame(['H', 'dlb', 'H-dlb', 'H-dlbv', 'H-dlbf'], [$c->region, $c->dl, $c->esquemaNew, $c->esquemaNewv, $c->esquemaNewf]);
        $this->assertSame([1, 0, 1], [$c->comun, $c->sv, $c->sf]);
        $this->assertFalse($c->sinRenombreEfectivo);
        $this->assertFalse($c->soloDestinoComprobacion);
    }

    public function test_si_origen_y_destino_coinciden_no_hay_renombre_efectivo(): void
    {
        $c = RenombrarEsquemaVerificacionContexto::desdeEntrada('H-dlb', 'H', 'dlb', 1, 1, 1);

        $this->assertTrue($c->sinRenombreEfectivo);
    }

    public function test_solo_destino_usa_la_misma_base_como_origen_y_destino(): void
    {
        $c = RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion('H', 'dlb', 1, 1, 0);

        $this->assertSame(['', 'H-dlb', 'H-dlb'], [$c->esquemaOrigenCampo, $c->esquemaOld, $c->esquemaNew]);
        $this->assertTrue($c->soloDestinoComprobacion);
        $this->assertTrue($c->sinRenombreEfectivo);
        $this->assertIsArray(RenombrarEsquemaVerificacionContexto::soloDestinoVerificacion('', 'dlb', 1, 1, 0));
    }

    // ---- guardas de las operaciones sobre esquemas ----------------------------------------------------------------------

    /**
     * @dataProvider copiasInvalidasProvider
     */
    public function test_copiar_esquema_valida_destino_y_referencia_antes_de_tocar_nada(string $ref, string $region, string $dl): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CopiarEsquema())->ejecutar($ref, $region, $dl, 1, 1, 1);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function copiasInvalidasProvider(): iterable
    {
        yield 'sin región' => ['H-dlb', '', 'dlp'];
        yield 'sin dl' => ['H-dlb', 'H', '  '];
        yield 'referencia vacía' => ['', 'H', 'dlp'];
        yield 'referencia sin guion' => ['Hdlb', 'H', 'dlp'];
    }

    public function test_crear_esquema_rechaza_una_referencia_invalida(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CrearEsquema($this->createMock(DbSchemaRepositoryInterface::class)))->ejecutar('singuion', 'H', 'dlp', 1, 1, 1);
    }

    public function test_una_precondicion_incumplida_es_un_aviso_bloqueante_no_un_error_sql(): void
    {
        $e = new CrearEsquemaPrecondicionException('destino ocupado');

        $this->assertInstanceOf(\RuntimeException::class, $e);
        $this->assertSame('destino ocupado', $e->getMessage());
    }

    public function test_las_contrasenas_de_los_roles_tienen_11_caracteres_y_varian(): void
    {
        $m = new \ReflectionMethod(CrearUsuarios::class, 'generarPassword');
        $m->setAccessible(true);

        $a = $m->invoke(null, 11);
        $b = $m->invoke(null, 11);

        $this->assertSame(11, strlen($a));
        $this->assertSame(0, strlen($m->invoke(null, 0)));
        $this->assertNotSame($a, $b);
        $this->assertDoesNotMatchRegularExpression("/['\"\\\\\\s;]/", $a, 'sin comillas, barra invertida, espacios ni punto y coma');
    }

    // ---- apptables ----------------------------------------------------------------------

    /**
     * @param array<string, mixed> $post
     */
    private function apptables(array $post, ?array $apps = ['procesos' => 3]): void
    {
        if ($apps === null) {
            unset($_SESSION['config']);
        } else {
            $_SESSION['config'] = ['a_apps' => $apps];
        }
        (new ApptablesUpdate())->ejecutar($post);
    }

    /**
     * @dataProvider apptablesInvalidosProvider
     * @param array<string, mixed> $post
     */
    public function test_apptables_valida_aplicacion_accion_y_esquema_antes_de_ejecutar(array $post, ?array $apps): void
    {
        $this->expectException(\RuntimeException::class);
        $this->apptables($post, $apps);
    }

    /**
     * @return iterable<string, array{array<string, mixed>, ?array<string, int>}>
     */
    public static function apptablesInvalidosProvider(): iterable
    {
        yield 'sin apps en sesión' => [['id_app' => 3, 'accion' => 'crear_global'], null];
        yield 'app inexistente' => [['id_app' => 99, 'accion' => 'crear_global'], ['procesos' => 3]];
        yield 'app no numérica' => [['id_app' => 'x', 'accion' => 'crear_global'], ['procesos' => 3]];
        yield 'sin acción' => [['id_app' => 3], ['procesos' => 3]];
        yield 'esquema obligatorio' => [['id_app' => 3, 'accion' => 'crear_esquema'], ['procesos' => 3]];
        yield 'llenar sin esquema' => [['id_app' => 3, 'accion' => 'llenar_esquema', 'esquema' => ''], ['procesos' => 3]];
        yield 'acción desconocida' => [['id_app' => 3, 'accion' => 'volar'], ['procesos' => 3]];
        yield 'app sin clases db' => [['id_app' => 7, 'accion' => 'crear_global'], ['app_que_no_existe_tst' => 7]];
        yield 'app sin clases db (esquema)' => [['id_app' => 7, 'accion' => 'eliminar_esquema', 'esquema' => 'H-dlb'], ['app_que_no_existe_tst' => 7]];
    }

    // ---- migraciones (ficheros temporales, repositorio simulado) ----------------------------------------------------------------------

    /** @param list<MigracionAplicada> $aplicadas */
    private function repoMigraciones(array $aplicadas = [], ?bool $eliminar = true): MigracionAplicadaRepositoryInterface&MockObject
    {
        $repo = $this->createMock(MigracionAplicadaRepositoryInterface::class);
        $repo->method('aplicadas')->willReturn($aplicadas);
        $repo->method('Eliminar')->willReturn((bool) $eliminar);

        return $repo;
    }

    private function aplicada(string $prefijo, string $descripcion, string $database): MigracionAplicada
    {
        $m = new MigracionAplicada();
        $m->setPrefijo($prefijo);
        $m->setDescripcion($descripcion);
        $m->setDatabase($database);

        return $m;
    }

    private function migracionComun(): void
    {
        file_put_contents($this->tmpDir . '/202601010000_demo_comun__comun.sql', "UPDATE public.x SET a = 1;\n");
    }

    public function test_quitar_sin_seleccion_avisa_sin_tocar_el_repositorio(): void
    {
        $repo = $this->repoMigraciones();
        $repo->expects($this->never())->method('ensureTabla');

        $r = (new MigracionesQuitarRegistro($repo, $this->tmpDir))->quitar([]);

        $this->assertSame([], $r['lines']);
        $this->assertNotNull($r['error']);
    }

    public function test_quitar_una_migracion_inexistente_no_elimina_nada(): void
    {
        $this->migracionComun();
        $repo = $this->repoMigraciones();
        $repo->expects($this->never())->method('Eliminar');

        $r = (new MigracionesQuitarRegistro($repo, $this->tmpDir))->quitar(['no_existe']);

        $this->assertStringContainsString('no encontrada', $r['lines'][0]);
        $this->assertNotNull($r['error']);
    }

    public function test_quitar_una_migracion_sin_registro_lo_indica_como_error(): void
    {
        $this->migracionComun();

        $r = (new MigracionesQuitarRegistro($this->repoMigraciones(), $this->tmpDir))->quitar(['202601010000_demo_comun']);

        $this->assertContains('  - sin registro en migracion_aplicada.', $r['lines']);
        $this->assertStringContainsString('Ninguna migracion', (string) $r['error']);
    }

    public function test_quitar_elimina_el_registro_de_cada_aplicacion_con_registro(): void
    {
        $this->migracionComun();
        $aplicada = $this->aplicada('202601010000', 'demo_comun', 'comun');
        $repo = $this->repoMigraciones([$aplicada]);
        $repo->expects($this->once())->method('Eliminar')->with($aplicada)->willReturn(true);

        $r = (new MigracionesQuitarRegistro($repo, $this->tmpDir))->quitar(['202601010000_demo_comun']);

        $this->assertNull($r['error']);
        $this->assertContains('Registros eliminados: 1.', $r['lines']);
    }

    public function test_si_el_repositorio_no_puede_eliminar_se_informa_y_hay_error(): void
    {
        $this->migracionComun();
        $repo = $this->repoMigraciones([$this->aplicada('202601010000', 'demo_comun', 'comun')], false);

        $r = (new MigracionesQuitarRegistro($repo, $this->tmpDir))->quitar(['202601010000_demo_comun']);

        $this->assertNotNull($r['error']);
        $this->assertNotSame([], preg_grep('/no se pudo eliminar/', $r['lines']));
    }

    /**
     * @dataProvider ejecucionesVaciasProvider
     * @param list<string> $seleccion
     */
    public function test_ejecutar_sin_nada_que_ejecutar_no_abre_conexiones(string $modo, array $seleccion, string $hasta): void
    {
        $this->migracionComun();
        $ejecutor = new MigracionesEjecutar($this->repoMigraciones(), $this->createMock(DbSchemaRepositoryInterface::class), $this->tmpDir);

        $this->assertSame(['lines' => ['No hay migraciones para ejecutar.'], 'error' => null], $ejecutor->ejecutar($modo, $seleccion, $hasta));
    }

    /**
     * @return iterable<string, array{string, list<string>, string}>
     */
    public static function ejecucionesVaciasProvider(): iterable
    {
        yield 'selección vacía' => ['seleccion', [], ''];
        yield 'selección inexistente' => ['seleccion', ['otra'], ''];
        yield 'hasta sin prefijo' => ['hasta', [], ''];
        yield 'hasta prefijo inexistente' => ['hasta', [], '209901010000_nada'];
    }
}
