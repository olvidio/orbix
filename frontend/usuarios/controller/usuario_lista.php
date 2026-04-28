<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

// Se usa al buscar:
$Qusername = (string)filter_input(INPUT_POST, 'username');
$oPosicion->setParametros(array('username' => $Qusername), 1);


$url_backend = '/src/usuarios/usuario_lista';
$a_campos_backend = ['username' => $Qusername];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (!empty($data['error'])) {
   exit($data['error']);
}

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$baseUrl = AppUrlConfig::getPublicAppBaseUrl();
foreach ($a_valores as $idx => $fila) {
    if (!is_array($fila)) {
        continue;
    }
    foreach ($fila as $colKey => $cell) {
        if (!is_array($cell) || !isset($cell['link_spec'])) {
            continue;
        }
        $spec = $cell['link_spec'];
        $path = (string)($spec['path'] ?? '');
        $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
        if ($path === '') {
            continue;
        }
        $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);
        $a_valores[$idx][$colKey]['ira'] = HashFront::link($url);
        unset($a_valores[$idx][$colKey]['link_spec']);
    }
}

if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
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
