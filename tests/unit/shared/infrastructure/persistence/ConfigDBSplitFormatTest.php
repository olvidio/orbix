<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\persistence\ConfigDB;

final class ConfigDBSplitFormatTest extends TestCase
{
    /** @var list<string> */
    private array $createdFiles = [];

    private ?string $tmpDir = null;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/configdb_split_' . bin2hex(random_bytes(4));
        mkdir($this->tmpDir, 0700, true);
        ConfigDB::$dirPwdOverride = $this->tmpDir;
    }

    protected function tearDown(): void
    {
        ConfigDB::$dirPwdOverride = null;
        foreach ($this->createdFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
        if ($this->tmpDir !== null && is_dir($this->tmpDir)) {
            @rmdir($this->tmpDir);
        }
        parent::tearDown();
    }

    public function test_baseRolesParaFichero_mapea_replicas(): void
    {
        $this->assertSame('comun', ConfigDB::baseRolesParaFichero('comun_select'));
        $this->assertSame('sv', ConfigDB::baseRolesParaFichero('sv-e'));
        $this->assertSame('sv', ConfigDB::baseRolesParaFichero('sv-e_select'));
        $this->assertSame('comun', ConfigDB::baseRolesParaFichero('comun'));
    }

    public function test_sv_e_select_carga_roles_desde_sv_roles_inc(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre('sv-e_select'),
            ['default' => ['host' => 'interior', 'dbname' => 'pruebas-sv-e_select']],
        );
        $this->writeInc(
            $dir . '/sv.roles.inc',
            [
                'B-crBv' => ['user' => 'B-crBv', 'password' => 'pwd-sv'],
            ],
        );

        $cfg = new ConfigDB('sv-e_select');

        $this->assertTrue($cfg->tieneEsquema('B-crBv'));
        $this->assertSame('pwd-sv', $cfg->getEsquema('B-crBv')['password']);
    }

    public function test_rutaDondeAnadirEsquema_dl_sf_apunta_a_sf_e_roles(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc($dir . '/sf-e.roles.inc', []);

        $ruta = ConfigDB::rutaDondeAnadirEsquema('sf', 'B-crBf');

        $this->assertSame($dir . '/sf-e.roles.inc', $ruta);
        $this->assertSame('sf-e', ConfigDB::ficheroBaseRolesParaEsquemaRegionDl('B-crBf'));
    }

    public function test_mensajeAvisoEsquemaConexionFaltante_lleva_prefijo(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc($dir . '/sf-e.roles.inc', []);

        $msg = ConfigDB::mensajeAvisoEsquemaConexionFaltante('sf', 'B-crBf');

        $this->assertStringStartsWith('Aviso:', $msg);
        $this->assertStringContainsString('sf-e.roles.inc', $msg);
    }

    public function test_formato_partido_merge_conn_y_roles(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre($base),
            ['default' => ['host' => 'prod-host', 'dbname' => 'orbix']],
        );
        $this->writeInc(
            $dir . '/' . $base . '.roles.inc',
            [
                'cfgdb_esq' => ['user' => 'cfgdb_esq', 'password' => 'secret'],
            ],
        );

        $cfg = new ConfigDB($base);
        $merged = $this->dataFromConfigDb($cfg);

        $this->assertSame('prod-host', $merged['default']['host']);
        $this->assertSame('secret', $merged['cfgdb_esq']['password']);
    }

    public function test_renombrar_en_roles_inc_solo_un_fichero(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $rolesPath = $dir . '/' . $base . '.roles.inc';
        $this->writeInc($rolesPath, [
            'cfgdb_old' => ['user' => 'cfgdb_old', 'password' => 'x'],
        ]);

        $cfg = new ConfigDB($base);
        $cfg->renombrarListaEsquema($base, 'cfgdb_old', 'cfgdb_new');

        $data = include $rolesPath;
        $this->assertIsArray($data);
        $this->assertArrayNotHasKey('cfgdb_old', $data);
        $this->assertSame('x', $data['cfgdb_new']['password']);
    }

    public function test_roles_inc_se_completa_con_monolito_si_falta_clave(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre($base),
            ['default' => ['host' => 'prod-host', 'dbname' => 'orbix']],
        );
        $this->writeInc(
            $dir . '/' . $base . '.roles.inc',
            [
                'cfgdb_otro' => ['user' => 'cfgdb_otro', 'password' => 'secret'],
            ],
        );
        $this->writeInc($dir . '/' . $base . '.inc', [
            'default' => ['host' => 'legacy-host', 'dbname' => 'legacy'],
            'cfgdb_esq' => ['user' => 'cfgdb_esq', 'password' => 'legacy-secret'],
        ]);

        $cfg = new ConfigDB($base);
        $merged = $this->dataFromConfigDb($cfg);

        $this->assertSame('secret', $merged['cfgdb_otro']['password']);
        $this->assertSame('legacy-secret', $merged['cfgdb_esq']['password']);
        $this->assertTrue($cfg->tieneEsquema('cfgdb_esq'));
    }

    public function test_getConexionMantenimiento_credenciales_plantilla_y_schema_public(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre($base),
            [
                'default' => ['host' => 'h-default', 'port' => 5432],
                'publicv' => ['dbname' => 'pruebas-sv', 'user' => 'postgres', 'password' => 'pgsecret'],
            ],
        );
        $this->writeInc(
            $dir . '/' . $base . '.roles.inc',
            [
                'B-crBv' => ['user' => 'B-crBv', 'password' => 'dlsecret'],
            ],
        );

        $cfg = new ConfigDB($base);
        $conn = $cfg->getConexionMantenimiento('publicv');

        $this->assertSame('postgres', $conn['user']);
        $this->assertSame('pgsecret', $conn['password']);
        $this->assertSame('pruebas-sv', $conn['dbname']);
        $this->assertSame('h-default', $conn['host']);
        $this->assertSame('public', $conn['schema']);
    }

    public function test_crearFicherosPartidos_desde_monolitos(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $this->writeInc($dir . '/' . $base . '.inc', [
            'default' => ['host' => 'h1', 'dbname' => 'db1'],
            'esq1' => ['user' => 'esq1', 'password' => 'p1'],
        ]);
        $this->writeInc($dir . '/pruebas-' . $base . '.inc', [
            'default' => ['host' => 'h-pruebas', 'dbname' => 'db-pruebas'],
            'esq2' => ['user' => 'esq2', 'password' => 'p2'],
        ]);

        $msgs = ConfigDB::crearFicherosPartidosDesdeMonoliticos($base);
        $this->assertNotEmpty($msgs);
        foreach ([$base . '.roles.inc', $base . '.conn.inc', 'pruebas-' . $base . '.conn.inc'] as $nombre) {
            $path = $dir . '/' . $nombre;
            $this->createdFiles[] = $path;
            $this->assertFileExists($path);
        }

        $roles = include $dir . '/' . $base . '.roles.inc';
        $this->assertIsArray($roles);
        $this->assertArrayHasKey('esq1', $roles);
        $this->assertArrayHasKey('esq2', $roles);
    }

    private function pwdDir(): string
    {
        $this->assertNotNull($this->tmpDir);

        return $this->tmpDir;
    }

    /** @param array<string, mixed> $data */
    private function writeInc(string $path, array $data): void
    {
        file_put_contents($path, '<?php return ' . var_export($data, true) . ' ;');
        $this->createdFiles[] = $path;
    }

    /** @return array<string, mixed> */
    private function dataFromConfigDb(ConfigDB $cfg): array
    {
        $ref = new \ReflectionProperty(ConfigDB::class, 'data');
        $ref->setAccessible(true);
        /** @var array<string, mixed> $data */
        $data = $ref->getValue($cfg);

        return $data;
    }
}
