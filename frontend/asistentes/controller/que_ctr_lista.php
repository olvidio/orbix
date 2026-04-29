<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\asistentes\helpers\QueCtrListaRender;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/que_ctr_lista_data', $campos);
$payload = is_array($data) ? $data : [];
$payload = QueCtrListaRender::enrich($payload);

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setBlanco(true);
$oDesplCentros->setOpciones((array)($payload['opciones_centros'] ?? []));
$oDesplCentros->setOpcion_sel((string)(($payload['id_ubi_sel'] ?? '') ?: ''));
$oDesplCentros->setAction('fnjs_otro(1)');

$a_campos = [
    'tituloGros' => (string)($payload['tituloGros'] ?? ''),
    'action' => (string)($payload['action'] ?? ''),
    'hash_form_html' => (string)($payload['hash_form_html'] ?? ''),
    'titulo' => (string)($payload['titulo'] ?? ''),
    'n' => (string)($payload['n'] ?? ''),
    'nj' => (string)($payload['nj'] ?? ''),
    'nm' => (string)($payload['nm'] ?? ''),
    'a' => (string)($payload['a'] ?? ''),
    'sssc' => (string)($payload['sssc'] ?? ''),
    'nax' => (string)($payload['nax'] ?? ''),
    'c' => (string)($payload['c'] ?? ''),
    'oDesplCentros' => $oDesplCentros,
    'periodo_form_html' => (string)($payload['periodo_form_html'] ?? ''),
    'locale_us' => $payload['locale_us'] ?? false,
    'mi_sfsv' => (int)($payload['mi_sfsv'] ?? 0),
];

$oView = new ViewNewPhtml('frontend\\asistentes\\view');
$oView->renderizar('que_ctr_lista.phtml', $a_campos);
