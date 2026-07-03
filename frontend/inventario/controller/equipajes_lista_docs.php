<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_lugar = (int)filter_input(INPUT_POST, 'id_lugar');
$Qid_item_egm = (int)filter_input(INPUT_POST, 'id_item_egm');

$url_backend = '';
if ($Qid_lugar !== 0) {
    $url_backend = '/src/inventario/lista_docs_de_lugar';
} elseif ($Qid_item_egm !== 0) {
    $url_backend = '/src/inventario/lista_docs_de_egm';
}
$a_campos_backend = [
    'id_lugar' => $Qid_lugar,
    'id_item_egm' => $Qid_item_egm,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$view = InventarioPayload::listaDocsGrupoFromPayload($payload);
$a_valores = $view['a_valores'];
$nombre_valija = $view['nombre_valija'];

$a_cabeceras = [];
$a_cabeceras[] = ucfirst(_('sigla'));
$a_cabeceras[] = ucfirst(_('identificador'));

$oLista = new Lista();
$oLista->setId_tabla('docs_' . $Qid_grupo);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);

$oHashGrupo = new HashFront();
$oHashGrupo->setArrayCamposHidden([
    'id_grupo' => $Qid_grupo,
    'id_equipaje' => $Qid_equipaje,
    'id_item_egm' => $Qid_item_egm,
]);

$a_campos = [
    'id_grupo' => $Qid_grupo,
    'id_lugar' => $Qid_lugar,
    'nom_lugar' => $nombre_valija,
    'oLista' => $oLista,
    'oHashGrupo' => $oHashGrupo,
];

AjaxJsonSupport::renderPhtml('frontend\inventario\controller', 'equipajes_doc_maleta.phtml', $a_campos);
