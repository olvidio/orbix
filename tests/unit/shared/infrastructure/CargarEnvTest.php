<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\CargarEnv;

/**
 * Lo que hay en el `.env` lo ve `getenv()`, no solo `$_ENV`: buena parte del código (y las herramientas
 * de `tools/`) lee las variables con `getenv()` (`ORBIX_CRON_SESION`, `ORBIX_DIR_PWD`…) y antes, con
 * `Dotenv::createImmutable`, no las veía. Una variable que ya viene del entorno (nginx `fastcgi_param`, la
 * línea de órdenes) gana a la del `.env`.
 */
final class CargarEnvTest extends TestCase
{
    private const NUEVA = 'ORBIX_TEST_CARGAR_ENV_NUEVA';
    private const DEL_ENTORNO = 'ORBIX_TEST_CARGAR_ENV_ENTORNO';

    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_env_' . bin2hex(random_bytes(4));
        mkdir($this->dir);
        file_put_contents($this->dir . '/.env', self::NUEVA . "=del-env\n" . self::DEL_ENTORNO . "=del-env\n");
    }

    protected function tearDown(): void
    {
        foreach ([self::NUEVA, self::DEL_ENTORNO] as $var) {
            putenv($var);
            unset($_ENV[$var], $_SERVER[$var]);
        }
        @unlink($this->dir . '/.env');
        @rmdir($this->dir);
    }

    public function test_getenv_ve_lo_del_env(): void
    {
        CargarEnv::desde($this->dir);

        $this->assertSame('del-env', $_ENV[self::NUEVA] ?? null);
        $this->assertSame('del-env', getenv(self::NUEVA));
    }

    public function test_lo_que_ya_viene_del_entorno_gana(): void
    {
        putenv(self::DEL_ENTORNO . '=de-nginx');

        CargarEnv::desde($this->dir);

        $this->assertSame('de-nginx', getenv(self::DEL_ENTORNO));
    }

    public function test_sin_env_no_falla(): void
    {
        unlink($this->dir . '/.env');

        CargarEnv::desde($this->dir);

        $this->assertFalse(getenv(self::NUEVA));
    }
}
