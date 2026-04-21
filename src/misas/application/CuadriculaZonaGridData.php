<?php

declare(strict_types=1);

namespace src\misas\application;

/**
 * @see misas_cuadricula_zona_grid_build()
 */
class CuadriculaZonaGridData
{
    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public static function build(array $in): array
    {
        require_once __DIR__ . '/cuadricula_zona_grid_data_build.php';

        return \misas_cuadricula_zona_grid_build($in);
    }
}
