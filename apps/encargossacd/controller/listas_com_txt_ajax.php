<?php

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');
$Qclave = (string)filter_input(INPUT_POST, 'clave');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');

$EncargoTextoRepository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
switch ($Qque) {
    case 'get_texto':
        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos($aWhere);
        $txt = '';
        if (count($cEncargoTextos) > 0) {
            $txt = $cEncargoTextos[0]->getTexto();
        }
        echo $txt;
        break;
    case 'update':
        $Qcomunicacion = (string)filter_input(INPUT_POST, 'comunicacion');

        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $cEncargoTextos = $EncargoTextoRepository->getEncargoTextos($aWhere);
        $txt = '';
        if (count($cEncargoTextos) > 0) {
            $oEncargoTexto = $cEncargoTextos[0];
            if (empty($Qcomunicacion)) {
                $EncargoTextoRepository->Eliminar($oEncargoTexto);
            } else {
                $oEncargoTexto->setTexto($Qcomunicacion);
                $EncargoTextoRepository->Guardar($oEncargoTexto);
            }
        } else {
            $newId = $EncargoTextoRepository->getNewId();
            $oEncargoTexto = new EncargoTexto();
            $oEncargoTexto->setId_item($newId);
            $oEncargoTexto->setClave($Qclave);
            $oEncargoTexto->setIdioma($Qidioma);
            $oEncargoTexto->setTexto($Qcomunicacion);
            $EncargoTextoRepository->Guardar($oEncargoTexto);
        }
        break;
}