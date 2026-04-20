<?php

namespace src\actividades\application;

use web\Desplegable;
use web\TiposActividades;

use function core\is_true;

/**
 * Devuelve el HTML del desplegable de actividades posibles. Portado del case
 * `actividad` del dispatcher legacy actividad_tipo_get.php.
 */
class ActividadTipoGetActividad
{
    public function execute(array $input = []): string
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qextendida = (string)($input['extendida'] ?? '');
        $extendida = (bool)is_true($Qextendida);

        $aux = $Qentrada . '....';
        $oTipoActiv = new TiposActividades($aux);

        if ($extendida) {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles2Digitos();
            $opcion_blanco = '..';
        } else {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles1Digito();
            $opcion_blanco = '.';
        }

        $oDespl = new Desplegable('iactividad_val', $a_actividades_posibles, '', true);
        $oDespl->setAction('fnjs_nom_tipo()');
        $oDespl->setValBlanco($opcion_blanco);
        $oDespl->setOpcion_sel($opcion_blanco);

        return $oDespl->desplegable();
    }
}
