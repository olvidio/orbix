<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Inserta o actualiza una excepción del parámetro `contribucion_no_duerme`
 * para un `id_tipo_activ` concreto.
 */
final class ContribucionNoDuermeExcepcionGuardar
{
    public static function execute(string $id_tipo_activ, string $valor): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        if ($valor === '') {
            return _('Falta valor de contribución');
        }
        if (!is_numeric($valor) || (int)$valor < 0 || (int)$valor > 100) {
            return _('Debe ser un numero entero del 1 al 100');
        }
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->addContribucionNoDuerme($id_tipo_activ, (int)$valor);
        return '';
    }
}
