<?php

namespace src\actividades\application;

use web\Desplegable;
use web\TiposActividades;

use function core\is_true;

/**
 * Devuelve el HTML del desplegable de asistentes posibles segun el
 * sfsv/seccion recibido en `entrada`. Portado del case `asistentes` del
 * dispatcher legacy actividad_tipo_get.php.
 */
class ActividadTipoGetAsistentes
{
    public function execute(array $input = []): string
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qextendida = (string)($input['extendida'] ?? '');
        $extendida = (bool)is_true($Qextendida);

        $aux = $Qentrada . '.....';
        $oTipoActiv = new TiposActividades($aux);
        $a_asistentes_posibles = $oTipoActiv->getAsistentesPosibles();

        // La opcion en blanco solo es valida para des o calendario.
        $blanco = false;
        if (isset($_SESSION['oPerm'])
            && ($_SESSION['oPerm']->have_perm_oficina('des')
                || $_SESSION['oPerm']->have_perm_oficina('calendario'))
        ) {
            $blanco = true;
        }

        $oDespl = new Desplegable('iasistentes_val', $a_asistentes_posibles, '', $blanco);
        $oDespl->setAction('fnjs_actividad(' . ($extendida ? 'true' : 'false') . ')');
        $oDespl->setValBlanco('.');
        $oDespl->setOpcion_sel('.');

        return $oDespl->desplegable();
    }
}
