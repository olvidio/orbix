<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\application;

use PDO;
use src\devel_db_admin\application\AbsorberEsquema;
use src\devel_db_admin\application\CopiarEsquema;
use src\devel_db_admin\application\CorregirEstadoRenombrarEsquema;
use src\devel_db_admin\application\CrearEsquema;
use src\devel_db_admin\application\CrearUsuarios;
use src\devel_db_admin\application\EliminarEsquemaDl;
use src\devel_db_admin\application\MoverTabla;
use src\devel_db_admin\application\RenombrarEsquema;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use Tests\myTest;

/**
 * Ciclo de vida de un esquema de delegación con las operaciones reales de `devel_db_admin`:
 * crear los usuarios (roles) → crear el esquema a partir de uno de referencia → renombrarlo →
 * eliminarlo. Es DDL de verdad (CREATE ROLE, pg_dump + psql, ALTER SCHEMA, DROP …).
 *
 * Solo se ejecuta en el entorno de pruebas (`WEBDIR=pruebas`, BD `pruebas-*`): contra las BD de
 * desarrollo se salta. Dos cosas son comunes a desarrollo y pruebas y se tratan aparte:
 *  - los **roles** de PostgreSQL son del servidor: el esquema de prueba usa un nombre propio
 *    (`Zz-dlzzt`, luego `Zz-dlzzu`) y al acabar se borran sus roles pase lo que pase;
 *  - los **ficheros de contraseñas** (`*.roles.inc`): el test trabaja sobre una copia temporal del
 *    directorio de configuración (`ConfigDB::$dirPwdOverride`), no sobre el real.
 *
 * Fase 6 del plan de revisión de tests (backlog «Tests del DDL de esquemas»).
 */
final class EsquemaCicloDeVidaTest extends myTest
{
    private const REF = 'H-dlb';
    private const REGION = 'Zz';
    private const DL = 'dlzzt';
    private const DL_NUEVA = 'dlzzu';

    /** Claves de `importar` (BD) en las que vive un esquema de sv: comun, sv y sv-e. */
    private const BDS = ['public' => '', 'publicv' => 'v', 'publicv-e' => 'v'];

    private const DIR_VOLCADOS = '/var/www/orbix/log/db';
    private const ACTIVIDAD_RESTO = 'zz actividad de resto';
    private const DIR_CONF_REAL = '/var/www/conf';

