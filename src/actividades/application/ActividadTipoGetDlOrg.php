<?php

namespace src\actividades\application;

use core\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;
use web\Desplegable;

/**
 * Devuelve el HTML del desplegable de delegaciones organizadoras para el sfsv
 * indicado en `entrada`. Portado del case `dl_org` del dispatcher legacy.
 */
class ActividadTipoGetDlOrg
{
    public function execute(array $input = []): string
    {
        $sfsv = (string)($input['entrada'] ?? '');
        $dl_default = ConfigGlobal::mi_delef($sfsv);

        $opciones = DelegacionDropdown::delegacionesURegiones($sfsv, true);
        $oDesplDelegacionesOrg = Desplegable::desdeOpciones($opciones, 'dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($dl_default);

        return $oDesplDelegacionesOrg->desplegable();
    }
}
