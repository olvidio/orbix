<?php

use actividades\model\entity as actividades;
use core\DBPropiedades;
use web\Desplegable;
use function core\is_true;
use core\ConfigGlobal;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oPosicion->recordar();

$a_sel = (array)\filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_activ = (integer)strtok($a_sel[0], "#");
    $nom_activ = strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)\filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else { // vengo de actualizar
    $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $nom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
    // borro la última
    $stack = $oPosicion->getStack() - 1;
    $oPosicion2 = new web\Posicion();
    $oPosicion2->olvidar($stack);
}

$oDesplDelegaciones = new Desplegable();
$oDesplDelegaciones->setNombre('region_dl');
//$gesDelegacion = new ubis\model\entity\GestorDelegacion();
$oDBPropiedades = new DBPropiedades();
$aOpcionesDl = $oDBPropiedades->array_posibles_esquemas(TRUE);
$oDesplDelegaciones->setOpciones($aOpcionesDl);

// comprobar que la actividad está publicada, sino: avisar!
$publicado = '';
$oActividad = new actividades\Actividad($id_activ);
$publicado = $oActividad->getPublicado();
// Si no es una actividad de la dl, publicado da NULL (igual que todos los campos)
if (!is_true($publicado) || $publicado === null) {
    $publicado = FALSE;
}
// avisar si es de otra dl:
$otra_dl = FALSE;
if ($oActividad->getDl_org() != ConfigGlobal::mi_delef()) {
    $otra_dl = TRUE;
}

$gesActividadPlazas = new \actividadplazas\model\GestorResumenPlazas();
$gesActividadPlazas->setId_activ($id_activ);
$a_plazas = $gesActividadPlazas->getResumen();

$plazas_totales = empty($a_plazas['total']['actividad']) ? 0 : $a_plazas['total']['actividad'];
$tot_calendario = empty($a_plazas['total']['calendario']) ? 0 : $a_plazas['total']['calendario'];
$tot_cedidas = empty($a_plazas['total']['cedidas']) ? 0 : $a_plazas['total']['cedidas'];
$tot_conseguidas = empty($a_plazas['total']['conseguidas']) ? 0 : $a_plazas['total']['conseguidas'];
$tot_disponibles = empty($a_plazas['total']['disponibles']) ? 0 : $a_plazas['total']['disponibles'];
$tot_ocupadas = empty($a_plazas['total']['ocupadas']) ? 0 : $a_plazas['total']['ocupadas'];

$oHash = new web\Hash();
$camposForm = 'num_plazas!region_dl';
$a_camposHidden = array(
    'id_activ' => $id_activ,
    'que' => 'ceder'
);
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$oHashActualizar = new web\Hash();
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = array(
    'id_activ' => $id_activ,
    'nom_activ' => $nom_activ
);
$oHashActualizar->setArraycamposHidden($a_camposHiddenActualizar);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashActualizar' => $oHashActualizar,
    'oHash' => $oHash,
    'publicado' => $publicado,
    'otra_dl' => $otra_dl,
    'nom_activ' => $nom_activ,
    'a_plazas' => $a_plazas,
    'tot_calendario' => $tot_calendario,
    'plazas_totales' => $plazas_totales,
    'tot_cedidas' => $tot_cedidas,
    'tot_conseguidas' => $tot_conseguidas,
    'tot_disponibles' => $tot_disponibles,
    'tot_ocupadas' => $tot_ocupadas,
    'oDesplDelegaciones' => $oDesplDelegaciones,
];

$oView = new core\View('actividadplazas/controller');
echo $oView->render('resumen_plazas.phtml', $a_campos);