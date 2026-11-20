<?php

declare(strict_types=1);

namespace frontend\shared\security;

/**
 * Quién puede pedir firmas al firmador de URL de la suite E2E (`frontend/shared/controller/e2e_firmar.php`).
 *
 * Da firmas `h` a la carta, así que no basta con estar en una instancia de pruebas (`WEBDIR=pruebas`
 * también lo es el entorno de ensayo de una instalación real): la instancia tiene que tener definido el
 * secreto `ORBIX_E2E_FIRMADOR` y la petición tiene que presentarlo.
 */
final class FirmadorE2e
{
    public const VARIABLE = 'ORBIX_E2E_FIRMADOR';
    public const CABECERA = 'HTTP_X_ORBIX_E2E';
    private const LARGO_MINIMO = 32;

    public static function permitido(bool $esInstanciaDePruebas, string $secreto, string $presentado): bool
    {
        return $esInstanciaDePruebas
            && strlen($secreto) >= self::LARGO_MINIMO
            && hash_equals($secreto, $presentado);
    }

    public static function secreto(): string
    {
        $valor = $_ENV[self::VARIABLE] ?? getenv(self::VARIABLE);

        return is_string($valor) ? $valor : '';
    }
}
