<?php
/**
 * Pantalla principal del modulo `cartaspresentacion` — shell con filtro
 * dl/r + poblacion, listado AJAX de centros y modal de modificacion.
 *
 * Migrada desde `apps/cartaspresentacion/controller/cartas_presentacion_que.php`
 * + `cartas_presentacion_ajax.php` siguiendo `refactor.md`. Los
 * endpoints backend viven en `/src/cartaspresentacion/...`; los
 * controladores AJAX HTML para la lista y el form viven en
 * `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`
 * y `cartas_presentacion_form.php`.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\DesplegableArray;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$mi_dele = ConfigGlobal::mi_delef();

$aOpcionesCiudad = [
    'get_dl' => $mi_dele,
    'get_r' => _("regiones"),
];
$oSelCiudades = new DesplegableArray('', $aOpcionesCiudad, '');
$oSelCiudades->setBlanco('t');
$oSelCiudades->setNombre('tipo_lista');
$oSelCiudades->setAction('fnjs_poblacion()');

$web = rtrim(ConfigGlobal::getWeb(), '/');

// URL y Hash para ver el detalle de un ubi (delega en el home de ubis).
$url_ctr = $web . '/frontend/ubis/controller/home_ubis.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('bloque!pau!id_ubi');
$h_ctr = $oHashCtr->linkSinValParams();

// Hash del form `#seleccion`: se envia por AJAX con `$(form).serialize()`
// al controlador frontend `cartas_presentacion_ubis_lista.php`.
$url_lista = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php';
$oHashLista = new Hash();
$oHashLista->setUrl($url_lista);
$oHashLista->setCamposForm('tipo_lista');
$oHashLista->setCamposNo('scroll_id!sel!poblacion_sel');

// URL y Hash para el formulario modal de modificacion (AJAX HTML).
$url_form = $web . '/frontend/cartaspresentacion/controller/cartas_presentacion_form.php';
$oHashForm = new Hash();
$oHashForm->setUrl($url_form);
$oHashForm->setCamposForm('id_direccion!id_ubi');
$h_form = $oHashForm->linkSinVal();

// URL y Hash para el desplegable dinamico de poblaciones (JSON).
$url_poblaciones = $web . '/src/cartaspresentacion/poblaciones_data';
$oHashPob = new Hash();
$oHashPob->setUrl($url_poblaciones);
$oHashPob->setCamposForm('filtro');
$h_poblaciones = $oHashPob->linkSinValParams();

// URL de la mutacion de guardar (JSON). El hash viaja en el body del
// form `#frm_pres` (se genera en el controlador del form).
$url_update = $web . '/src/cartaspresentacion/carta_presentacion_update';

// URL + Hash para la mutacion de eliminar (JSON).
$oHashEliminar = new Hash();
$oHashEliminar->setUrl($web . '/src/cartaspresentacion/carta_presentacion_eliminar');
$oHashEliminar->setCamposForm('id_ubi!id_direccion');
$h_eliminar = $oHashEliminar->linkSinValParams();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oSelCiudades' => $oSelCiudades,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'url_lista' => $url_lista,
    'oHashLista' => $oHashLista,
    'url_form' => $url_form,
    'h_form' => $h_form,
    'url_poblaciones' => $url_poblaciones,
    'h_poblaciones' => $h_poblaciones,
    'url_update' => $url_update,
    'url_eliminar' => $web . '/src/cartaspresentacion/carta_presentacion_eliminar',
    'h_eliminar' => $h_eliminar,
    'txt_confirmar_eliminar' => (string)_("¿Está seguro que quiere quitar los datos de presentación de este centro?"),
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\controller');
$oView->renderizar('cartas_presentacion.phtml', $a_campos);
