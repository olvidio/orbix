<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\QueCtrListaRender;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$campos = array_merge($_GET, $_POST);
/** @var array<string, mixed> $payload */
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/que_ctr_lista_data', $campos));
$payload = QueCtrListaRender::enrich($payload);

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setOpciones(notas_desplegable_opciones($payload['opciones_centros'] ?? []));
$oDesplCentros->setOpcion_sel(payload_string($payload, 'id_ubi_sel'));
$oDesplCentros->setAction('fnjs_otro(1)');

$a_campos = [
    'tituloGros' => payload_string($payload, 'tituloGros'),
    'action' => payload_string($payload, 'action'),
    'hash_form_html' => payload_string($payload, 'hash_form_html'),
    'titulo' => payload_string($payload, 'titulo'),
    'n' => payload_string($payload, 'n'),
    'nj' => payload_string($payload, 'nj'),
    'nm' => payload_string($payload, 'nm'),
    'a' => payload_string($payload, 'a'),
    'sssc' => payload_string($payload, 'sssc'),
    'nax' => payload_string($payload, 'nax'),
    'c' => payload_string($payload, 'c'),
    'oDesplCentros' => $oDesplCentros,
    'periodo_form_html' => payload_string($payload, 'periodo_form_html'),
    'locale_us' => $payload['locale_us'] ?? false,
    'mi_sfsv' => tessera_imprimir_int($payload['mi_sfsv'] ?? 0),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('que_ctr_lista.phtml', $a_campos);
