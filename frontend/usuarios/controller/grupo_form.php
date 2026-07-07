<?php

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

$restored = ListNavSupport::restoreSelectionFromStackPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!ListNavSupport::idSelIsEmpty($restored['id_sel'])) {
    $a_sel = is_array($restored['id_sel']) ? $restored['id_sel'] : [$restored['id_sel']];
}
if (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') {
        $Qid_usuario = UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
    }
}

$navIdentity = $Qid_usuario > 0 ? ['id_usuario' => $Qid_usuario] : [];
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge(
        array_filter([
            'username' => (string)filter_input(INPUT_POST, 'username'),
            'quien' => 'grupo',
        ], static fn ($v) => $v !== ''),
        ListNavSupport::buildSelectionStatePatchFromPost(),
    ),
);

if (!empty($Qid_usuario)) {
    $infoData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/grupo_info', ['id_usuario' => $Qid_usuario]));
    $usuario = \frontend\shared\helpers\PayloadCoercion::string($infoData['nombre'] ?? '');

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
