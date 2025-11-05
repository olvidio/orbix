<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$Qid_mod = (integer)filter_input(INPUT_POST, 'id_mod');
$Qmod = (string)filter_input(INPUT_POST, 'mod');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qid_mod = urldecode(strtok($a_sel[0], "#"));
}

$Qaccion = (string)filter_input(\INPUT_POST, 'accion');
$Qaccion = 'crear';

$oModulo = new devel\model\entity\Modulo($Qid_mod);
$mod_nom = $oModulo->getNom();

switch ($Qmod) {
    case 'global':
        /*
         * Generar las tablas a nivel global
         */
        $clase_global = "$mod_nom\\db\\DB";
        if (class_exists($clase_global)) {
            $ClaseGlobal = new $clase_global();
            if ($Qaccion == 'crear') {
                $ClaseGlobal->createAll();
            }
            if ($Qaccion == 'eliminar') {
                $ClaseGlobal->dropAll();
            }
        }
        break;
    case 'esquema':
        /*
         * Genera las tablas del esquema correspondiente
         */
        $clase_esquema = "$mod_nom\\db\\DBEsquema";
        if (class_exists($clase_esquema)) {
            $ClaseEsquema = new $clase_esquema();
            if ($Qaccion == 'crear') {
                $ClaseEsquema->createAll();
            }
            if ($Qaccion == 'eliminar') {
                $ClaseEsquema->dropAll();
            }
        }
        break;
}