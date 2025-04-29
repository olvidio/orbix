<?php

use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorPermUsuarioActividad;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use web\ContestarJson;
use web\TiposActividades;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oCuadrosAfecta = new PermAfectados();

$Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');

$oGesPerm = new GestorPermUsuarioActividad();
$aWhere = ['id_usuario' => $Qid_usuario, '_ordre' => 'dl_propia DESC, id_tipo_activ_txt, afecta_a'];
$aOperador = [];
$cUsuarioPerm = $oGesPerm->getPermUsuarioActividades($aWhere, $aOperador);
// No se pueden mandar objetos de php por json. Deben se arrays
$cUsuarioPermArray = [];
foreach ($cUsuarioPerm as $oUsuarioPerm) {
    $a_usuario_perm['id_item'] = $oUsuarioPerm->getId_item();
    $a_usuario_perm['id_tipo_activ_txt'] = $oUsuarioPerm->getId_tipo_activ_txt();
    $a_usuario_perm['dl_propia'] = $oUsuarioPerm->getDl_propia();
    $a_usuario_perm['fase_ref'] = $oUsuarioPerm->getFase_ref();
    $a_usuario_perm['afecta_a'] = $oUsuarioPerm->getAfecta_a();
    $a_usuario_perm['perm_on'] = $oUsuarioPerm->getPerm_on();
    $a_usuario_perm['perm_off'] = $oUsuarioPerm->getPerm_off();

    $cUsuarioPermArray[] = $a_usuario_perm;
}


// propios
$i = 0;
/**
 * Para la tabla slickGrid, el width debe ser en pixels
 * No hay que poner unidades, pues da un error de javascript.
 * Al final obligo a que la tabla sea html.
 *
 * @var array $a_cabeceras
 */
$a_cabeceras = [['name' => _("dl propia"), 'width' => '5%'],
    ['name' => _("tipo de actividad"), 'width' => '10%'],
    ['name' => _("afecta a"), 'width' => '20%'],
    ['name' => _("fase de referencia"), 'width' => '30%'],
    ['name' => _("permiso off"), 'width' => '20%'],
    ['name' => _("permiso on"), 'width' => '20%'],
];

$a_botones = [
    ['prefix' => 'perm', 'txt' => _("modificar"), 'click' => "fnjs_mod_perm_activ(\"#permisos_activ\")"],
    ['prefix' => 'perm', 'txt' => _("eliminar"), 'click' => "fnjs_del_perm_activ(\"#permisos_activ\")"],
];

$oAcciones = new PermAccion();
$aOpcionesAction = $oAcciones->lista_array();
$a_valores = [];
$id_tipo_activ_anterior = '';
$dl_propia_anterior = '';
foreach ($cUsuarioPermArray as $aUsuarioPerm) {
    $i++;
    $id_item = $aUsuarioPerm['id_item'];
    $id_tipo_activ_txt = $aUsuarioPerm['id_tipo_activ_txt'];
    $dl_propia = $aUsuarioPerm['dl_propia'];
    $fase_ref = $aUsuarioPerm['fase_ref'];
    $afecta_a = $aUsuarioPerm['afecta_a'];
    $perm_on = $aUsuarioPerm['perm_on'];
    $perm_off = $aUsuarioPerm['perm_off'];

    if (is_true($dl_propia)) {
        if (str_starts_with($id_tipo_activ_txt, '1')) { //sv
            $dl_propia_txt = ConfigGlobal::mi_dele();
        } else { //sf
            if (str_starts_with($id_tipo_activ_txt, '.')) { //sf y sv
                $dl_propia_txt = ConfigGlobal::mi_dele() . ' - ' . ConfigGlobal::mi_dele() . 'f';
            } else {
                $dl_propia_txt = ConfigGlobal::mi_dele() . 'f';
            }
        }
    } else {
        $dl_propia_txt = _("otras");
    }

    $oTipoActividad = new TiposActividades($id_tipo_activ_txt);
    $id_tipo_activ = $oTipoActividad->getId_tipo_activ();

    $GesTiposActiv = new GestorTipoDeActividad();
    $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ, $dl_propia);

    $oGesFases = new GestorActividadFase();
    $aFases = $oGesFases->getArrayFasesProcesos($aTiposDeProcesos);
    $fase_ref_txt = array_search($fase_ref, $aFases);

    if ($dl_propia == $dl_propia_anterior && $id_tipo_activ == $id_tipo_activ_anterior) {
        $a_valores[$i]['sel'] = "";
        $a_valores[$i][1] = '';
        $a_valores[$i][2] = '';
    } else {
        $a_valores[$i]['sel'] = "$Qid_usuario#$id_item#$id_tipo_activ_txt#$dl_propia";
        $a_valores[$i][1] = $dl_propia_txt;
        $a_valores[$i][2] = $oTipoActividad->getNom();
    }
    $a_valores[$i][3] = $oCuadrosAfecta->lista_tiene_txt($afecta_a);

    $a_valores[$i][4] = $fase_ref_txt;
    $a_valores[$i][5] = empty($aOpcionesAction[$perm_off]) ? '?' : $aOpcionesAction[$perm_off];
    $a_valores[$i][6] = empty($aOpcionesAction[$perm_on]) ? '?' : $aOpcionesAction[$perm_on];

    $id_tipo_activ_anterior = $id_tipo_activ;
    $dl_propia_anterior = $dl_propia;
}

$data = [
    'a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
];


$error_txt = '';

ContestarJson::enviar($error_txt, $data);