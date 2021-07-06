<?php

use core\ConfigGlobal;
use ubis\model\TrasladoUbi;
use ubis\model\entity\Ubi;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//En el caso de modificar cartas de presentación, quiero que quede dentro del bloque.
$oPosicion->recordar();

$dl_dst = (string) \filter_input(INPUT_POST, 'dl_dst');
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (empty($a_sel)) {
    exit ("No se...");
}

$mi_region_dl = ConfigGlobal::mi_region_dl();
$esquema_org = substr($mi_region_dl,0,-1); // quito la v o la f.

$oTrasladoUbi = new TrasladoUbi();
$oTrasladoUbi->setEsquema_org($esquema_org);
$oTrasladoUbi->setEsquema_dst($dl_dst);

foreach($a_sel as $id_ubi) {
    // averiguar si es ctr o casa
    $oUbi = Ubi::NewUbi($id_ubi);
    
    $classname = str_replace("ubis\\model\\entity\\",'',get_class($oUbi));
    switch($classname) {
        case 'Centro':
        case 'CentroDl':
            $oTrasladoUbi->trasladoCtr($id_ubi);
            break;
        case 'Casa':
        case 'CasaDl':
            $oTrasladoUbi->trasladoCdc($id_ubi);
            break;
    }
    
}

