<?php

namespace src\pasarela\application;

use src\pasarela\domain\ContribucionNoDuerme;

/**
 * Elimina una excepción del parámetro `contribucion_no_duerme` para un
 * `id_tipo_activ` concreto.
 */
final class ContribucionNoDuermeExcepcionEliminar
{
    public static function execute(string $id_tipo_activ): string
    {
        if ($id_tipo_activ === '') {
            return _('Falta id_tipo_activ');
        }
        $oContribucionNoDuerme = new ContribucionNoDuerme();
        $oContribucionNoDuerme->delContribucionNoDuerme($id_tipo_activ);
        return '';
    }
}
