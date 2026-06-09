<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_item_egm = (int)filter_input(INPUT_POST, 'id_item_egm');

// posibles tipos de documento
$url_backend = '/src/inventario/lista_docs_de_egm';
$a_campos_backend = [ 'id_item_egm' => $Qid_item_egm];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);


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


$oHashForm = new HashFront();
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