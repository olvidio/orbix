<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();

$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = actividades_posicion_string($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = actividades_posicion_string($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qusername = (string)filter_input(INPUT_POST, 'username');
$oPosicion->setParametros(array('username' => $Qusername), 1);

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/usuario_lista', ['username' => $Qusername]));
if (!empty($data['error'])) {
   exit($data['error']);
}

$lista = usuarios_lista_from_payload($data);
$a_valores = usuarios_lista_apply_nav($lista['valores'], $Qid_sel, $Qscroll_id);

$oTabla = new Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('username');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden(array('quien' => 'usuario'));

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id');
$oHash1->setArraycamposHidden(array('que' => 'eliminar'));

$aQuery = ['nuevo' => 1, 'quien' => 'usuario'];
$url_nuevo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/usuario_form.php?'
    . http_build_query($aQuery)
);

$url_lista = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/usuario_lista.php'
);
$url_eliminar = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/src/usuarios/usuario_eliminar'
);

$a_campos = [
    'oHash' => $oHash,
    'username' => $Qusername,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'url_lista' => $url_lista,
    'url_nuevo' => $url_nuevo,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_lista.phtml', $a_campos);
