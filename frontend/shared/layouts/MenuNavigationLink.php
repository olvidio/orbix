<?php

declare(strict_types=1);

namespace frontend\shared\layouts;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashF;

/**
 * Compone en frontend la navegación firmada de los menús recibidos desde `src/menus`.
 */
final class MenuNavigationLink
{
    /**
     * @param array{path:string,parametros:string}|mixed $spec
     * @return array{full_url:string,parametros:string}
     */
    public static function fromSpec(mixed $spec): array
    {
        if (!is_array($spec)) {
            return self::empty();
        }

        $path = $spec['path'] ?? null;
        if (!is_string($path) || $path === '') {
            return self::empty();
        }

        $parametros = $spec['parametros'] ?? '';
        if (!is_string($parametros)) {
            return self::empty();
        }

        $fullUrl = AppUrlConfig::browserUrlFromAppRelative($path);
        if ($fullUrl === '') {
            return self::empty();
        }

        return [
            'full_url' => $fullUrl,
            'parametros' => HashF::add_hash($parametros, $fullUrl),
        ];
    }

    /** @return array{full_url:string,parametros:string} */
    private static function empty(): array
    {
        return [
            'full_url' => '',
            'parametros' => '',
        ];
    }
}
