<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\cli;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use src\shared\infrastructure\cli\CronSesion;

final class CronSesionTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $postAntes = [];

    /** @var array<string, mixed> */
    private array $serverAntes = [];

    /** @var array<string, string|false> */
    private array $envAntes = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->postAntes = $_POST;
        $this->serverAntes = $_SERVER;
        $this->envAntes = [
            'UBICACION' => getenv('UBICACION'),
            'ESQUEMA' => getenv('ESQUEMA'),
            'PRIVATE' => getenv('PRIVATE'),
            'DB_SERVER' => getenv('DB_SERVER'),
        ];
    }

    protected function tearDown(): void
    {
        $_POST = $this->postAntes;
        $_SERVER = $this->serverAntes;
        foreach ($this->envAntes as $clave => $valor) {
            if ($valor === false) {
                putenv($clave);
            } else {
                putenv($clave . '=' . $valor);
            }
        }
        parent::tearDown();
    }

    public function test_carga_el_fichero_y_aplica_el_entorno(): void
    {
        $ruta = $this->escribirSesion([
            'username' => 'cron',
            'password' => 'secreto',
            'dirweb' => 'orbix',
            'document_root' => '/var/www',
            'ubicacion' => 'sv',
            'esquema' => 'H-dlbv',
            'private' => 'sv',
            'db_server' => '1',
        ]);

        $sesion = CronSesion::desdeFichero($ruta);
        $sesion->aplicar();

        $this->assertSame('sv', $sesion->ubicacion());
        $this->assertSame('cron', $_POST['username']);
        $this->assertSame('secreto', $_POST['password']);
        $this->assertSame('orbix', $_SERVER['DIRWEB']);
        $this->assertSame('/var/www', $_SERVER['DOCUMENT_ROOT']);
        $this->assertSame('sv', getenv('UBICACION'));
        $this->assertSame('H-dlbv', getenv('ESQUEMA'));
        $this->assertSame('', $sesion->mailAviso());
    }

    public function test_mail_aviso_es_opcional(): void
    {
        $ruta = $this->escribirSesion([
            'username' => 'cron',
            'password' => 'x',
            'dirweb' => 'orbix',
            'document_root' => '/tmp',
            'ubicacion' => 'sv',
            'esquema' => 'H-dlbv',
            'private' => 'sv',
            'db_server' => '1',
            'mail_aviso' => 'admin@dlb.example',
        ]);

        $this->assertSame('admin@dlb.example', CronSesion::desdeFichero($ruta)->mailAviso());
    }

    public function test_falta_una_clave(): void
    {
        $ruta = $this->escribirSesion(['username' => 'cron']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('falta la clave');
        CronSesion::desdeFichero($ruta);
    }

    public function test_buscar_devuelve_el_primero_legible(): void
    {
        $ruta = $this->escribirSesion([
            'username' => 'cron',
            'password' => 'x',
            'dirweb' => 'orbix',
            'document_root' => '/tmp',
            'ubicacion' => 'sf',
            'esquema' => 'H-dlbf',
            'private' => 'sf',
            'db_server' => '1',
        ]);

        $this->assertSame($ruta, CronSesion::buscar(['/no/existe.inc', $ruta]));
    }

    /**
     * @param array<string, string> $datos
     */
    private function escribirSesion(array $datos): string
    {
        $ruta = tempnam(sys_get_temp_dir(), 'cron_sesion_');
        if ($ruta === false) {
            $this->fail('no se pudo crear temporal');
        }
        $export = var_export($datos, true);
        file_put_contents($ruta, "<?php\nreturn $export;\n");

        return $ruta;
    }
}
