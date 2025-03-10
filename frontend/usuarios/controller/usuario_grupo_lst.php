<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_grupo_lst.php'
);

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];


$oTabla = new Lista();
$oTabla->setId_tabla('usuario_grupo_lst');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();