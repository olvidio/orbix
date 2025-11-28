<?php

use src\shared\domain\DatosUpdateRepo;
use web\ContestarJson;

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

$a_pkey = json_decode(core\urlsafe_b64decode($Qs_pkey));

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info);
$oInfoClase = new $obj();
$oInfoClase->setMod($Qmod);
$oInfoClase->setA_pkey($a_pkey); //Para eliminar y editar
$oInfoClase->setId_pau($Qid_pau); //Para nuevo
$oFicha = $oInfoClase->getFicha();

$clase_ficha = $oInfoClase->getClase();
$repo = str_replace('domain\entity', 'domain\contracts', $clase_ficha);
$repositoryInterface = $repo . 'RepositoryInterface';

$oDatosUpdate = new DatosUpdateRepo();
$oDatosUpdate->setRepositoryInterface($repositoryInterface);
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

$error_txt = '';
if ($rta !== true) {
    $error_txt = (string)$rta;
}
ContestarJson::enviar($error_txt, 'ok');
