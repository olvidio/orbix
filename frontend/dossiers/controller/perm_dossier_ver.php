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
require_once 'frontend/dossiers/helpers/dossiers_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qid_tipo_dossier = (integer)filter_input(INPUT_POST, 'id_tipo_dossier');
$data = PostRequest::getDataFromUrl('/src/dossiers/perm_dossier_ver_data', [
    'tipo' => $Qtipo,
    'id_tipo_dossier' => $Qid_tipo_dossier,
]);

// Firma de URLs y composición del bloque hash en el frontend.
$goTo = HashFrontSignedLink::tryFromSpec($data['go_to_link_spec'] ?? null);
unset($data['go_to_link_spec']);

$hashConfigRaw = $data['hash_config'] ?? [];
$hashConfig = is_array($hashConfigRaw) ? $hashConfigRaw : [];
unset($data['hash_config']);

$oHash = new HashFront();
$oHash->setCamposForm(tessera_imprimir_string($hashConfig['campos_form'] ?? ''));
$oHash->setCamposNo(tessera_imprimir_string($hashConfig['campos_no'] ?? ''));
$camposHidden = [];
$hiddenRaw = $hashConfig['campos_hidden'] ?? null;
if (is_array($hiddenRaw)) {
    foreach ($hiddenRaw as $k => $v) {
        if (is_string($k)) {
            $camposHidden[$k] = $v;
        }
    }
}
$camposHidden['go_to'] = $goTo;
$oHash->setArrayCamposHidden($camposHidden);

$dossierMap = dossiers_perm_bit_map($data['permiso_dossier_bit_map'] ?? null);

$viewData = dossiers_view_variables($data);
$viewData['go_to'] = $goTo;
$viewData['hash_campos_html'] = $oHash->getCamposHtml();
$viewData['permiso_lectura_html'] = MenuPermisoMenuHtml::cuadrosCheck(
    'permiso_lectura',
    tessera_imprimir_int($data['permiso_lectura'] ?? 0),
    $dossierMap
);
$viewData['permiso_escritura_html'] = MenuPermisoMenuHtml::cuadrosCheck(
    'permiso_escritura',
    tessera_imprimir_int($data['permiso_escritura'] ?? 0),
    $dossierMap
);
unset($viewData['permiso_dossier_bit_map']);

$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('perm_dossier_pres.phtml', $viewData);
