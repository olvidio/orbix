<?php

declare(strict_types=1);

namespace Tests\unit\shared\security;

use PHPUnit\Framework\TestCase;
use src\shared\security\SignedDownloadToken;
use src\usuarios\infrastructure\security\EnlaceRecuperacionPassword;

/**
 * El secreto de los enlaces firmados (`ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET`) se lee del `.env`.
 * `load_env.php` lo carga con `Dotenv::createImmutable`, que lo deja en `$_ENV` pero no en `getenv()`;
 * antes `SignedDownloadToken` y `EnlaceRecuperacionPassword` solo miraban `getenv()` y, con el secreto en
 * el `.env`, seguían firmando con la clave de respaldo (derivada de la ruta del proyecto, predecible).
 * Sin secreto, las descargas siguen usando esa clave de respaldo; los enlaces de restablecer la contraseña, no.
 */
final class SecretoFirmaEnlacesTest extends TestCase
{
    private const VAR = 'ORBIX_SIGNED_DOWNLOAD_TOKEN_SECRET';

    private mixed $envAnterior;
    private string|false $getenvAnterior;

    protected function setUp(): void
    {
        $this->envAnterior = $_ENV[self::VAR] ?? null;
        $this->getenvAnterior = getenv(self::VAR);
        unset($_ENV[self::VAR]);
        putenv(self::VAR);
    }

    protected function tearDown(): void
    {
        unset($_ENV[self::VAR]);
        if ($this->envAnterior !== null) {
            $_ENV[self::VAR] = $this->envAnterior;
        }
        putenv($this->getenvAnterior === false ? self::VAR : self::VAR . '=' . $this->getenvAnterior);
    }

    public function test_la_descarga_se_firma_con_el_secreto_del_env(): void
    {
        $_ENV[self::VAR] = 'secreto-del-env-de-al-menos-32-caracteres';
        $tk = SignedDownloadToken::tokenCertificadoEmitido(5);
        $this->assertNotNull(SignedDownloadToken::parse($tk));

        unset($_ENV[self::VAR]);
        $this->assertNull(SignedDownloadToken::parse($tk), 'firmado con el secreto, no con la clave de respaldo');
    }

    public function test_el_enlace_de_recuperar_la_contrasena_se_firma_con_el_secreto_del_env(): void
    {
        $_ENV[self::VAR] = 'secreto-del-env-de-al-menos-32-caracteres';
        $tk = EnlaceRecuperacionPassword::emitir(7, 'H-dlbv', 'hash');
        $this->assertNotNull(EnlaceRecuperacionPassword::abrir($tk));

        unset($_ENV[self::VAR]);
        $this->assertNull(EnlaceRecuperacionPassword::abrir($tk), 'firmado con el secreto, no con la clave de respaldo');
    }

    public function test_sin_secreto_la_descarga_usa_la_clave_de_respaldo_y_el_enlace_de_contrasena_no_vale(): void
    {
        $this->assertNotNull(SignedDownloadToken::parse(SignedDownloadToken::tokenCertificadoEmitido(5)));
        // El enlace de restablecer la contraseña no admite clave de respaldo (era deducible): sin secreto no vale.
        $this->assertNull(EnlaceRecuperacionPassword::abrir(EnlaceRecuperacionPassword::emitir(7, 'H-dlbv', 'hash')));
    }
}
