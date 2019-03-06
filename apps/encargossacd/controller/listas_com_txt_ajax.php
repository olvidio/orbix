<?php
use encargossacd\model\entity\GestorEncargoTexto;
use encargossacd\model\entity\EncargoTexto;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qclave = (string) \filter_input(INPUT_POST, 'clave');
$Qidioma = (string) \filter_input(INPUT_POST, 'idioma');

switch ($Qque) {
    case 'get_texto':
        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $oGesEncargoTextos = new GestorEncargoTexto();
        $cEncargoTextos = $oGesEncargoTextos->getEncargoTextos($aWhere);
        $txt = '';
        if (count($cEncargoTextos) > 0) {
            $txt = $cEncargoTextos[0]->getTexto();
        }
        echo $txt; 
		break;
    case 'update':
        $Qcomunicacion = (string) \filter_input(INPUT_POST, 'comunicacion');
        
        $aWhere = [];
        $aWhere['clave'] = $Qclave;
        $aWhere['idioma'] = $Qidioma;
        $oGesEncargoTextos = new GestorEncargoTexto();
        $cEncargoTextos = $oGesEncargoTextos->getEncargoTextos($aWhere);
        $txt = '';
        if (count($cEncargoTextos) > 0) {
            $oEncargoTexto = $cEncargoTextos[0];
            $oEncargoTexto->DBCarregar();
            if (empty($Qcomunicacion)) {
                $oEncargoTexto->DBEliminar(); 
            } else {
                $oEncargoTexto->setTexto($Qcomunicacion);
                $oEncargoTexto->DBGuardar();
            }
        } else {
            $oEncargoTexto = new EncargoTexto();
            $oEncargoTexto->setClave($Qclave);
            $oEncargoTexto->setIdioma($Qidioma);
            $oEncargoTexto->setTexto($Qcomunicacion);
            $oEncargoTexto->DBGuardar();
        }
        break;
}