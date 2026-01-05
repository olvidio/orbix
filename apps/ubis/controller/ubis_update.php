<?php

use core\ConfigGlobal;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use web\ContestarJson;
use function core\is_true;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = ConfigGlobal::MiUsuario();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qtipo_ubi = (string)filter_input(INPUT_POST, 'tipo_ubi');

$Qdl = (string)filter_input(INPUT_POST, 'dl');
$Qregion = (string)filter_input(INPUT_POST, 'region');
$Qactive = (string)filter_input(INPUT_POST, 'active'); // checkbox, puede ser tipo 'on' o 'off'
$Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');
$Qsv = (string)filter_input(INPUT_POST, 'sv'); // checkbox, puede ser tipo 'on' o 'off'
$Qsf = (string)filter_input(INPUT_POST, 'sf'); // checkbox, puede ser tipo 'on' o 'off'


$repo = 'src\\ubis\\application\\repositories\\' . $Qobj_pau. 'Repository';
$Repository = new $repo();
$oUbi = $Repository->findById($Qid_ubi);


// casas
if ($Qobj_pau === 'CasaDl' || $Qobj_pau === 'CasaEx') {
    $Qtipo_casa = (string)filter_input(INPUT_POST, 'tipo_casa');
    $Qplazas = (integer)filter_input(INPUT_POST, 'plazas');
    $Qplazas_min = (integer)filter_input(INPUT_POST, 'plazas_min');
    $Qnum_sacd = (integer)filter_input(INPUT_POST, 'num_sacd');
    //$Qbiblioteca = (integer)filter_input(INPUT_POST, 'biblioteca');
    //$Qobserv = (integer)filter_input(INPUT_POST, 'observ');

    if (empty($oUbi)) {
        $oUbi = new Casa();
        $id = $Repository->getNewId();
        $id_ubi = $Repository->getNewIdUbi($id);
        $oUbi->setId_auto($id);
        $oUbi->setId_ubi($id_ubi);
    }
    $oUbi->setTipo_ubi($Qtipo_ubi);
    $oUbi->setNombre_ubi($Qnombre_ubi);
    $oUbi->setDl($Qdl);
    // pais
    $oUbi->setRegion($Qregion);
    $oUbi->setActive($Qactive);
    //$oUbi->setF_active($Qf_active);
    $oUbi->setSv($Qsv);
    $oUbi->setSf($Qsf);
    $oUbi->setTipo_casa($Qtipo_casa);
    $oUbi->setPlazas($Qplazas);
    $oUbi->setPlazas_min($Qplazas_min);
    $oUbi->setNum_sacd($Qnum_sacd);
    //$oUbi->setBiblioteca($Qbiblioteca);
    //$oUbi->setObserv($Qobserv);
}

// centros
if ($Qobj_pau === 'CentroDl' || $Qobj_pau === 'CentroEx') {
    $Qtipo_ctr = (string)filter_input(INPUT_POST, 'tipo_ctr');
    $aTipo_labor = (array)filter_input(INPUT_POST, 'tipo_labor', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qcdc = (string)filter_input(INPUT_POST, 'cdc'); // checkbox, puede ser tipo 'on' o 'off'
    $Qid_ctr_padre = (integer)filter_input(INPUT_POST, 'id_ctr_padre');
    $Qn_buzon = (int)filter_input(INPUT_POST, 'n_buzon');
    $Qnum_pi = (int)filter_input(INPUT_POST, 'num_pi');
    $Qnum_cartas = (int)filter_input(INPUT_POST, 'num_cartas');
    $Qobserv = (string)filter_input(INPUT_POST, 'observ');
    $Qnum_habit_indiv = (int)filter_input(INPUT_POST, 'num_habit_indiv');
    $Qplazas = (integer)filter_input(INPUT_POST, 'plazas');
    //$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
    //$Qsede = (string)filter_input(INPUT_POST, 'sede');
    $Qnum_cartas_mensuales = (int)filter_input(INPUT_POST, 'num_cartas_mensuales');


    if (empty($oUbi)) {
        $oUbi = new Centro();
        $id = $Repository->getNewId();
        $id_ubi = $Repository->getNewIdUbi($id);
        $oUbi->setIdAuto($id);
        $oUbi->setId_ubi($id_ubi);
    }
    $oUbi->setTipo_ubi($Qtipo_ubi);
    $oUbi->setNombre_ubi($Qnombre_ubi);
    $oUbi->setDl($Qdl);
    // pais
    $oUbi->setRegion($Qregion);
    $oUbi->setActive($Qactive);
    //$oUbi->setF_active($Qf_active);
    $oUbi->setSv($Qsv);
    $oUbi->setSf($Qsf);
    $oUbi->setTipo_ctr($Qtipo_ctr);
    $oUbi->setCdc($Qcdc);
    $oUbi->setId_ctr_padre($Qid_ctr_padre);
    $oUbi->setN_buzon($Qn_buzon);
    $oUbi->setNum_pi($Qnum_pi);
    $oUbi->setNum_cartas($Qnum_cartas);
    $oUbi->setObserv($Qobserv);
    $oUbi->setNum_habit_indiv($Qnum_habit_indiv);
    $oUbi->setPlazas($Qplazas);
    //$oUbi->setId_zona($Qid_zona);
    //$oUbi->setSede($Qsede);
    $oUbi->setNum_cartas_mensuales($Qnum_cartas_mensuales);
    if (!empty($aTipo_labor) && (count($aTipo_labor) > 0)) {
        $byte = 0;
        foreach ($aTipo_labor as $bit) {
            $byte = $byte + $bit;
        }
        $valor = $byte;
        $oUbi->setTipo_labor($valor);
    }
}


/*
if ($camp === "tipo_labor") {
    $byte = 0;
    foreach ($_POST[$camp] as $bit) {
        $byte = $byte + $bit;
    }
    $valor = $byte;
}
*/

$error_txt = '';
if ($Repository->Guardar($oUbi) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $Repository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
