<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
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
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_docs_de_dlb.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_tipo_doc' => $Qid_tipo_doc, 'inventario' => $Qinventario]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];
$aGrupos = $data['aGrupos'];
$nombreDoc = $data['nombreDoc'];

//6
$url_doc_mod = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_asignar_dlb.php?';
//13
$url_imprimir = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_imprimir_dlb.php?';

if (empty($Qinventario)) {
    $a_botones[] = array('txt' => _("Asignar"), 'click' => "fnjs_go(\"$url_doc_mod\")");
} else {
    $nombreDoc = _("Para imprimir inventario");
    $a_botones[] = array('txt' => _('Imprimir'), 'click' => "fnjs_go(\"$url_imprimir\")");
}

$a_cabeceras = array(ucfirst(_("centro - lugar")));
$a_botones[] = array('txt' => _('marcar/desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\")");
$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");

$oTabla = new Lista();
$oTabla->setId_tabla('doc_dlb_tabla');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
$oTabla->setGrupos($aGrupos);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    //'inventario' => $Qinventario,
    'id_tipo_doc' => $Qid_tipo_doc
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nombreDoc' => $nombreDoc,
    'oTabla' => $oTabla,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('doc_de_dlb.phtml', $a_campos);
