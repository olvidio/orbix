<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Actualiza el valor por defecto del parámetro `contribucion_no_duerme`.
 */
final class ContribucionNoDuermeDefaultGuardar
{
    public static function execute(string $default): string
    {
        if ($default === '') {
            return _('Falta valor por defecto');
        }
        if (!is_numeric($default) || (int)$default < 0 || (int)$default > 100) {
            return _('Debe ser un numero entero del 1 al 100');
        }
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->setDefault((int)$default);
        return '';
    }
}
