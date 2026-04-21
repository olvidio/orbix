<?php

declare(strict_types=1);

namespace src\misas\application;

/**
 * @see misas_crear_nuevo_periodo_build()
 */
class CrearNuevoPeriodoData
{
    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public static function build(array $in): array
    {
        require_once __DIR__ . '/crear_nuevo_periodo_data_build.php';

        return \misas_crear_nuevo_periodo_build($in);
    }
}
