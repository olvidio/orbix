<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\QueCtrListaRender;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildReturnParametrosFromPost(),
);

$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/que_ctr_lista_data', $campos));
$payload = QueCtrListaRender::enrich($payload);

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setOpciones(NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []));
$oDesplCentros->setOpcion_sel(FuncTablasSupport::payloadString($payload, 'id_ubi_sel'));
$oDesplCentros->setAction('fnjs_otro(1)');

$a_campos = [
    'tituloGros' => FuncTablasSupport::payloadString($payload, 'tituloGros'),
    'action' => FuncTablasSupport::payloadString($payload, 'action'),
    'hash_form_html' => FuncTablasSupport::payloadString($payload, 'hash_form_html'),
    'titulo' => FuncTablasSupport::payloadString($payload, 'titulo'),
    'n' => FuncTablasSupport::payloadString($payload, 'n'),
    'nj' => FuncTablasSupport::payloadString($payload, 'nj'),
    'nm' => FuncTablasSupport::payloadString($payload, 'nm'),
    'a' => FuncTablasSupport::payloadString($payload, 'a'),
    'sssc' => FuncTablasSupport::payloadString($payload, 'sssc'),
    'nax' => FuncTablasSupport::payloadString($payload, 'nax'),
    'c' => FuncTablasSupport::payloadString($payload, 'c'),
    'oDesplCentros' => $oDesplCentros,
    'periodo_form_html' => FuncTablasSupport::payloadString($payload, 'periodo_form_html'),
    'locale_us' => $payload['locale_us'] ?? false,
    'mi_sfsv' => PayloadCoercion::int($payload['mi_sfsv'] ?? 0),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('que_ctr_lista.phtml', $a_campos);
