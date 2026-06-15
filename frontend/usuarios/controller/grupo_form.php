<?php

use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $a_sel = $oPosicion2->getParametro('id_sel');
            if (!empty($a_sel)) {
                $Qid_usuario = usuarios_id_from_sel_item(usuarios_sel_first_item($a_sel));
            } else {
                $Qid_usuario = actividades_posicion_int($oPosicion2->getParametro('id_usuario'));
            }
            $Qscroll_id = actividades_posicion_int($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') {
        $Qid_usuario = usuarios_id_from_sel_item(usuarios_sel_first_item($a_sel));
    }
}
list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), list_nav_id_sel_from_post(), isset($Qscroll_id) ? (string) $Qscroll_id : ''));

$oPosicion->setParametros(array('id_usuario' => $Qid_usuario), 1);

if (!empty($Qid_usuario)) {
    $infoData = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/grupo_info', ['id_usuario' => $Qid_usuario]));
    $usuario = tessera_imprimir_string($infoData['nombre'] ?? '');

    $oHashG = new HashFront();
    $oHashG->setCamposForm('que!usuario');
    $oHashG->setcamposNo('id_ctr!id_sacd!casas!refresh');
    $oHashG->setArraycamposHidden(['id_usuario' => $Qid_usuario]);

    $txt_guardar = _("guardar datos grupo");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'usuario' => $usuario,
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('grupo_form.phtml', $a_camposG);

    $permData = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/perm_menu_lista', ['id_usuario' => $Qid_usuario]));
    $lista = usuarios_lista_from_payload($permData);

    $oTablaPermMenu = new Lista();
    $oTablaPermMenu->setId_tabla('form_perm_menu');
    $oTablaPermMenu->setCabeceras($lista['cabeceras']);
    $oTablaPermMenu->setBotones($lista['botones']);
    $oTablaPermMenu->setDatos($lista['valores']);

    $oHashPermisos = new HashFront();
    $oHashPermisos->setCamposForm('que!sel');
    $oHashPermisos->setcamposNo('scroll_id!refresh');
    $oHashPermisos->setArraycamposHidden(['id_usuario' => $Qid_usuario]);

    $a_camposP = [
        'oHashPermisos' => $oHashPermisos,
        'oTablaPermMenu' => $oTablaPermMenu,
    ];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('perm_menu_lista.phtml', $a_camposP);

    if (AppInstalled::is('procesos')) {
        $url = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl()
            . '/frontend/usuarios/controller/perm_activ_lista.php'
        );

        $oHash = new HashFront();
        $oHash->setUrl($url);
        $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario]);
        $hash_params = $oHash->getArrayCampos();

        echo PostRequest::getContent($url, $hash_params);
    }
} else {
    $oHashG = new HashFront();
    $oHashG->setCamposForm('que!usuario');
    $oHashG->setcamposNo('id_ctr!id_sacd!casas!refresh');
    $oHashG->setArraycamposHidden(['id_usuario' => '']);

    $txt_guardar = _("guardar datos grupo");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'usuario' => '',
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('grupo_form.phtml', $a_camposG);
}
