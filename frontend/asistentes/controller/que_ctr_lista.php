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
/** @var \frontend\shared\web\Posicion $oPosicion */
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/que_ctr_lista_data', $campos));
$payload = QueCtrListaRender::enrich($payload);

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setOpciones(NotasFormSupport::desplegableOpciones($payload['opciones_centros'] ?? []));
$oDesplCentros->setOpcion_sel(\frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'id_ubi_sel'));
$oDesplCentros->setAction('fnjs_otro(1)');

$a_campos = [
    'tituloGros' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'tituloGros'),
    'action' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'action'),
    'hash_form_html' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'hash_form_html'),
    'titulo' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'titulo'),
    'n' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'n'),
    'nj' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'nj'),
    'nm' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'nm'),
    'a' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'a'),
    'sssc' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'sssc'),
    'nax' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'nax'),
    'c' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'c'),
    'oDesplCentros' => $oDesplCentros,
    'periodo_form_html' => \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'periodo_form_html'),
    'locale_us' => $payload['locale_us'] ?? false,
    'mi_sfsv' => \frontend\shared\helpers\PayloadCoercion::int($payload['mi_sfsv'] ?? 0),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('que_ctr_lista.phtml', $a_campos);