    private static string $dirConf = '';
    /** @var list<string> */
    private static array $volcadosAntes = [];
    /** @var array<string, PDO> */
    private static array $pdos = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (getenv('WEBDIR') !== 'pruebas') {
            return;
        }
        // Copia del directorio de configuración: lo que se escriba en los ficheros de contraseñas va a la copia.
        // Copias que dejara una ejecución anterior que acabó con un error fatal (llevan contraseñas).
        foreach (glob(sys_get_temp_dir() . '/orbix_conf_test_*') ?: [] as $resto) {
            array_map('unlink', glob($resto . '/*') ?: []);
            @rmdir($resto);
        }
        self::$dirConf = sys_get_temp_dir() . '/orbix_conf_test_' . getmypid();
        @mkdir(self::$dirConf, 0700, true);
        ConfigDB::$dirPwdOverride = self::$dirConf;
        self::$volcadosAntes = glob(self::DIR_VOLCADOS . '/*') ?: [];
        self::limpiar();
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$dirConf !== '') {
            self::limpiar();
            ConfigDB::$dirPwdOverride = null;
            array_map('unlink', glob(self::$dirConf . '/*') ?: []);
            @rmdir(self::$dirConf);
            // Volcados de pg_dump que deja la creación del esquema.
            foreach (array_diff(glob(self::DIR_VOLCADOS . '/*') ?: [], self::$volcadosAntes) as $f) {
                @unlink($f);
            }
        }
        self::$pdos = [];
        parent::tearDownAfterClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if (getenv('WEBDIR') !== 'pruebas') {
            $this->markTestSkipped('DDL de esquemas: solo en el entorno de pruebas (tools/qa/phpunit_protegido.php).');
        }
        foreach (array_keys(self::BDS) as $clave) {
            $bd = (string) self::valor($clave, 'SELECT current_database()');
            if (!str_starts_with($bd, 'pruebas-')) {
                $this->fail("La conexión de mantenimiento «{$clave}» va a «{$bd}», no a una BD de pruebas: no se ejecuta DDL.");
            }
        }
        // Cada test empieza sin restos del anterior (aunque haya acabado con un error).
        self::limpiar();
    }

    /**
     * Las operaciones de esquemas no dejan cambiados los repositorios del contenedor: al trasladar
     * datos entre «resto» y la dl les ponen la conexión del esquema destino, y tienen que reponerla
     * (si no, lo que venga después en el mismo proceso trabaja contra un esquema que ya no existe).
     */
    public function tearDown(): void
    {
        if (self::$dirConf !== '') {
            foreach ([ActividadDlRepositoryInterface::class => 'oDBC', CasaDlRepositoryInterface::class => 'oDBC', CentroDlRepositoryInterface::class => 'oDB'] as $interfaz => $global) {
                $repositorio = DependencyResolver::get($interfaz);
                if ($repositorio instanceof ClaseRepository) {
                    $this->assertSame($GLOBALS[$global], $repositorio->getoDbl(), "$interfaz conserva su conexión");
                }
            }
        }
        parent::tearDown();
    }

    private static function pdo(string $clave): PDO
    {
        return self::$pdos[$clave] ??= (new DBConnection((new ConfigDB('importar'))->getConexionMantenimiento($clave)))->getPDO();
    }

    /** Quita cualquier resto de los esquemas y roles de prueba (por si una ejecución anterior se quedó a medias). */
    private static function limpiar(): void
    {
        // Ficheros de configuración: de nuevo una copia limpia de los reales.
        foreach (glob(self::DIR_CONF_REAL . '/*.inc') ?: [] as $f) {
            copy($f, self::$dirConf . '/' . basename($f));
        }
        foreach ([self::DL, self::DL_NUEVA] as $dl) {
            foreach (self::BDS as $clave => $sufijo) {
                // Con su nombre y con el «zz…» que le pone la absorción al esquema disuelto.
                foreach (['', 'zz'] as $prefijo) {
                    $esquema = $prefijo . self::REGION . '-' . $dl . $sufijo;
                    try {
                        self::pdo($clave)->exec('DROP SCHEMA IF EXISTS "' . $esquema . '" CASCADE');
                    } catch (\Throwable) {
                    }
                }
            }
            foreach (['', 'v', 'f'] as $sufijo) {
                $rol = self::REGION . '-' . $dl . $sufijo;
                foreach (array_keys(self::BDS) as $clave) {
                    try {
                        if (self::existeRol($rol)) {
                            self::pdo($clave)->exec('DROP OWNED BY "' . $rol . '" CASCADE');
                        }
                    } catch (\Throwable) {
                    }
                }
                try {
                    self::pdo('public')->exec('DROP ROLE IF EXISTS "' . $rol . '"');
                } catch (\Throwable) {
                }
            }
            // El registro de esquemas se lleva en las tres BD.
            foreach (array_keys(self::BDS) as $clave) {
                try {
                    self::pdo($clave)->exec("DELETE FROM public.db_idschema WHERE schema LIKE '" . self::REGION . '-' . $dl . "%'");
                } catch (\Throwable) {
                }
            }
        }
        try {
            self::pdo('public')->exec("DELETE FROM public.xu_dl WHERE region = '" . self::REGION . "'");
        } catch (\Throwable) {
        }
        // La actividad de «resto» del test de copiar datos, esté donde esté.
        foreach (['resto.a_actividades_ex', '"' . self::REF . '".a_actividades_dl'] as $tabla) {
            try {
                self::pdo('public')->exec("DELETE FROM $tabla WHERE nom_activ = '" . self::ACTIVIDAD_RESTO . "'");
            } catch (\Throwable) {
            }
        }
    }

    /** Primer valor de una consulta en la BD de esa clave de mantenimiento. */
    private static function valor(string $clave, string $sql): string|false
    {
        $st = self::pdo($clave)->query($sql);
        $valor = $st === false ? false : $st->fetchColumn();

        return is_scalar($valor) && $valor !== false ? (string) $valor : false;
    }

    /** @return list<mixed>|false primera fila de una consulta */
    private static function fila(string $clave, string $sql): array|false
    {
        $st = self::pdo($clave)->query($sql);
        $fila = $st === false ? false : $st->fetch(PDO::FETCH_NUM);

        return is_array($fila) ? array_values($fila) : false;
    }

    private static function existeRol(string $rol): bool
    {
        $st = self::pdo('public')->prepare('SELECT 1 FROM pg_roles WHERE rolname = ?');
        $st->execute([$rol]);

        return $st->fetchColumn() !== false;
    }

    private static function numTablas(string $clave, string $esquema): int
    {
        $st = self::pdo($clave)->prepare("SELECT count(*) FROM pg_tables WHERE schemaname = ?");
        $st->execute([$esquema]);

        return (int) $st->fetchColumn();
    }

    /** @return array<string, int> BD => número de tablas del esquema de la dl en esa BD */
    private static function tablasDe(string $region, string $dl): array
    {
        $out = [];
        foreach (self::BDS as $clave => $sufijo) {
            $out[$clave] = self::numTablas($clave, "$region-$dl$sufijo");
        }

        return $out;
    }

    private static function clavesDeRoles(string $base): string
    {
        return implode(',', ConfigDB::clavesEnFicheroRoles($base));
    }

    /** Crea los roles y el esquema de una dl de prueba (comun, sv y sv-e) a partir del de referencia. */
    private function crearDl(string $dl): void
    {
        (new CrearUsuarios())->ejecutar(self::REGION, $dl, false);
        $crear = DependencyResolver::get(CrearEsquema::class);
        $this->assertInstanceOf(CrearEsquema::class, $crear);
        $crear->ejecutar(self::REF, self::REGION, $dl, 1, 1, 0);
    }

    /**
     * Absorber una dl en otra: sus actividades y sus personas pasan al esquema de la que absorbe y el
     * esquema absorbido queda renombrado a «zz…», sin herencias, para borrarlo a mano después.
     */
    public function test_absorber_un_esquema_en_otro_traslada_sus_actividades_y_personas(): void
    {
        $matriz = self::REGION . '-' . self::DL;
        $disuelta = self::REGION . '-' . self::DL_NUEVA;
        $this->crearDl(self::DL);
        $this->crearDl(self::DL_NUEVA);
        self::pdo('public')->exec("INSERT INTO public.xu_dl (dl, region, nombre_dl, active) VALUES ('" . self::DL . "', '" . self::REGION . "', 'zz matriz', true), ('" . self::DL_NUEVA . "', '" . self::REGION . "', 'zz disuelta', true)");
        self::pdo('public')->exec('INSERT INTO "' . $disuelta . "\".a_actividades_dl (id_tipo_activ, nom_activ, f_ini, f_fin) VALUES (112000, 'zz actividad absorbida', '2031-01-01', '2031-01-05')");
        self::pdo('publicv')->exec('INSERT INTO "' . $disuelta . "v\".p_numerarios (apellido1, nom) VALUES ('Zzabsorbido', 'Prueba')");
        $idActiv = self::valor('public', 'SELECT id_activ FROM "' . $disuelta . "\".a_actividades_dl");
        $idNom = self::valor('publicv', 'SELECT id_nom FROM "' . $disuelta . "v\".p_numerarios");
        $this->assertNotFalse($idActiv);
        $this->assertNotFalse($idNom);

        $absorber = DependencyResolver::get(AbsorberEsquema::class);
        $this->assertInstanceOf(AbsorberEsquema::class, $absorber);
        $resultado = $absorber->execute($matriz, $disuelta);

        $this->assertSame([], $resultado->errores, implode(' | ', $resultado->errores));
        $activas = self::valor('public', "SELECT string_agg(dl || ':' || active::text, ',' ORDER BY dl) FROM public.xu_dl WHERE region = '" . self::REGION . "'");
        $this->assertSame(self::DL . ':true,' . self::DL_NUEVA . ':false', $activas, 'la delegación absorbida queda inactiva');
        $actividad = self::fila('public', 'SELECT nom_activ, dl_org FROM "' . $matriz . '".a_actividades_dl WHERE id_activ = ' . (int) $idActiv);
        $this->assertSame(['zz actividad absorbida', self::DL], $actividad, 'la actividad está en la dl que absorbe, y es suya');
        $persona = self::fila('publicv', 'SELECT apellido1, dl FROM "' . $matriz . 'v".p_numerarios WHERE id_nom = ' . (int) $idNom);
        $this->assertSame(['Zzabsorbido', self::DL], $persona, 'la persona está en la dl que absorbe');

        // El esquema absorbido ya no existe con su nombre: queda como «zz…» en las tres BD.
        $this->assertSame(['public' => 0, 'publicv' => 0, 'publicv-e' => 0], self::tablasDe(self::REGION, self::DL_NUEVA));
        $this->assertGreaterThan(0, self::numTablas('public', 'zz' . $disuelta));
        $this->assertGreaterThan(0, self::numTablas('publicv', 'zz' . $disuelta . 'v'));
        $heredan = self::valor('public', "SELECT count(*) FROM pg_inherits i JOIN pg_class c ON c.oid = i.inhrelid JOIN pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = 'zz" . $disuelta . "'");
        $this->assertSame(0, (int) $heredan, 'las tablas del esquema disuelto ya no heredan de las globales');
    }

    /**
     * Si alguna de las dos delegaciones no está en la tabla de delegaciones, la absorción no empieza:
     * antes copiaba los datos y después fallaba con un error fatal a medio camino.
     */
    public function test_absorber_una_dl_que_no_esta_en_la_tabla_de_delegaciones_no_hace_nada(): void
    {
        $matriz = self::REGION . '-' . self::DL;
        $disuelta = self::REGION . '-' . self::DL_NUEVA;
        $this->crearDl(self::DL);
        $this->crearDl(self::DL_NUEVA);
        // Solo está registrada la que absorbe.
        self::pdo('public')->exec("INSERT INTO public.xu_dl (dl, region, nombre_dl, active) VALUES ('" . self::DL . "', '" . self::REGION . "', 'zz matriz', true)");
        self::pdo('public')->exec('INSERT INTO "' . $disuelta . "\".a_actividades_dl (id_tipo_activ, nom_activ, f_ini, f_fin) VALUES (112000, 'zz actividad', '2031-01-01', '2031-01-05')");

        $absorber = DependencyResolver::get(AbsorberEsquema::class);
        $this->assertInstanceOf(AbsorberEsquema::class, $absorber);
        $resultado = $absorber->execute($matriz, $disuelta);

        $this->assertCount(1, $resultado->errores, implode(' | ', $resultado->errores));
        $this->assertStringContainsString(self::DL_NUEVA, $resultado->errores[0]);
        $this->assertSame(0, (int) self::valor('public', 'SELECT count(*) FROM "' . $matriz . '".a_actividades_dl'), 'no se ha copiado nada');
        $this->assertSame(1, (int) self::valor('public', 'SELECT count(*) FROM "' . $disuelta . '".a_actividades_dl'), 'el esquema de origen sigue como estaba');
    }

    /**
     * Copiar datos al esquema nuevo: las tablas de configuración del esquema de referencia (tipos de
     * actividad, parámetros, usuarios, menús…) y las actividades que la dl tenía en «resto».
     */
    public function test_copiar_datos_trae_la_configuracion_de_referencia_y_las_actividades_de_resto(): void
    {
        $esquema = self::REGION . '-' . self::DL;
        $this->crearDl(self::DL);
        self::pdo('public')->exec("INSERT INTO resto.a_actividades_ex (id_tipo_activ, nom_activ, f_ini, f_fin, dl_org) VALUES (112000, '" . self::ACTIVIDAD_RESTO . "', '2031-02-01', '2031-02-05', '" . self::DL . "')");
        $cuenta = static fn (string $clave, string $tabla): int => (int) self::valor($clave, "SELECT count(*) FROM $tabla");

        (new CopiarEsquema())->ejecutar(self::REF, self::REGION, self::DL, 1, 1, 0);

        foreach (['a_tipos_actividad', 'x_config_schema'] as $tabla) {
            $this->assertSame($cuenta('public', '"' . self::REF . '".' . $tabla), $cuenta('public', '"' . $esquema . '".' . $tabla), "$tabla copiada a comun");
        }
        foreach (['aux_usuarios', 'aux_menus', 'm0_mods_installed_dl'] as $tabla) {
            $this->assertSame($cuenta('publicv-e', '"' . self::REF . 'v".' . $tabla), $cuenta('publicv-e', '"' . $esquema . 'v".' . $tabla), "$tabla copiada a sv-e");
        }
        // Las filas copiadas llevan el id_schema del esquema nuevo, no el de referencia.
        $this->assertSame(1, (int) self::valor('public', 'SELECT count(DISTINCT id_schema) FROM "' . $esquema . '".a_tipos_actividad'));
        $this->assertSame(0, (int) self::valor('public', 'SELECT count(*) FROM "' . $esquema . '".a_tipos_actividad WHERE id_schema IN (SELECT id_schema FROM "' . self::REF . '".a_tipos_actividad)'));

        // La actividad que la dl tenía en «resto» pasa a su esquema.
        $donde = static fn (string $tabla): int => $cuenta('public', $tabla . " WHERE nom_activ = '" . self::ACTIVIDAD_RESTO . "'");
        $this->assertSame(
            ['resto' => 0, 'esquema nuevo' => 1, 'esquema de quien lo ejecuta' => 0],
            ['resto' => $donde('resto.a_actividades_ex'), 'esquema nuevo' => $donde('"' . $esquema . '".a_actividades_dl'), 'esquema de quien lo ejecuta' => $donde('"' . self::REF . '".a_actividades_dl')],
        );
    }

    /**
     * Un renombre que se quedó a medias (hecho en PostgreSQL, pero no en los ficheros de contraseñas, en
     * el registro de esquemas ni en los defaults de las columnas): la comprobación lo detecta y la
     * corrección lo completa.
     */
    public function test_un_renombre_a_medias_se_detecta_y_se_corrige(): void
    {
        $viejo = self::REGION . '-' . self::DL;
        $nuevo = self::REGION . '-' . self::DL_NUEVA;
        $this->crearDl(self::DL);
        // Solo la parte de PostgreSQL, a mano: esquemas y roles con el nombre nuevo.
        foreach (self::BDS as $clave => $sufijo) {
            self::pdo($clave)->exec('ALTER SCHEMA "' . $viejo . $sufijo . '" RENAME TO "' . $nuevo . $sufijo . '"');
        }
        foreach (['', 'v', 'f'] as $sufijo) {
            self::pdo('public')->exec('ALTER ROLE "' . $viejo . $sufijo . '" RENAME TO "' . $nuevo . $sufijo . '"');
        }

        $antes = (new VerificarEstadoRenombrarEsquema())->ejecutar($viejo, self::REGION, self::DL_NUEVA, 1, 1, 0);
        $this->assertFalse($antes['listo'], 'con los ficheros y el registro sin tocar, el renombre no está completo');

        $corregir = DependencyResolver::get(CorregirEstadoRenombrarEsquema::class);
        $this->assertInstanceOf(CorregirEstadoRenombrarEsquema::class, $corregir);
        $resultado = $corregir->ejecutar($viejo, self::REGION, self::DL_NUEVA, 1, 1, 0);

        $this->assertNotSame([], $resultado['acciones'], 'la corrección ha hecho algo');
        $despues = (new VerificarEstadoRenombrarEsquema())->ejecutar($viejo, self::REGION, self::DL_NUEVA, 1, 1, 0);
        $pendientes = [];
        foreach ($despues['bloques'] as $bloque) {
            foreach ($bloque['items'] as $item) {
                if ($item['estado'] !== 'ok') {
                    $pendientes[] = $bloque['nombre'] . ': ' . $item['texto'] . ' [' . $item['estado'] . ']';
                }
            }
        }
        $this->assertTrue($despues['listo'], $despues['resumen'] . "\n" . implode("\n", $pendientes) . "\nacciones: " . implode(' | ', $resultado['acciones']) . "\navisos: " . implode(' | ', $resultado['avisos']));
        $this->assertStringContainsString($nuevo, self::clavesDeRoles('comun'));
        $this->assertStringNotContainsString($viejo . ',', self::clavesDeRoles('comun') . ',');
        foreach (array_keys(self::BDS) as $clave) {
            $this->assertSame('0', self::valor($clave, "SELECT count(*) FROM public.db_idschema WHERE schema LIKE '" . $viejo . "%'"), "registro de $clave sin el nombre antiguo");
            $this->assertNotSame('0', self::valor($clave, "SELECT count(*) FROM public.db_idschema WHERE schema LIKE '" . $nuevo . "%'"), "registro de $clave con el nombre nuevo");
        }
        // Los defaults de las columnas (id_schema, ids globales) apuntan al esquema con su nombre nuevo.
        foreach (self::BDS as $clave => $sufijo) {
            $this->assertSame('', (string) self::valor($clave, "SELECT string_agg(table_name || '.' || column_name || ' = ' || column_default, '; ' ORDER BY 1) FROM information_schema.columns WHERE table_schema = '" . $nuevo . $sufijo . "' AND column_default LIKE '%" . $viejo . "%'"), "defaults con el nombre antiguo en $clave");
        }
    }

    /** Una tabla de un esquema pasa de la BD sv a la BD sv-e (con sus datos) y desaparece de sv. */
    public function test_mover_una_tabla_de_sv_a_sv_e(): void
    {
        $esquemaV = self::REGION . '-' . self::DL . 'v';
        $this->crearDl(self::DL);
        // Una tabla propia del test, para no depender de cuáles están ya en sv-e.
        self::pdo('publicv')->exec('CREATE TABLE "' . $esquemaV . '".zz_tabla_movida (id integer PRIMARY KEY, texto text)');
        self::pdo('publicv')->exec('ALTER TABLE "' . $esquemaV . '".zz_tabla_movida OWNER TO "' . $esquemaV . '"');
        self::pdo('publicv')->exec('INSERT INTO "' . $esquemaV . "\".zz_tabla_movida VALUES (1, 'uno'), (2, 'dos')");

        $resultado = (new MoverTabla())->ejecutar('zz_tabla_movida', [$esquemaV]);

        $registros = '';
        foreach (array_diff(glob(self::DIR_VOLCADOS . '/*') ?: [], self::$volcadosAntes) as $f) {
            if (str_contains($f, 'log')) {
                $registros .= "\n" . basename($f) . ': ' . substr((string) file_get_contents($f), 0, 400);
            }
        }
        $this->assertSame('', $resultado->fatalError, $registros);
        $enSve = self::valor('publicv-e', 'SELECT string_agg(texto, \',\' ORDER BY id) FROM "' . $esquemaV . '".zz_tabla_movida');
        $this->assertSame('uno,dos', $enSve, 'la tabla está en sv-e con sus filas');
        $st = self::valor('publicv', "SELECT count(*) FROM pg_tables WHERE schemaname = '" . $esquemaV . "' AND tablename = 'zz_tabla_movida'");
        $this->assertSame(0, (int) $st, 'y ya no está en sv');
    }

    public function test_crear_renombrar_y_eliminar_un_esquema_de_delegacion(): void
    {
        $esquema = self::REGION . '-' . self::DL;
        $nuevo = self::REGION . '-' . self::DL_NUEVA;
        [$refRegion, $refDl] = explode('-', self::REF);
        $tablasRef = self::tablasDe($refRegion, $refDl);
        $this->assertNotContains(0, $tablasRef, 'el esquema de referencia tiene tablas en comun, sv y sv-e');

        // --- 1. Usuarios: un rol por esquema (comun, sv, sf) y su contraseña en los ficheros de roles.
        $usuarios = (new CrearUsuarios())->ejecutar(self::REGION, self::DL, false);
        $this->assertSame([$esquema, $esquema . 'v'], [$usuarios->esquema, $usuarios->esquemav]);
        $this->assertTrue(self::existeRol($esquema), 'rol del esquema de comun');
        $this->assertTrue(self::existeRol($esquema . 'v'), 'rol del esquema de sv');
        $this->assertStringContainsString($esquema, self::clavesDeRoles('comun'), 'contraseña anotada en comun.roles.inc (copia)');
        $this->assertStringContainsString($esquema . 'v', self::clavesDeRoles('sv'));
        $this->assertStringNotContainsString(self::REGION . '-', (string) file_get_contents('/var/www/conf/comun.roles.inc'), 'el fichero real de contraseñas no se toca');

        // --- 2. Esquema: copia de la estructura del de referencia en comun, sv y sv-e.
        $crear = DependencyResolver::get(CrearEsquema::class);
        $this->assertInstanceOf(CrearEsquema::class, $crear);
        $crear->ejecutar(self::REF, self::REGION, self::DL, 1, 1, 0);
        $this->assertSame($tablasRef, self::tablasDe(self::REGION, self::DL), 'las mismas tablas que el esquema de referencia, en las tres BD');
        $idSchema = self::valor('public', "SELECT count(*) FROM public.db_idschema WHERE schema LIKE '" . $esquema . "%'");
        $this->assertGreaterThan(0, (int) $idSchema, 'el esquema nuevo queda en el registro de ids');
        // Las tablas de la dl heredan de las globales y están vacías.
        $this->assertSame(0, (int) self::valor('publicv', 'SELECT count(*) FROM "' . $esquema . 'v".p_numerarios'));

        // --- 3. Renombrar: esquema, roles, registro y ficheros de contraseñas.
        $renombrar = DependencyResolver::get(RenombrarEsquema::class);
        $this->assertInstanceOf(RenombrarEsquema::class, $renombrar);
        $r = $renombrar->ejecutar($esquema, self::REGION, self::DL_NUEVA, 1, 1, 0);
        $this->assertArrayNotHasKey('error', $r, 'renombrar: ' . json_encode($r, JSON_UNESCAPED_UNICODE));
        $this->assertSame($tablasRef, self::tablasDe(self::REGION, self::DL_NUEVA), 'el esquema renombrado conserva sus tablas');
        $this->assertSame(['public' => 0, 'publicv' => 0, 'publicv-e' => 0], self::tablasDe(self::REGION, self::DL), 'con el nombre antiguo ya no hay nada');
        $this->assertTrue(self::existeRol($nuevo) && self::existeRol($nuevo . 'v'), 'los roles llevan el nombre nuevo');
        $this->assertFalse(self::existeRol($esquema) || self::existeRol($esquema . 'v'), 'no quedan roles con el nombre antiguo');
        $this->assertStringContainsString($nuevo, self::clavesDeRoles('comun'));
        $this->assertStringNotContainsString($esquema . ',', self::clavesDeRoles('comun') . ',');

        // La comprobación del renombre (BD, ficheros de contraseñas, registro y defaults de columnas) lo da por bueno.
        $estado = (new VerificarEstadoRenombrarEsquema())->ejecutar($esquema, self::REGION, self::DL_NUEVA, 1, 1, 0);
        $pendientes = [];
        foreach ($estado['bloques'] as $bloque) {
            foreach ($bloque['items'] as $item) {
                if ($item['estado'] !== 'ok') {
                    $pendientes[] = $bloque['nombre'] . ': ' . $item['texto'] . ' [' . $item['estado'] . ']';
                }
            }
        }
        $this->assertTrue($estado['listo'], $estado['resumen'] . "\n" . implode("\n", $pendientes));
        foreach (self::BDS as $clave => $sufijo) {
            $this->assertSame('', (string) self::valor($clave, "SELECT string_agg(table_name || '.' || column_name, ', ' ORDER BY 1) FROM information_schema.columns WHERE table_schema = '" . $nuevo . $sufijo . "' AND column_default LIKE '%" . $esquema . "%'"), "ningún default de $clave cita el nombre antiguo");
        }

        // --- 4. Eliminar: esquemas, roles y contraseñas.
        (new EliminarEsquemaDl())->ejecutar(self::REGION, self::DL_NUEVA, 1, 1, 0);
        $this->assertSame(['public' => 0, 'publicv' => 0, 'publicv-e' => 0], self::tablasDe(self::REGION, self::DL_NUEVA), 'el esquema ya no existe en ninguna BD');
        $this->assertFalse(self::existeRol($nuevo) || self::existeRol($nuevo . 'v'), 'los roles se han eliminado');
        $this->assertStringNotContainsString($nuevo, self::clavesDeRoles('comun'), 'y su contraseña ya no está en el fichero de roles');

        // El esquema de referencia sigue como estaba.
        $this->assertSame($tablasRef, self::tablasDe($refRegion, $refDl));
    }
}
