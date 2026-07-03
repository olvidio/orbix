<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\misas\helpers\MisasDesplegableSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/misas/modificar_plantilla_data');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($data['zonas_opciones'] ?? []));
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_plantilla_zona()');

$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones(MisasDesplegableSupport::opciones($data['tipos_plantilla'] ?? []));
$oDesplTipoPlantilla->setNombre('tipo_plantilla');
$oDesplTipoPlantilla->setOpcion_sel(\frontend\shared\helpers\PayloadCoercion::string($data['plantilla_selected'] ?? ''));
$oDesplTipoPlantilla->setAction('fnjs_ver_plantilla_zona()');

$a_TiposPlantilla2 = array_merge(
    ['-' => ''],
    MisasDesplegableSupport::opciones($data['tipos_plantilla'] ?? [])
);
$oDesplImportarDePlantilla = new Desplegable();
$oDesplImportarDePlantilla->setOpciones(MisasDesplegableSupport::opciones($a_TiposPlantilla2));
$oDesplImportarDePlantilla->setNombre('importar_de_plantilla');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones(MisasDesplegableSupport::opciones($data['orden_opciones'] ?? []));
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_plantilla_zona()');

$url_importar_plantilla = 'frontend/misas/controller/importar_plantilla.php';
$oHashImportarPlantilla = new HashFront();
$oHashImportarPlantilla->setUrl($url_importar_plantilla);
$oHashImportarPlantilla->setCamposForm('id_zona!tipo_plantilla_origen!tipo_plantilla_destino');
$h_importar_plantilla = $oHashImportarPlantilla->linkSinValParams();

$url_modificar_cuadricula_zona = 'frontend/misas/controller/modificar_cuadricula_zona.php';
$oHashZonaTipo = new HashFront();
$oHashZonaTipo->setUrl($url_modificar_cuadricula_zona);
$oHashZonaTipo->setCamposForm('id_zona!tipo_plantilla!orden');
$h_zona_tipo = $oHashZonaTipo->linkSinValParams();

$oHash = new HashFront();
$oHash->setUrl('frontend/misas/controller/modificar_plantilla.php');
$oHash->setCamposForm('id_zona!tipo_plantilla!orden!importar_de_plantilla');

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oDesplImportarDePlantilla' => $oDesplImportarDePlantilla,
    'oDesplOrden' => $oDesplOrden,
    'url_modificar_cuadricula_zona' => $url_modificar_cuadricula_zona,
    'url_importar_plantilla' => $url_importar_plantilla,
    'h_zona_tipo' => $h_zona_tipo,
    'h_importar_plantilla' => $h_importar_plantilla,
    'oHash' => $oHash,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'modificar_plantilla.phtml', $a_campos);
