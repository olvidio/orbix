<?php

use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use frontend\shared\web\Desplegable;

/*
* Devuelvo un desplegable con los valores posibles según el valor de entrada.
*
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsalida = (string)filter_input(INPUT_POST, 'salida');
$Qentrada = (string)filter_input(INPUT_POST, 'entrada');
switch ($Qsalida) {
    case "lugar":
        if (empty($Qentrada)) die();

        $region = $Qentrada;

        $repoDl = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        // Filtrar delegaciones por región si se facilita
        $aWhere = ['active' => true];
        if (!empty($region)) { $aWhere['region'] = $region; }
        $cDelegaciones = $repoDl->getDelegaciones($aWhere, ['_ordre' => 'dl']);
        // poner el valor del desplegable igual al texto, no id.
        $aOpciones = [];
        if (is_array($cDelegaciones)) {
            foreach ($cDelegaciones as $oDeleg) {
                $dl = $oDeleg->getDlVo()->value();
                $aOpciones[$dl] = $dl;
            }
        }
        // Añadir gestión global (region)
        $aOpciones[$region] = _("para gestión global");

        $oDesplDelegaciones = new Desplegable();
        $oDesplDelegaciones->setOpciones($aOpciones);
        $oDesplDelegaciones->setNombre('dl');
        echo $oDesplDelegaciones->desplegable();
        break;
}
