<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_item_egm = (int)filter_input(INPUT_POST, 'id_item_egm');

// posibles tipos de documento
$url_backend = '/src/inventario/infrastructure/controllers/lista_docs_de_egm.php';
$a_campos_backend = [ 'id_item_egm' => $Qid_item_egm];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$a_valores = $data['a_valores'];


$a_cabeceras = [ucfirst(_("sigla")),
    ucfirst(_("identificador")),
];

$a_botones = [['txt' => _("Quitar"), 'click' => "fnjs_del_doc(\"#frm_del\",\"$Qid_grupo\");"],
    ['txt' => _("Cancel"), 'click' => "fnjs_cerrar()"],
];

$oLista = new Lista();
$oLista->setId_tabla('docs_' . $Qid_grupo);
$oLista->setCabeceras($a_cabeceras);
$oLista->setBotones($a_botones);
$oLista->setDatos($a_valores);


$oHashForm = new Hash();
$oHashForm->setCamposForm('sel');
$oHashForm->setArrayCamposHidden([
    'id_grupo' => $Qid_grupo,
    'id_equipaje' => $Qid_equipaje,
    'id_item_egm' => $Qid_item_egm,
]);

$a_campos = [
    'oHashForm' => $oHashForm,
    'oLista' => $oLista,
    'Qid_grupo' => $Qid_grupo,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_form_del.phtml', $a_campos);