<?php

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

$first_load = $id === '';

if ($first_load) {
    $id = '1';
    $data = PostRequest::getDataFromUrl('/src/dbextern/ver_orbix_datos', [
        'region' => $region,
        'dl' => $dl,
        'tipo_persona' => $tipo_persona,
    ]);
    $a_lista = DbexternPayload::listaFromBackend($data['lista'] ?? []);

    session_start();
    $_SESSION['DBOrbix'] = $a_lista;
    session_write_close();
}

$orbix = DbexternPayload::sessionDbOrbix();
$max = count($orbix);
$a_lista_bdu = [];
$persona_orbix = [];
$new_id = 0;

if ($max === 0) {
    $html_reg = _("No hay registros");
} else {
    $idInt = is_numeric($id) ? (int) $id : 1;
    $newIdRaw = DbexternPayload::otroOrbix($idInt, $mov, $max);
    $new_id = $newIdRaw === false ? 0 : $newIdRaw;
    if ($new_id > 0 && isset($orbix[$new_id])) {
        $persona_orbix = $orbix[$new_id];
        $id_nom_orbix = DbexternPayload::personaOrbixRow($persona_orbix)['id_nom_orbix'];

        $matches = PostRequest::getDataFromUrl('/src/dbextern/ver_orbix_datos', [
            'region' => $region,
            'dl' => $dl,
            'tipo_persona' => $tipo_persona,
            'id_nom_orbix' => $id_nom_orbix,
        ]);
        $a_lista_bdu = DbexternPayload::listaBduFromMatches($matches);
    }

    $html_reg = sprintf(_("registro %s de %s"), $new_id, $max);
}

$url_sincro_ver = AppUrlConfig::getApiBaseUrl() . '/frontend/dbextern/controller/ver_orbix.php';
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

$url_sincro_unir = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_unir';
$oHash1 = new HashFront();
$oHash1->setUrl($url_sincro_unir);
$oHash1->setCamposForm('region!dl!id_nom_listas!id!id_orbix!tipo_persona');
$h1 = $oHash1->linkSinValParams();

$a_campos = [
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'new_id' => $new_id,
    'max' => $max,
    'persona_orbix' => $persona_orbix,
    'a_lista_bdu' => $a_lista_bdu,
    'html_reg' => $html_reg,
    'oHash' => $oHash,
    'url_sincro_ver' => $url_sincro_ver,
    'url_sincro_unir' => $url_sincro_unir,
    'h1' => $h1,
];

$oView = new ViewNewPhtml('frontend\dbextern\controller');
$oView->renderizar('ver_orbix.phtml', $a_campos);
