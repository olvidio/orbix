<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use core\ViewPhtml;
use planning\domain\Planning;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

$Qdd = (integer)filter_input(INPUT_POST, 'dd');
$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$QsIniPlanning = (string)filter_input(INPUT_POST, 'sIniPlanning');
$QsFinPlanning = (string)filter_input(INPUT_POST, 'sFinPlanning');
$Qsactividades = (string)filter_input(INPUT_POST, 'sactividades');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qnueva = (string)filter_input(INPUT_POST, 'nueva');
$Qdoble = (string)filter_input(INPUT_POST, 'doble');

$Qa_actividades = json_decode(core\urlsafe_b64decode($Qsactividades));
$QoIniPlanning = unserialize(base64_decode($QsIniPlanning), ['allowed_classes' => true]);
$QoFinPlanning = unserialize(base64_decode($QsFinPlanning), ['allowed_classes' => true]);

/* TODO: comprobar que tiene permiso para crear algo. Sino: $Qmod = 0; */

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/apps/planning/controller/leyenda.php?' . http_build_query(array('id_item' => 1)));
$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
switch ($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
        $css = file_get_contents(ConfigGlobal::$dir_estilos .'/calendario.css.php');
        break;
    case 3:
        $css = file_get_contents(ConfigGlobal::$dir_estilos .'/calendario_grid.css.php');
        //include_once('apps/web/calendario_grid.php');
        break;
}
// Las variables de color de las columnas están en la página css.
include_once(ConfigGlobal::$dir_estilos . '/calendario_color_cols.css.php');
$oPlanning = new Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$oPlanning->setDd($Qdd);
$oPlanning->setCabecera($Qcabecera);
$oPlanning->setInicio($QoIniPlanning);
$oPlanning->setFin($QoFinPlanning);
$oPlanning->setActividades($Qa_actividades);
$oPlanning->setMod($Qmod);
$oPlanning->setNueva($Qnueva);
$oPlanning->setDoble($Qdoble);

$cabecera_title = ucfirst(_("casas"));

$a_campos = [
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
    'css' => $css,
];

$oView = new ViewPhtml('planning\controller');
$oView->renderizar('planning_casa_ver.phtml', $a_campos);