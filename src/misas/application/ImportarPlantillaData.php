<?php

declare(strict_types=1);

namespace src\misas\application;

/**
 * @see misas_importar_plantilla_build()
 */
class ImportarPlantillaData
{
    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public static function build(array $in): array
    {
        require_once __DIR__ . '/importar_plantilla_data_build.php';

        return \misas_importar_plantilla_build($in);
    }
}
