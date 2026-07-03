<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;
use frontend\dossiers\helpers\DossiersListaSupport;
use frontend\dossiers\helpers\DossiersPayload;

/**
 * Página de selección de los dossiers cuyos permisos deseo visualizar
 * o modificar. Hay que pasarle la variable $tipo, para que sólo aparezca
 * la lista de selección de los dossiers que interesen.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$data = PostRequest::getDataFromUrl('/src/dossiers/perm_dossiers_data', [
    'tipo' => $Qtipo,
]);
$viewData = DossiersPayload::viewVariables($data);
$viewData['a_filas'] = DossiersListaSupport::signFilas($data['a_filas'] ?? [], ['pagina']);
$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('perm_dossiers.phtml', $viewData);
