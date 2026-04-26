<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

/**
 * Página de visualización de los permisos de los dossiers.
 * Le llegan las variables $tipo y $id_tipo.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qid_tipo_dossier = (integer)filter_input(INPUT_POST, 'id_tipo_dossier');
$data = PostRequest::getDataFromUrl('/src/dossiers/perm_dossier_ver_data', [
    'tipo' => $Qtipo,
    'id_tipo_dossier' => $Qid_tipo_dossier,
]);
$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('perm_dossier_pres.phtml', $data);
