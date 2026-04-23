<?php

namespace frontend\procesos\support;

use web\Hash;

/**
 * Helper para construir los `Hash` repetitivos de las pantallas de procesos
 * (`procesos_select`, `procesos_ver`, `tipo_activ_proceso`, ...).
 *
 * Usa el patron `setUrl + setCamposForm + linkSinValParams()` que se repite
 * una y otra vez por cada boton/AJAX de la pantalla.
 */
final class ProcesosHashes
{
    /**
     * Genera el sufijo "&hnov=1&h=..." para un POST ajax hacia `$url`
     * firmando los campos indicados por `!` (p.e. `id_item!id_tipo_proceso`).
     */
    public static function formLink(string $url, string $camposForm = ''): string
    {
        $oHash = new Hash();
        $oHash->setUrl($url);
        $oHash->setCamposForm($camposForm);
        return $oHash->linkSinValParams();
    }
}
