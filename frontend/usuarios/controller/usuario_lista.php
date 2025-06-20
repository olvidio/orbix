<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
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


$url_lista = Hash::link(ConfigGlobal::getWeb()
    . '/frontend/usuarios/controller/usuario_lista.php'
);

$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_lista.php'
);

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['username' => $Qusername]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

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

$oHash = new Hash();
$oHash->setCamposForm('username');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden(array('quien' => 'usuario'));

$oHash1 = new Hash();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id');
$oHash1->setArraycamposHidden(array('que' => 'eliminar'));

$aQuery = ['nuevo' => 1, 'quien' => 'usuario'];
$url_nuevo = Hash::link(ConfigGlobal::getWeb()
    . '/frontend/usuarios/controller/usuario_form.php?'
    . http_build_query($aQuery)
);

$url_eliminar = Hash::link(ConfigGlobal::getWeb()
    . '/frontend/usuarios/controller/usuario_eliminar.php'
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
