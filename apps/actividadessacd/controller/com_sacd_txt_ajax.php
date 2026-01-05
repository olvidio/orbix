<?php

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');
$Qclave = (string)filter_input(INPUT_POST, 'clave');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');

// si es tipo ca_ES.UTF-8, quedarme sólo con 'ca'
$Qidioma = substr($Qidioma, 0, strpos($Qidioma, '_'));


switch ($Qque) {
    case 'get_texto':
        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
        $cAtnActivTextos = $ActividadSacdTextoRepository->getActividadSacdTextos($aWhere);
        $txt = '';
        if (count($cAtnActivTextos) > 0) {
            $txt = $cAtnActivTextos[0]->getTexto();
        }
        echo $txt;
        break;
    case 'update':
        $Qcomunicacion = (string)filter_input(INPUT_POST, 'comunicacion');

        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
        $cAtnActivTextos = $ActividadSacdTextoRepository->getActividadSacdTextos($aWhere);
        $txt = '';
        if (count($cAtnActivTextos) > 0) {
            $oAtnActivSacdTexto = $cAtnActivTextos[0];
            if (empty($Qcomunicacion)) {
                $ActividadSacdTextoRepository->Eliminar($oAtnActivSacdTexto);
            } else {
                $oAtnActivSacdTexto->setTexto($Qcomunicacion);
                $ActividadSacdTextoRepository->Guardar($oAtnActivSacdTexto);
            }
        } else {
            $newIdItem = $ActividadSacdTextoRepository->getNewId();
            $oAtnActivSacdTexto = new ActividadSacdTexto();
            $oAtnActivSacdTexto->setId_item($newIdItem);
            $oAtnActivSacdTexto->setClave($Qclave);
            $oAtnActivSacdTexto->setIdioma($Qidioma);
            $oAtnActivSacdTexto->setTexto($Qcomunicacion);
            $ActividadSacdTextoRepository->Guardar($oAtnActivSacdTexto);
        }
        break;
}