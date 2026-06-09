<?php

use frontend\shared\permisos\MenuPermisoMenuHtml;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\FrontBootstrap;

/**
 * Página de visualización de los permisos de los dossiers.
 * Le llegan las variables $tipo y $id_tipo.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qid_tipo_dossier = (integer)filter_input(INPUT_POST, 'id_tipo_dossier');
$data = PostRequest::getDataFromUrl('/src/dossiers/perm_dossier_ver_data', [
    'tipo' => $Qtipo,
    'id_tipo_dossier' => $Qid_tipo_dossier,
]);

// Firma de URLs y composición del bloque hash en el frontend.
$goTo = '';
if (!empty($data['go_to_link_spec']) && is_array($data['go_to_link_spec'])) {
    $goTo = HashFrontSignedLink::fromSpec($data['go_to_link_spec']);
}
unset($data['go_to_link_spec']);
$data['go_to'] = $goTo;

$hashConfig = is_array($data['hash_config'] ?? null) ? $data['hash_config'] : [];
unset($data['hash_config']);
$oHash = new HashFront();
$oHash->setCamposForm((string)($hashConfig['campos_form'] ?? ''));
$oHash->setCamposNo((string)($hashConfig['campos_no'] ?? ''));
$camposHidden = is_array($hashConfig['campos_hidden'] ?? null) ? $hashConfig['campos_hidden'] : [];
$camposHidden['go_to'] = $goTo;
$oHash->setArrayCamposHidden($camposHidden);
$data['hash_campos_html'] = $oHash->getCamposHtml();

$dossierMap = [];
if (isset($data['permiso_dossier_bit_map']) && is_array($data['permiso_dossier_bit_map'])) {
    $dossierMap = $data['permiso_dossier_bit_map'];
}
$data['permiso_lectura_html'] = MenuPermisoMenuHtml::cuadrosCheck(
    'permiso_lectura',
    (int)($data['permiso_lectura'] ?? 0),
    $dossierMap
);
$data['permiso_escritura_html'] = MenuPermisoMenuHtml::cuadrosCheck(
    'permiso_escritura',
    (int)($data['permiso_escritura'] ?? 0),
    $dossierMap
);
unset($data['permiso_dossier_bit_map']);

$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('perm_dossier_pres.phtml', $data);
