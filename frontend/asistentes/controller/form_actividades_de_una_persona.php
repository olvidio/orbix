<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormActividadesDeUnaPersonaRender;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();
list_nav_persist_dossier_return_to_posicion($oPosicion, 1);
list_nav_persist_clean_return_to_posicion($oPosicion, list_nav_build_dossier_return_parametros(), 0);

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/form_actividades_de_una_persona_data', $campos));
$payload = FormActividadesDeUnaPersonaRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
