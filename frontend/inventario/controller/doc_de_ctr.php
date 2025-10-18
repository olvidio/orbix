<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');

$oPosicion->recordar();
$aGoBack = array(
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc);
$oPosicion->setParametros($aGoBack, 1);

// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/infrastructure/controllers/lista_de_ctr_con_docs.php';
$a_campos_backend = [ 'id_tipo_doc' => $Qid_tipo_doc, 'inventario' => $Qinventario];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    echo $data['error'];
}

$a_valores = $data['a_valores'];
$nombreDoc = $data['nombreDoc'];

//3
$url_doc_mod = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_asignar_ctr.php?';
//12
$url_imprimir = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_imprimir_ctr.php?';

if (empty($Qinventario)) {
    $a_botones[] = array('txt' => _("Asignar"), 'click' => "fnjs_go(\"$url_doc_mod\")");
} else {
    $a_botones[] = array('txt' => _('Imprimir'), 'click' => "fnjs_go(\"$url_imprimir\")");
}

$a_cabeceras = array(ucfirst(_("centro")));
$a_botones[] = array('txt' => _('marcar/desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\")");
$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");

$oTabla = new Lista();
$oTabla->setId_tabla('doc_ctr_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_de_ctr.phtml', $a_campos);
