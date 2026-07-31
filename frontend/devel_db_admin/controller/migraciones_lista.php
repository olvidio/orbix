<?php

declare(strict_types=1);

namespace frontend\devel_db_admin\controller;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\PayloadCoercion;
use frontend\actividades\helpers\ActividadesListaSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_lista_data');

$oTabla = new Lista();
$oTabla->setId_tabla('devel_db_admin_migraciones');
$oTabla->setCabeceras(ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setDatos(ActividadesListaSupport::datos($data['a_valores'] ?? []));
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
    'serie' => PayloadCoercion::string($data['serie'] ?? ''),
    'url_ejecutar' => OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/migraciones_ejecutar.php',
    'url_quitar_registro' => OrbixRuntime::getWeb() . '/frontend/devel_db_admin/controller/migraciones_quitar_registro.php',
    'msg_sin_seleccion' => _('debe seleccionar una migracion'),
    'msg_confirmar_quitar_registro' => _('Esto solo borra el registro de control en migracion_aplicada; no revierte cambios en la base de datos. ¿Continuar?'),
    'msg_hasta_unica' => _('debe marcar una sola migracion para ejecutar hasta esa'),
    'msg_error_respuesta' => _('No se puede interpretar la respuesta del servidor'),
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('migraciones_lista.phtml', $a_campos);
