<?php

declare(strict_types=1);

namespace src\shared\domain\contracts;

use src\shared\domain\DatosCampo;

/**
 * Contrato mínimo de entidades usadas por DatosTablaRepo, DatosFormRepo y DatosUpdateRepo.
 */
interface DatosFichaInterface
{
    /**
     * @return string|array<string, mixed>
     */
    public function getPrimary_key(): string|array;

    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array;
}
