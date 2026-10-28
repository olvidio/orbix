<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\asistentes\helpers\FormActividadesDeUnaPersonaRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
ListNavSupport::enterDossierChildNav($oPosicion);

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/form_actividades_de_una_persona_data', $campos, false);
if (!empty($data['error'])) {
    // Error de negocio (p. ej. «los datos los modifica el propietario de la plaza»): sin el bloque técnico.
    exit(PostRequest::stripInternalCallProvenance(PayloadCoercion::string($data['error'])));
}
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData($data);
$payload = FormActividadesDeUnaPersonaRender::enrich($payload);

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\view'))
    ->renderizar('form_actividades_de_una_persona.phtml', $a_campos);
