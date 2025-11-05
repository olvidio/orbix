<?php

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qs_pkey = (string)filter_input(INPUT_POST, 's_pkey');
$Qid_pau = (string)filter_input(INPUT_POST, 'id_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qgo_to = (string)filter_input(INPUT_POST, 'go_to');

// Cuando es eliminar, viene directamente de la tabla (mod_tabla_sql)
// Como es borrar, no hace falta mantener el scroll
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$stack = '';
if (!empty($a_sel)) { //vengo de un checkbox
    $Qs_pkey = explode('#', $a_sel[0]);
    // he cambiado las comillas dobles por simples. Deshago el cambio.
    $Qs_pkey = str_replace("'", '"', $Qs_pkey[0]);
}

$a_pkey = unserialize(core\urlsafe_b64decode($Qs_pkey), ['allowed_classes' => false]);

// Tiene que ser en dos pasos.
$obj = $Qclase_info;
$oInfoClase = new $obj();
$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey); //Para eliminar y editar
$oInfoClase->setId_pau($Qid_pau); //Para nuevo
$oInfoClase->setObj_pau($Qobj_pau); //Imprescindible para dossiers complejos.
$oFicha = $oInfoClase->getFicha();

$oDatosUpdate = new core\DatosUpdate();
$oDatosUpdate->setFicha($oFicha);

// campos del dossier (de hecho todo el $_POST, porque desconozco...)
$oDatosUpdate->setCampos($_POST);

$rta = _("no se ha ejecutado la acción");
switch ($Qmod) {
    case 'eliminar':
        $rta = $oDatosUpdate->eliminar();
        break;
    case 'editar':
        $rta = $oDatosUpdate->editar();
        break;
    case 'nuevo':
        $rta = $oDatosUpdate->nuevo();
        break;
}

if ($rta !== true) {
    $msg_err = "$rta";
    echo $msg_err;
}