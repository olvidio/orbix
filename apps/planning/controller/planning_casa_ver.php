<?php

// INICIO Cabecera global de URL de controlador *********************************

use core\ConfigGlobal;
use planning\domain\Planning;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
switch ($Qmodelo) {
    case 2:
        $print = 1;
    case 1:
    default:
        include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        //include_once('apps/web/calendario.php');
        break;
    case 3:
        include_once(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// para los estilos. Las variables están en la página css.
$oPlanning = new Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$Qdd = (integer)filter_input(INPUT_POST, 'dd');
$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$QsIniPlanning = (string)filter_input(INPUT_POST, 'sIniPlanning');
$QsFinPlanning = (string)filter_input(INPUT_POST, 'sFinPlanning');
$Qsactividades = (string)filter_input(INPUT_POST, 'sactividades');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qnueva = (string)filter_input(INPUT_POST, 'nueva');
$Qdoble = (string)filter_input(INPUT_POST, 'doble');

$Qa_actividades = unserialize(base64_decode($Qsactividades), ['allowed_classes' => false]);
$QoIniPlanning = unserialize(base64_decode($QsIniPlanning), ['allowed_classes' => true]);
$QoFinPlanning = unserialize(base64_decode($QsFinPlanning), ['allowed_classes' => true]);

/* TODO: comprobar que tiene permiso para crear algo. Sino: $Qmod = 0; */

$oPlanning->setDd($Qdd);
$oPlanning->setCabecera($Qcabecera);
$oPlanning->setInicio($QoIniPlanning);
$oPlanning->setFin($QoFinPlanning);
$oPlanning->setActividades($Qa_actividades);
$oPlanning->setMod($Qmod);
$oPlanning->setNueva($Qnueva);
$oPlanning->setDoble($Qdoble);

echo $oPlanning->dibujar();