<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividadDl;
use actividades\model\entity\GestorActividadPub;
use actividadplazas\model\entity\GestorPlazaPeticion;
use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\PersonaDl;
use ubis\model\entity\GestorDelegacion;
use web\DesplegableArray;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtodos = (string)filter_input(INPUT_POST, 'todos');

$oPosicion->recordar();
//Si vengo de actualizar borro la ultima posicion
if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    $Qna = strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    $Qsactividad = (string)filter_input(INPUT_POST, 'que');
    $Qtodos = empty($Qtodos) ? 1 : $Qtodos;
} else { // vengo de actualizar
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');

}

if (($Qna === 'a' || $Qna === 'agd') && $Qsactividad === 'ca') {
    $Qsactividad = 'cv';
}

$oPersona = new PersonaDl($Qid_nom);
$ap_nom = $oPersona->getPrefApellidosNombre();

// Posibles:
if (!empty($Qtodos) && $Qtodos != 1) {
    $grupo_estudios = $Qtodos;
    $GesGrupoEst = new GestorDelegacion();
    $cDelegaciones = $GesGrupoEst->getDelegaciones(array('grupo_estudios' => $grupo_estudios));
    if (count($cDelegaciones) > 1) $aOperador['dl_org'] = 'OR';
    $mi_grupo = '';
    foreach ($cDelegaciones as $oDelegacion) {
        $mi_grupo .= empty($mi_grupo) ? '' : ',';
        $mi_grupo .= "'" . $oDelegacion->getDl() . "'";
    }
    $aWhere['dl_org'] = $mi_grupo;
}
//periodo
switch ($Qsactividad) {
    case 'ca':
    case 'cv':
        $any = $_SESSION['oConfig']->any_final_curs('est');
        $inicurs = core\curso_est("inicio", $any, "est")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "est")->format('Y-m-d');
        break;
    case 'crt':
        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $inicurs = core\curso_est("inicio", $any, "crt")->format('Y-m-d');
        $fincurs = core\curso_est("fin", $any, "crt")->format('Y-m-d');
        break;
}

$aWhere['f_ini'] = "'$inicurs','$fincurs'";
$aOperador['f_ini'] = 'BETWEEN';
$aWhere['status'] = ActividadAll::STATUS_ACTUAL;
$aWhere['_ordre'] = 'f_ini,nivel_stgr';

$cActividades = [];
$sfsv = ConfigGlobal::mi_sfsv();
$mi_dele = ConfigGlobal::mi_delef();
switch ($Qna) {
    case "agd":
    case "a":
        //caso de agd
        $id_ctr = (integer)filter_input(INPUT_POST, 'id_ctr_agd');
        if ($id_ctr == 1) $id_ctr = ''; //es todos los ctr.
        $id_tabla_persona = 'a'; //el id_tabla entra en conflicto con el de actividad
        $tabla_pau = 'p_agregados';

        switch ($Qsactividad) {
            case 'ca': //133
            case 'cv': //133
                $Qid_tipo_activ = '^' . $sfsv . '33';
                break;
            case 'crt':
                $Qid_tipo_activ = '^' . $sfsv . '31';
                break;
        }
        $aWhere['id_tipo_activ'] = $Qid_tipo_activ;
        $aOperador['id_tipo_activ'] = '~';
        //inicialmente estaba sólo con las activiades publicadas.
        //Ahora añado las no publicadas de midl.
        $GesActividadesDl = new GestorActividadDl();
        $cActividadesDl = $GesActividadesDl->getActividades($aWhere, $aOperador);
        // Añado la condición para que no duplique las de midele:
        $aWhere['dl_org'] = $mi_dele;
        $aOperador['dl_org'] = '!=';
        $GesActividadesPub = new GestorActividadPub();
        $cActividadesPub = $GesActividadesPub->getActividades($aWhere, $aOperador);

        $cActividades = array_merge($cActividadesDl, array('-------'), $cActividadesPub);
        break;
    case "n":
        // caso de n
        $id_ctr = (integer)filter_input(INPUT_POST, 'id_ctr_n');
        if ($id_ctr == 1) $id_ctr = ''; //es todos los ctr.
        $id_tabla_persona = 'n';
        $tabla_pau = 'p_numerarios';

        switch ($Qsactividad) {
            case 'ca': //112
                $Qid_tipo_activ = '^' . $sfsv . '12';
                break;
            case 'crt':
                $Qid_tipo_activ = '^' . $sfsv . '11';
                break;
        }
        $aWhere['id_tipo_activ'] = $Qid_tipo_activ;
        $aOperador['id_tipo_activ'] = '~';
        //inicialmente estaba sólo con las activiades publicadas.
        //Ahora añado las no publicadas de midl.
        $GesActividadesDl = new GestorActividadDl();
        $cActividadesDl = $GesActividadesDl->getActividades($aWhere, $aOperador);
        // Añado la condición para que no duplique las de midele:
        $aWhere['dl_org'] = $mi_dele;
        $aOperador['dl_org'] = '!=';
        $GesActividadesPub = new GestorActividadPub();
        $cActividadesPub = $GesActividadesPub->getActividades($aWhere, $aOperador);

        $cActividades = array_merge($cActividadesDl, array('-------'), $cActividadesPub);
        break;
}

$aOpciones = [];
$a_IdActividades = [];
foreach ($cActividades as $oActividad) {
    // para el separador '-------'
    if (is_object($oActividad)) {
        $id_activ = $oActividad->getId_activ();
        $nom_activ = $oActividad->getNom_activ();
        $aOpciones[$id_activ] = $nom_activ;
        $a_IdActividades[] = $id_activ;
    } else {
        $aOpciones[1] = '--------';
    }
}

//Miro los actuales
$gesPlazasPeticion = new GestorPlazaPeticion();
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom' => $Qid_nom, 'tipo' => $Qsactividad, '_ordre' => 'orden'));
$sid_activ = '';
foreach ($cPlazasPeticion as $key => $oPlazaPeticion) {
    $id_activ = $oPlazaPeticion->getId_activ();
    // Borrar los aniguos (no están en la nueva selección de actividades)
    if (!in_array($id_activ, $a_IdActividades)) {
        unset($cPlazasPeticion[$key]);
        $oPlazaPeticion->DBEliminar();
    } else {
        $sid_activ .= empty($sid_activ) ? $id_activ : ',' . $id_activ;
    }
}

$oSelects = new DesplegableArray($sid_activ, $aOpciones, 'actividades');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_actividades(event)');

// En el caso de actualizar la misma página (fnjs_actualizar) solo me quedo con la última.
$stack = $oPosicion->getStack(0);

$oHash = new Hash();
$camposForm = 'actividades!actividades_mas!actividades_num';
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo('que!actividades');
$a_camposHidden = array(
    'id_nom' => $Qid_nom,
    'na' => $Qna,
    'sactividad' => $Qsactividad,
    'que' => '',
    'stack' => $stack
);
$oHash->setArraycamposHidden($a_camposHidden);

$txt_guardar = _("guardar peticiones");


$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oSelects' => $oSelects,
    'ap_nom' => $ap_nom,
    'txt_guardar' => $txt_guardar,
];

$oView = new ViewPhtml('actividadplazas/controller');
$oView->renderizar('peticiones_activ.phtml', $a_campos);