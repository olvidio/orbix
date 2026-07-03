<?php

use frontend\actividades\helpers\ActividadesPostInput;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;


require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

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
                $Qquien = ActividadesPostInput::posicionString($oPosicion2->getParametro('quien'));
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
$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
}
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionForRecordar(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), \frontend\shared\helpers\ListNavSupport::idSelFromPost(), $Qscroll_id));

$oPosicion->setParametros(array('id_usuario' => $Qid_usuario), 1);

$formData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_form', [
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
]));
$a_camposRaw = $formData['a_campos'] ?? [];
$a_campos_src = UsuariosPayload::formCamposFromPayload(is_array($a_camposRaw) ? $a_camposRaw : []);

$txt_guardar = _("guardar datos usuario");
$txt_eliminar = _("¿Está seguro que desea quitar este permiso?");

$oDesplRoles = new Desplegable('id_role', $a_campos_src['aOpcionesRoles'], $a_campos_src['id_role'], true);
$a_campos['oDesplRoles'] = $oDesplRoles;
$a_campos['oDesplArrayCtrCasas'] = UsuariosPayload::desplegableCasasFromData($a_campos_src['aDataDespl']);

$oHash = new HashFront();
$camposMas = $a_campos_src['camposMas'];
$camposForm = 'que!usuario!nom_usuario!password!email!id_role';
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien,
    'ctx' => $a_campos_src['ctx_guardar'],
);
if ($camposMas !== '') {
    $camposForm .= '!' . $camposMas;
}
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo('password!id_ctr!id_nom!casas!cambio_password!has_2fa');
$oHash->setArraycamposHidden($a_camposHidden);
$a_campos['oHash'] = $oHash;

$a_campos['oPosicion'] = $oPosicion;
$a_campos['txt_guardar'] = $txt_guardar;
$a_campos['txt_eliminar'] = $txt_eliminar;
$a_campos['url_usuario_guardar'] = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/src/usuarios/usuario_guardar'
);

$url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_grupo_lst.php';
$oHash1 = new HashFront();
$oHash1->setUrl($url);
$oHash1->setCamposForm('id_usuario');
$oHash1->setCamposNo('scroll_id');
$a_campos['h_lst'] = $oHash1->linkSinValParams();

$url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/usuarios/controller/usuario_grupo_del_lst.php';
$oHash2 = new HashFront();
$oHash2->setUrl($url);
$oHash2->setCamposForm('id_usuario');
$oHash2->setCamposNo('scroll_id');
$a_campos['h_del_lst'] = $oHash2->linkSinValParams();

$url_usuario_update = AppUrlConfig::getApiBaseUrl() . '/src/usuarios/usuario_check_pwd';
$oHash3 = new HashFront();
$oHash3->setUrl($url_usuario_update);
$oHash3->setCamposForm('id_usuario!usuario!password');
$a_campos['h_pwd'] = $oHash3->linkSinValParams();

$a_campos['id_usuario'] = $a_campos_src['id_usuario'];
$a_campos['usuario'] = $a_campos_src['usuario'];
$a_campos['quien'] = $a_campos_src['quien'];
$a_campos['pau'] = $a_campos_src['pau'];
$a_campos['nom_usuario'] = $a_campos_src['nom_usuario'];
$a_campos['email'] = $a_campos_src['email'];
$a_campos['chk_cambio_password'] = $a_campos_src['chk_cambio_password'];
$a_campos['chk_has_2fa'] = $a_campos_src['chk_has_2fa'];
$a_campos['obj'] = $a_campos_src['obj'];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form.phtml', $a_campos);

if (!empty($Qid_usuario)) {
    $infoData = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_info', ['id_usuario' => $Qid_usuario]));
    $a_campos['grupos_txt'] = \frontend\shared\helpers\PayloadCoercion::string($infoData['grupos_txt'] ?? '');

    $a_campos['procesos_installed'] = AppInstalled::is('procesos');
    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('usuario_grupo.phtml', $a_campos);

    if (AppInstalled::is('procesos')) {
        $url = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl()
            . '/frontend/usuarios/controller/perm_activ_lista.php'
        );

        $oHash = new HashFront();
        $oHash->setUrl($url);
        $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'olvidar' => 1]);
        $hash_params = $oHash->getArrayCampos();

        echo PostRequest::getContent($url, $hash_params);
    }

    if (AppInstalled::is('cambios')) {
        $url_avisos = HashFront::cmdSinParametros(AppUrlConfig::getPublicAppBaseUrl()
            . '/frontend/cambios/controller/usuario_form_avisos.php?'
            . http_build_query(['quien' => 'usuario', 'id_usuario' => $Qid_usuario])
        );

        $oHash = new HashFront();
        $oHash->setUrl($url_avisos);
        $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'quien' => 'usuario']);
        $hash_params = $oHash->getArrayCampos();

        echo PostRequest::getContent($url_avisos, $hash_params);
    }
}
