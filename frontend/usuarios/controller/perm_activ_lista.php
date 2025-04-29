<?php

use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use procesos\model\entity\GestorActividadFase;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use web\Hash;
use web\Lista;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************


$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qolvidar = (string)filter_input(INPUT_POST, 'olvidar');

if (empty($Qolvidar)) {
    $oPosicion->recordar();
}

$url = Hash::link(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/perm_activ_lista.php'
);

$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url, $hash_params);

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];


$oHash3 = new Hash();
$oHash3->setCamposForm('que!sel');
$oHash3->setcamposNo('sel!refresh!scroll_id');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien
);
$oHash3->setArraycamposHidden($a_camposHidden);
$oHash3->setPrefix('perm'); // prefijo par el id.

$oTabla = new Lista();
$oTabla->setId_tabla('perm_activ_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash3,
    'oTabla' => $oTabla,
];

$oView = new ViewPhtml('../frontend/usuarios/controller');
$oView->renderizar('perm_activ_lista.phtml', $a_campos);
