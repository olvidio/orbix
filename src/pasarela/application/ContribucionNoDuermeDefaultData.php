<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Devuelve solo el valor por defecto del parámetro `contribucion_no_duerme`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ContribucionNoDuermeDefaultData
{
    public function __construct(
        private readonly ContribucionNoDuerme $contribucionNoDuerme,
    ) {
    }

    /**
     * @return array{default: string}
     */
    public function execute(): array
    {
        
        return [
            'default' => (string)$this->contribucionNoDuerme->getDefault(),
        ];
    }
}
