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

    public function test_add_esquema_monolitico_sincroniza_par_select(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc($dir . '/comun.inc', ['default' => ['host' => 'ext']]);
        $this->writeInc($dir . '/comun_select.inc', ['default' => ['host' => 'int']]);
        $this->writeInc($dir . '/pruebas-comun.inc', ['default' => ['host' => 'ext-p']]);
        $this->writeInc($dir . '/pruebas-comun_select.inc', ['default' => ['host' => 'int-p']]);

        $cfg = new ConfigDB('comun');
        $cfg->addEsquemaEnFicheroPasswords('comun', 'T-tT', 'pwd-comun');
        $cfg->addEsquemaEnFicheroPasswords('sv-e', 'T-tTv', 'pwd-sve');
        // En pre-prod (no docker) addEsquemaEnFicheroPasswords también escribe pruebas-*; aquí lo forzamos.
        $monolito = new \ReflectionMethod(ConfigDB::class, 'addEsquemaMonolitico');
        $monolito->invoke($cfg, 'pruebas-comun', 'T-tT', 'pwd-comun');
        $monolito->invoke($cfg, 'pruebas-sv-e', 'T-tTv', 'pwd-sve');

        foreach (['comun.inc', 'comun_select.inc', 'pruebas-comun.inc', 'pruebas-comun_select.inc'] as $nombre) {
            $data = include $dir . '/' . $nombre;
            $this->assertIsArray($data);
            $this->assertSame('pwd-comun', $data['T-tT']['password'] ?? null, $nombre);
        }
        foreach (['sv-e.inc', 'sv-e_select.inc', 'pruebas-sv-e.inc', 'pruebas-sv-e_select.inc'] as $nombre) {
            $data = include $dir . '/' . $nombre;
            $this->assertIsArray($data);
            $this->assertSame('pwd-sve', $data['T-tTv']['password'] ?? null, $nombre);
        }
    }

    public function test_remove_esquema_monolitico_quita_en_par_select(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc($dir . '/comun.inc', [
            'default' => ['host' => 'h'],
            'X-xX' => ['user' => 'X-xX', 'password' => 'p'],
        ]);
        $this->writeInc($dir . '/comun_select.inc', [
            'default' => ['host' => 'h2'],
            'X-xX' => ['user' => 'X-xX', 'password' => 'p'],
        ]);
        $this->writeInc($dir . '/pruebas-comun.inc', [
            'default' => ['host' => 'h3'],
            'X-xX' => ['user' => 'X-xX', 'password' => 'p'],
        ]);
        $this->writeInc($dir . '/pruebas-comun_select.inc', [
            'default' => ['host' => 'h4'],
            'X-xX' => ['user' => 'X-xX', 'password' => 'p'],
        ]);

        $cfg = new ConfigDB('comun');
        $cfg->removeEsquemaEnFicheroPasswords('comun', 'X-xX');
        $monolito = new \ReflectionMethod(ConfigDB::class, 'removeEsquemaMonolitico');
        $monolito->invoke($cfg, 'pruebas-comun', 'X-xX');

        foreach (['comun.inc', 'comun_select.inc', 'pruebas-comun.inc', 'pruebas-comun_select.inc'] as $nombre) {
            $data = include $dir . '/' . $nombre;
            $this->assertIsArray($data);
            $this->assertArrayNotHasKey('X-xX', $data, $nombre);
        }
    }

    public function test_getConexionImportarReplica_fallback_comun_conn(): void
    {
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre('importar'),
            [
                'default' => [
                    'host' => 'exterior',
                    'dbname' => 'comun',
                    'user' => 'postgres',
                    'password' => 'pgsecret',
                ],
            ],
        );
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre('comun'),
            [
                'default' => ['host' => 'exterior', 'dbname' => 'comun'],
                'public_select' => [
                    'host' => 'interior',
                    'dbname' => 'comun_select',
                ],
            ],
        );
        $this->writeInc($dir . '/importar.roles.inc', [
            'public_select' => ['user' => 'postgres', 'password' => 'pgsecret'],
        ]);

        $cfg = new ConfigDB('importar');
        $replica = $cfg->getConexionImportarReplica('public_select');

        $this->assertSame('interior', $replica['host']);
        $this->assertSame('comun_select', $replica['dbname']);
    }

    public function test_getConexionImportarReplica_usa_host_de_conn_no_de_default(): void
    {
        $base = 'cfgdbtest';
        $dir = $this->pwdDir();
        $this->writeInc(
            $dir . '/' . ConfigDB::ficheroConnNombre($base),
            [
                'default' => [
                    'host' => 'exterior',
                    'dbname' => 'comun',
                    'user' => 'postgres',
                    'password' => 'pgsecret',
                ],
                'public_select' => [
                    'host' => 'interior',
                    'dbname' => 'comun_select',
                ],
            ],
        );
        $this->writeInc(
            $dir . '/' . $base . '.roles.inc',
            [
                'public_select' => ['user' => 'orbix', 'password' => 'orbixpwd'],
            ],
        );

        $cfg = new ConfigDB($base);
        $esquema = $cfg->getEsquema('public_select');
        $replica = $cfg->getConexionImportarReplica('public_select');

        $this->assertSame('exterior', $esquema['host']);
        $this->assertSame('comun', $esquema['dbname']);
        $this->assertSame('interior', $replica['host']);
        $this->assertSame('comun_select', $replica['dbname']);
        $this->assertSame('public_select', $replica['schema']);
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
        /** @var array<string, mixed> $data */
        $data = $ref->getValue($cfg);

        return $data;
    }
}
