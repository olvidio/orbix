<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

/**
 * AJAX del selector de tipo de actividad en modo gestión (`_actividad_tipo_gestion.js.html.twig`): tabla de los
 * códigos y nombres de tipo que ya existen para el prefijo elegido (`entrada`). Los datos vienen de
 * `/src/actividades/actividad_tipo_get` (salida `nom_tipo_posibles`). Responde `{content: html}`.
 */
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/actividades/actividad_tipo_get', [
    'salida' => 'nom_tipo_posibles',
    'entrada' => (string) filter_input(INPUT_POST, 'entrada'),
]);

$a_valores = [];
$i = 0;
foreach (is_array($data['nom_tipos'] ?? null) ? $data['nom_tipos'] : [] as $par) {
    if (!is_array($par)) {
        continue;
    }
    $i++;
    $a_valores[$i][1] = (string) ($par[0] ?? '');
    $a_valores[$i][2] = (string) ($par[1] ?? '');
}

$oTabla = new Lista();
$oTabla->setBotones([]);
$oTabla->setCabeceras([_("id"), _("nombre")]);
$oTabla->setDatos($a_valores);
$oTabla->setFormatoTabla('html');

AjaxJsonSupport::response('', ['content' => $oTabla->mostrar_tabla()]);
