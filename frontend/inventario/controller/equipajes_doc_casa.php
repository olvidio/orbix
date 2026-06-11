<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

if ($Qid_equipaje === 0) {
    ajax_json_html('', _('debe seleccionar un equipaje'));
}

$url_backend = '/src/inventario/equipajes_doc_casa';
$a_campos_backend = [
    'id_equipaje' => $Qid_equipaje,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$docCasa = inventario_equipajes_doc_casa_from_payload(inventario_post_payload($data));
$a_valores = $docCasa['a_valores'];
$nombre_ubi = $docCasa['nombre_ubi'];

$a_cabeceras = [
    ['id' => 'nombre', 'name' => ucfirst(_('nombre'))],
    ['id' => 'identificador', 'name' => ucfirst(_('identificador'))],
    ['id' => 'lugar', 'name' => ucfirst(_('lugar'))],
];

$a_botones = [['txt' => _('seleccionar'), 'click' => 'fnjs_ver_equipaje()']];

$oListaDocsCasa = new Lista();
$oListaDocsCasa->setId_tabla('doc_activ_tabla');
$oListaDocsCasa->setCabeceras($a_cabeceras);
$oListaDocsCasa->setDatos($a_valores);
$oListaDocsCasa->setBotones($a_botones);

$url_backend = '/src/inventario/equipajes_egm';
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$a_egm = inventario_egm_rows(inventario_post_payload($data)['a_egm'] ?? []);

$oHash = new HashFront();
$oHash->setCamposNo('id_grupo');
$id_grupo = 33;
$oHash->setArrayCamposHidden(['id_grupo' => $id_grupo, 'id_equipaje' => $Qid_equipaje]);

$a_campos = [
    'oHash' => $oHash,
    'nombre_ubi' => $nombre_ubi,
    'oListaDocsCasa' => $oListaDocsCasa,
];

ob_start();
$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_doc_casa.phtml', $a_campos);
echo "<div id='grupos'>";

$a_cabeceras = [
    ['id' => 'nombre', 'name' => ucfirst(_('sigla'))],
    ['id' => 'identificador', 'name' => ucfirst(_('identificador'))],
];
foreach ($a_egm as $aEgm) {
    $id_grupo = $aEgm['id_grupo'];
    $id_lugar = $aEgm['id_lugar'];
    $nom_lugar = $aEgm['nom_lugar'];
    $id_item_egm = $aEgm['id_item_egm'];
    $a_valores_grupo = $aEgm['a_valores'];

    $oLista = new Lista();
    $oLista->setId_tabla('docs_' . $id_grupo);
    $oLista->setCabeceras($a_cabeceras);
    $oLista->setDatos($a_valores_grupo);

    $oHashGrupo = new HashFront();
    $oHashGrupo->setArrayCamposHidden([
        'id_grupo' => $id_grupo,
        'id_equipaje' => $Qid_equipaje,
        'id_item_egm' => $id_item_egm,
    ]);

    $a_campos = [
        'id_grupo' => $id_grupo,
        'id_lugar' => $id_lugar,
        'nom_lugar' => $nom_lugar,
        'oLista' => $oLista,
        'oHashGrupo' => $oHashGrupo,
    ];

    $oView = new ViewNewPhtml('frontend\inventario\controller');
    $oView->renderizar('equipajes_doc_maleta.phtml', $a_campos);
}
echo '</div>';
ajax_json_html((string) ob_get_clean());
