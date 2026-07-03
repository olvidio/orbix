<?php

use frontend\actividades\helpers\ActividadesPostInput;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;


require_once 'frontend/shared/FrontBootstrap.php';
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
                $Qid_usuario = UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
            } else {
                $Qid_usuario = ActividadesPostInput::posicionInt($oPosicion2->getParametro('id_usuario'));
            }
            $Qscroll_id = ActividadesPostInput::posicionInt($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') {
        $Qid_usuario = UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
    }
}
ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionForRecordar(ListNavSupport::buildReturnParametrosFromPost(), ListNavSupport::idSelFromPost(), $Qscroll_id));

$oPosicion->setParametros(array('id_usuario' => $Qid_usuario), 1);

if (!empty($Qid_usuario)) {
    $infoData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/grupo_info', ['id_usuario' => $Qid_usuario]));
    $usuario = PayloadCoercion::string($infoData['nombre'] ?? '');

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

    $permData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/perm_menu_lista', ['id_usuario' => $Qid_usuario]));
    $lista = UsuariosPayload::listaFromPayload($permData);

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
