<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Devuelve solo el valor por defecto del parámetro `contribucion_no_duerme`,
 * para alimentar el formulario `form_default` desde el frontend.
 */
final class ContribucionNoDuermeDefaultData
{
    public static function execute(): array
    {
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        return [
            'default' => (string)$oContribucionNoDuerme->getDefault(),
        ];
    }
}
