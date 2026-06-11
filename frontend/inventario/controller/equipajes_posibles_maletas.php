<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');

$url_backend = '/src/inventario/lista_equipajes_posibles_maletas';
$a_campos_backend = ['id_equipaje' => $Qid_equipaje];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$view = inventario_posibles_maletas_from_payload(inventario_post_payload($data));

$a_opciones = $view['a_opciones'];
$new_id_grupo = $view['new_id_grupo'];

$nom_grupo = 'sel_grupo_' . $new_id_grupo;
$nom_form = 'form_ver_' . $new_id_grupo;

$oDespl = new Desplegable();
$oDespl->setOpciones($a_opciones);
$oDespl->setNombre($nom_grupo);
$oDespl->setBlanco(true);
$oDespl->setAction("fnjs_ver_docs('$new_id_grupo')");

$oHash = new HashFront();
$oHash->setCamposForm($nom_grupo);
$oHash->setArrayCamposHidden([
    'id_grupo' => $new_id_grupo,
    'id_equipaje' => $Qid_equipaje,
    'nom_grupo' => $nom_grupo,
]);

ob_start();
echo "<span id='grupo_$new_id_grupo'>";
echo "<form id='$nom_form'>";
echo $oHash->getCamposHtml();
echo '<br>';
echo _('valija') . $new_id_grupo;
echo $oDespl->desplegable();
echo '</form>';
echo "<span id='docs_grupo_$new_id_grupo'>";
echo '</span>';
echo '</span>';
ajax_json_html((string) ob_get_clean());
