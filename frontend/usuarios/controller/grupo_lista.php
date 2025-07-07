<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

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


$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/grupo_lista.php'
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
$oTabla->setId_tabla('grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHashBuscar = new Hash();
$oHashBuscar->setCamposForm('username');
$oHashBuscar->setcamposNo('scroll_id');
$oHashBuscar->setArraycamposHidden(array('quien' => 'grupo'));

$oHashSelect = new Hash();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('que' => 'eliminar_grupo'));

$aQuery = ['nuevo' => 1, 'quien' => 'grupo'];
$url_nuevo = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/frontend/usuarios/controller/grupo_form.php?'
    . http_build_query($aQuery));

$a_campos = [
    'oHashBuscar' => $oHashBuscar,
    'username' => $Qusername,
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('grupo_lista.phtml', $a_campos);
