<?php

declare(strict_types=1);

namespace frontend\devel_db_admin\controller;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_lista_data');
$data = is_array($data) ? $data : [];

$oTabla = new Lista();
$oTabla->setId_tabla('devel_db_admin_migraciones');
$oTabla->setCabeceras((array) ($data['a_cabeceras'] ?? []));
$oTabla->setDatos((array) ($data['a_valores'] ?? []));
$oTabla->setBotones([
    ['txt' => _('ejecutar seleccionadas'), 'click' => 'fnjs_migraciones_ejecutar_seleccion()'],
    ['txt' => _('ejecutar hasta la marcada'), 'click' => 'fnjs_migraciones_ejecutar_hasta()'],
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
    'msg_sin_seleccion' => _('debe seleccionar una migracion'),
    'msg_hasta_unica' => _('debe marcar una sola migracion para ejecutar hasta esa'),
    'msg_error_respuesta' => _('No se puede interpretar la respuesta del servidor'),
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('migraciones_lista.phtml', $a_campos);
