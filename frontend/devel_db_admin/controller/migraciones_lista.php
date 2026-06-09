<?php

declare(strict_types=1);

namespace frontend\devel_db_admin\controller;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_lista_data');
$data = is_array($data) ? $data : [];

$oTabla = new Lista();
$oTabla->setId_tabla('devel_db_admin_migraciones');
$oTabla->setCabeceras((array) ($data['a_cabeceras'] ?? []));
$oTabla->setDatos((array) ($data['a_valores'] ?? []));
$oTabla->setBotones([
    ['txt' => _('ejecutar seleccionadas'), 'click' => 'fnjs_migraciones_ejecutar_seleccion()'],
    ['txt' => _('ejecutar hasta la marcada'), 'click' => 'fnjs_migraciones_ejecutar_hasta()'],
    ['txt' => _('quitar registro de seleccionadas'), 'click' => 'fnjs_migraciones_quitar_registro()'],
]);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setArrayCamposHidden([
    'modo' => '',
    'prefijo_hasta' => '',
]);
$oHash->setCamposNo('modo!prefijo_hasta');

$a_campos = [
    'oTabla' => $oTabla,
    'oHash' => $oHash,
    'warnings' => (array) ($data['warnings'] ?? []),
    'url_ejecutar' => OrbixRuntime::getWeb() . '/src/devel_db_admin/migraciones_ejecutar',
    'url_quitar_registro' => OrbixRuntime::getWeb() . '/src/devel_db_admin/migraciones_quitar_registro',
    'msg_sin_seleccion' => _('debe seleccionar una migracion'),
    'msg_confirmar_quitar_registro' => _('Esto solo borra el registro de control en migracion_aplicada; no revierte cambios en la base de datos. ¿Continuar?'),
    'msg_hasta_unica' => _('debe marcar una sola migracion para ejecutar hasta esa'),
    'msg_error_respuesta' => _('No se puede interpretar la respuesta del servidor'),
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('migraciones_lista.phtml', $a_campos);
