<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\dbextern\helpers\DbexternPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$mov = '';
$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$id = (string)filter_input(INPUT_POST, 'id');
$mov = (string)filter_input(INPUT_POST, 'mov');

$cont_sync = 0;
$first_load = $id === '';

if ($first_load) {
    $id = '1';
    $data = PostRequest::getDataFromUrl('/src/dbextern/ver_listas_datos', [
        'region' => $region,
        'dl' => $dl,
        'tipo_persona' => $tipo_persona,
        'first_load' => '1',
    ]);
    $a_lista = DbexternPayload::listaFromBackend($data['lista'] ?? []);
    $cont_sync = PayloadCoercion::int($data['cont_sync'] ?? 0);

    session_start();
    $_SESSION['DBListas'] = $a_lista;
    session_write_close();
}

$listas = DbexternPayload::sessionDbListas();
$max = count($listas);
$a_lista_orbix = [];
$persona_listas = [];
$a_lista_orbix_otradl = [];
$new_id = 0;
$id_nom_bdu = '';

if ($max > 0) {
    $idInt = is_numeric($id) ? (int) $id : 1;
    $new_id = DbexternPayload::otroListas($idInt, $mov, $max);
}

if ($new_id > 0 && isset($listas[$new_id])) {
    $persona_listas = $listas[$new_id];
    $id_nom_bdu = DbexternPayload::personaListasRow($persona_listas)['id_nom_listas'];

    $matches = PostRequest::getDataFromUrl('/src/dbextern/ver_listas_datos', [
        'region' => $region,
        'dl' => $dl,
        'tipo_persona' => $tipo_persona,
        'id_nom_bdu' => $id_nom_bdu,
    ]);
    $a_lista_orbix = DbexternPayload::listaFromBackend($matches['posibles_misma_dl'] ?? []);
    $a_lista_orbix_otradl = DbexternPayload::listaFromBackend($matches['posibles_otra_dl'] ?? []);
}

$url_sincro_ver = AppUrlConfig::getApiBaseUrl() . '/frontend/dbextern/controller/ver_listas.php';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_ver);
$oHash->setcamposNo('mov');
$a_camposHidden = [
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'id' => $new_id,
];
$oHash->setArraycamposHidden($a_camposHidden);

$url_sincro_crear = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_crear';
$oHash1 = new HashFront();
$oHash1->setUrl($url_sincro_crear);
$oHash1->setCamposForm('id_nom_listas!id_orbix!region!dl!id!tipo_persona');
$h_crear = $oHash1->linkSinValParams();

$url_sincro_unir = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_unir';
$oHash2 = new HashFront();
$oHash2->setUrl($url_sincro_unir);
$oHash2->setCamposForm('id_nom_listas!id_orbix!region!dl!id!tipo_persona');
$h_unir = $oHash2->linkSinValParams();

$url_sincro_crear_todos = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_crear_todos';
$oHash3 = new HashFront();
$oHash3->setUrl($url_sincro_crear_todos);
$oHash3->setCamposForm('region!dl!tipo_persona');
$h_crear_todos = $oHash3->linkSinValParams();

$html_reg = sprintf(_("registro %s de %s"), $new_id, $max);

$a_campos = [
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'id_nom_bdu' => $id_nom_bdu,
    'new_id' => $new_id,
    'max' => $max,
    'mov' => $mov,
    'cont_sync' => $cont_sync,
    'first_load' => $first_load,
    'persona_listas' => $persona_listas,
    'a_lista_orbix' => $a_lista_orbix,
    'a_lista_orbix_otradl' => $a_lista_orbix_otradl,
    'html_reg' => $html_reg,
    'oHash' => $oHash,
    'url_sincro_ver' => $url_sincro_ver,
    'url_sincro_crear' => $url_sincro_crear,
    'url_sincro_unir' => $url_sincro_unir,
    'url_sincro_crear_todos' => $url_sincro_crear_todos,
    'h_crear' => $h_crear,
    'h_unir' => $h_unir,
    'h_crear_todos' => $h_crear_todos,
];

$oView = new ViewNewPhtml('frontend\dbextern\controller');
$oView->renderizar('ver_listas.phtml', $a_campos);
