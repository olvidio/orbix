<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_app = (integer)filter_input(INPUT_POST, 'id_app');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema'); // esquema con la v o f.
$Qaccion = (string)filter_input(INPUT_POST, 'accion');

$a_todasApps = $_SESSION['config']['a_apps'];
$nom_app = array_search($Qid_app, $a_todasApps);

$clase_global = "$nom_app\\db\\DB";
$clase_esquema = "$nom_app\\db\\DBEsquema";

$clase_global_src = 'src\\'."$nom_app\\db\\DB";
$clase_esquema_src = 'src\\'."$nom_app\\db\\DBEsquema";

switch ($Qaccion) {
    case 'crear_global':
        if (class_exists($clase_global)) {
            $ClaseGlobal = new $clase_global();
            $ClaseGlobal->createAll();
        }
        if (class_exists($clase_global_src)) {
            $ClaseGlobal = new $clase_global_src();
            $ClaseGlobal->createAll();
        }
        break;
    case 'eliminar_global':
        if (class_exists($clase_global)) {
            $ClaseGlobal = new $clase_global();
            $ClaseGlobal->dropAll();
        }
        if (class_exists($clase_global_src)) {
            $ClaseGlobal = new $clase_global_src();
            $ClaseGlobal->dropAll();
        }
        break;
    case 'crear_esquema':
        if (class_exists($clase_esquema)) {
            $ClaseEsquema = new $clase_esquema($Qesquema);
            $ClaseEsquema->createAll();
        }
        if (class_exists($clase_esquema_src)) {
            $ClaseEsquema = new $clase_esquema_src($Qesquema);
            $ClaseEsquema->createAll();
        }
        break;
    case 'eliminar_esquema':
        if (class_exists($clase_esquema)) {
            $ClaseEsquema = new $clase_esquema($Qesquema);
            $ClaseEsquema->dropAll();
        }
        if (class_exists($clase_esquema_src)) {
            $ClaseEsquema = new $clase_esquema_src($Qesquema);
            $ClaseEsquema->dropAll();
        }
        break;
    case 'llenar_esquema':
        if (class_exists($clase_esquema)) {
            $ClaseEsquema = new $clase_esquema($Qesquema);
            $ClaseEsquema->llenarAll();
        }
        if (class_exists($clase_esquema_src)) {
            $ClaseEsquema = new $clase_esquema_src($Qesquema);
            $ClaseEsquema->llenarAll();
        }
        break;
}