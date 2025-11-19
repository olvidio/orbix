<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;
use web\Lista;
use function core\is_true;
use function core\urlsafe_b64encode;

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
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCdcRepository';
        $obj_ubi = 'ubis\\model\\entity\\Casa';
        break;
    case 'CasaDl':
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCdcDlRepository';
        $obj_ubi = 'ubis\\model\\entity\\CasaDl';
        break;
    case 'CasaEx':
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCdcExRepository';
        $obj_ubi = 'ubis\\model\\entity\\CentroEx';
        break;
    case 'Centro': // tipo dl pero no de la mia
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCtrRepository';
        $obj_ubi = 'ubis\\model\\entity\\Centro';
        break;
    case 'CentroDl':
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCtrDlRepository';
        $obj_ubi = 'ubis\\model\\entity\\CentroDl';
        break;
    case 'CentroEx':
        $repoTeleco = 'src\\ubis\\application\\repositories\\TelecoCtrExRepository';
        $obj_ubi = 'ubis\\model\\entity\\CentroEx';
        break;
}

$repoTeleco = new $repoTeleco();
$Coleccion = $repoTeleco->getTelecos(['id_ubi' => $Qid_ubi]);

$botones = 0;
/*
1: modificar,eliminar,nuevo
*/
if (strstr($Qobj_pau, 'Dl')) {
    $oUbi = new $obj_ubi($Qid_ubi);
    $dl = $oUbi->getDl();
    if ($dl == ConfigGlobal::mi_delef()) {
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
    $a_botones = [];
}

$a_cabeceras = [];
$a_valores = [];
$c = 0;
foreach ($Coleccion as $oFila) {
    $v = 0;
    $pks1 = 'get' . ucfirst($oFila->getPrimary_key());
    $val_pks = $oFila->$pks1();
    $pks = urlsafe_b64encode(json_encode($val_pks, JSON_THROW_ON_ERROR));
    $a_valores[$c]['sel'] = $pks;
    foreach ($oFila->getDatosCampos() as $oDatosCampo) {
        if ($c == 0) {
            $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta());
        }
        $v++;
        $metodo = $oDatosCampo->getMetodoGet();
        // si el metodo obtiene un valueobject
        if (substr($metodo, -2) === 'Vo') {
            $valor_camp = $oFila->$metodo()->value();
        } else {
            $valor_camp = $oFila->$metodo();
        }
        if (!$valor_camp) {
            $a_valores[$c][$v] = '';
            continue;
        }
        $var_1 = $oDatosCampo->getArgument();
        $var_2 = $oDatosCampo->getArgument2();
        switch ($oDatosCampo->getTipo()) {
            case "fecha":
                $a_valores[$c][$v] = $valor_camp->getFromLocal();
                break;
            case "array":
                $lista = $oDatosCampo->getLista();
                $a_valores[$c][$v] = $lista[$valor_camp];
                break;
            case 'depende':
            case 'opciones':
                $RepoRelacionado = new $var_1();
                $oRelacionado = $RepoRelacionado->findById($valor_camp);
                if ($oRelacionado !== null) {
                    $var = $oRelacionado->$var_2();
                    if (empty($var)) {
                        $var = $valor_camp;
                    }
                } else {
                    $var = '?';
                }
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
$oTabla = new Lista();
$oTabla->setId_tabla('telecos_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
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

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('teleco_tabla.phtml', $a_campos);
