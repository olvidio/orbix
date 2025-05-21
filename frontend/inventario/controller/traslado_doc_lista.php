<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

$oPosicion->recordar();


// muestra los ctr que tienen el documento.
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_docs_de_ctr.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_ubi' => $Qid_ubi]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];

$a_cabeceras[] = ucfirst(_("documento"));
$a_cabeceras[] = ucfirst(_("observaciones"));

$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");


$oLista = new Lista();
$oLista->setId_tabla('doc_ajax');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);

echo $oLista->mostrar_tabla_html();



