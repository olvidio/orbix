<?php

declare(strict_types=1);

namespace src\misas\application;

/**
 * @see misas_ver_misas_zona_build()
 */
class VerMisasZonaData
{
    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public static function build(array $in): array
    {
        require_once __DIR__ . '/ver_misas_zona_data_build.php';

        return \misas_ver_misas_zona_build($in);
    }
}
