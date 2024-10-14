<?php

use ubis\model\entity as ubis;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/***************  datos  **********************************/

// dossier="1001";

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->setBloque('#ficha');
$oPosicion->recordar();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

switch ($Qobj_pau) {
    case 'Casa': // tipo dl pero no de la mia
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCdc';
        $obj_ubi = 'ubis\\model\\entity\\Casa';
        break;
    case 'CasaDl':
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCdcDl';
        $obj_ubi = 'ubis\\model\\entity\\CasaDl';
        break;
    case 'CasaEx':
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCdcEx';
        $obj_ubi = 'ubis\\model\\entity\\CentroEx';
        break;
    case 'Centro': // tipo dl pero no de la mia
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCtr';
        $obj_ubi = 'ubis\\model\\entity\\Centro';
        break;
    case 'CentroDl':
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCtrDl';
        $obj_ubi = 'ubis\\model\\entity\\CentroDl';
        break;
    case 'CentroEx':
        $obj_ges_tel = 'ubis\\model\\entity\\GestorTelecoCtrEx';
        $obj_ubi = 'ubis\\model\\entity\\CentroEx';
        break;
}

$oLista = new $obj_ges_tel();
$Coleccion = $oLista->getTelecos(array('id_ubi' => $Qid_ubi));

$botones = 0;
/*
1: modificar,eliminar,nuevo
*/
if (strstr($Qobj_pau, 'Dl')) {
    $oUbi = new $obj_ubi($Qid_ubi);
    $dl = $oUbi->getDl();
    if ($dl == core\ConfigGlobal::mi_delef()) {
        // ----- sv sólo a scl -----------------
        if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
            $botones = "1";
        }
    }
} else if (strstr($Qobj_pau, 'Ex')) {
    // ----- sv sólo a scl -----------------
    if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
        $botones = "1";
    }
}

$tit_txt = _("telecomunicaciones de un centro o casa");
$ficha = "ficha";

if ($botones == 1) {
    $a_botones = array(array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"),
        array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")")
    );
} else {
    $a_botones = array();
}

$a_cabeceras = array();
$a_valores = array();
$c = 0;
foreach ($Coleccion as $oFila) {
    $v = 0;
    $pks = core\urlsafe_b64encode(serialize($oFila->getPrimary_key()));
    //$pks=str_replace('"','\"',$pks);
    //echo "sel: $pks<br>";
    $a_valores[$c]['sel'] = $pks;
    foreach ($oFila->getDatosCampos() as $oDatosCampo) {
        if ($c == 0) $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta());
        $v++;
        $nom_camp = $oDatosCampo->getNom_camp();
        $valor_camp = $oFila->$nom_camp;
        $var_1 = $oDatosCampo->getArgument();
        $var_2 = $oDatosCampo->getArgument2();
        switch ($oDatosCampo->getTipo()) {
            case "array":
                $a_valores[$c][$v] = $var_1[$valor_camp];
                break;
            case 'depende':
            case 'opciones':
                $oRelacionado = new $var_1($valor_camp);
                $var = $oRelacionado->$var_2();
                if (empty($var)) $var = $valor_camp;
                $a_valores[$c][$v] = $var;
                break;
            case "check":
                if (is_true($valor_camp)) {
                    $a_valores[$c][$v] = _("sí");
                } else {
                    $a_valores[$c][$v] = _("no");
                }
                break;
            default:
                $a_valores[$c][$v] = $valor_camp;
        }
    }
    $c++;
}
$oTabla = new web\Lista();
$oTabla->setId_tabla('telecos_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new web\Hash();
$oHash->setCamposForm('mod!sel');
$oHash->setcamposNo('mod!sel!scroll_id!refresh');
$a_camposHidden = array(
    'id_ubi' => $Qid_ubi,
    'obj_pau' => $Qobj_pau,
);
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = ['botones' => $botones,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'ficha' => $ficha,
    'tit_txt' => $tit_txt,
    'oTabla' => $oTabla,
];

$oView = new core\View('ubis/controller');
$oView->renderizar('teleco_tabla.phtml', $a_campos);
