<?php

namespace src\actividades\application;

use web\Desplegable;
use web\TiposActividades;

use function core\is_true;

/**
 * Devuelve el HTML del desplegable de nombres de tipo de actividad. Portado
 * del case `nom_tipo` del dispatcher legacy.
 */
class ActividadTipoGetNomTipo
{
    public function execute(array $input = []): string
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qextendida = (string)($input['extendida'] ?? '');
        $Qmodo = (string)($input['modo'] ?? 'buscar');
        $extendida = (bool)is_true($Qextendida);

        if ($extendida) {
            $aux = $Qentrada . '..';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();
            $opcion_blanco = '..';
        } else {
            $aux = $Qentrada . '...';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles3Digitos();
            $opcion_blanco = '...';
        }

        $oDespl = new Desplegable('inom_tipo_val', $a_nom_tipo_posibles, '', true);
        $oDespl->setValBlanco($opcion_blanco);
        $oDespl->setOpcion_sel($opcion_blanco);
        if ($Qmodo === 'buscar') {
            $oDespl->setAction('fnjs_id_activ()');
        } else {
            $oDespl->setAction('fnjs_act_id_activ()');
        }

        return $oDespl->desplegable();
    }
}
