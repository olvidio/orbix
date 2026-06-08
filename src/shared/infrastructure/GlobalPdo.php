<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use PDO;
use RuntimeException;

/**
 * Acceso tipado a conexiones PDO del bootstrap (`ConnectionBootstrap` → `BootstrapPdoGlobals`).
 * El código de aplicación/repos debe usar {@see self::get()} en lugar de `$GLOBALS['oDB*']`.
 */
final class GlobalPdo
{
    public static function get(string $globalKey): PDO
    {
        $pdo = $GLOBALS[$globalKey] ?? null;
        if (!$pdo instanceof PDO) {
            throw new RuntimeException(sprintf('PDO global %s no disponible', $globalKey));
        }

        return $pdo;
    }
}
