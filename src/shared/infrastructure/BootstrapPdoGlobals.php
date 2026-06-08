<?php

declare(strict_types=1);

namespace src\shared\infrastructure;

use PDO;

/**
 * Publica conexiones PDO del bootstrap en `$GLOBALS` para `GlobalPdo` y repos legacy.
 */
final class BootstrapPdoGlobals
{
    /** @var list<string> */
    public const PDO_GLOBAL_KEYS = [
        'oDBPC', 'oDBRC', 'oDBPC_Select', 'oDBRC_Select',
        'oDBC', 'oDBC_Select', 'oDB', 'oDBP', 'oDBR',
        'oDBE', 'oDBEP', 'oDBER', 'oDBE_Select', 'oDBEP_Select', 'oDBER_Select',
        'oDBF', 'oDBListas',
    ];

    /**
     * @param array<string, PDO|string|null> $connections
     */
    public static function register(int|string $userSfsv, array $connections): void
    {
        $GLOBALS['user_sfsv'] = $userSfsv;

        foreach (self::PDO_GLOBAL_KEYS as $key) {
            if (!array_key_exists($key, $connections)) {
                continue;
            }
            $value = $connections[$key];
            if ($value === null) {
                continue;
            }
            $GLOBALS[$key] = $value;
        }
    }
}
