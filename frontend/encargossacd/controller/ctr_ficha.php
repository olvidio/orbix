<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\encargossacd\model\DesplCentros;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Ficha de atencion sacerdotal de un centro. Los datos dependientes del centro
 * (calculo de `filtro_ctr` y `opciones_seccion`) se obtienen del backend via
 * {@see \src\encargossacd\application\CtrFichaData} a traves de
 * `/src/encargossacd/ctr_ficha_data`.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = encargossacd_post_int('id_ubi');
$Qfiltro_ctr = encargossacd_post_int('filtro_ctr');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_ficha_data', [
    'id_ubi' => $Qid_ubi,
    'filtro_ctr' => $Qfiltro_ctr,
]);

$Qfiltro_ctr = tessera_imprimir_int($data['filtro_ctr'] ?? $Qfiltro_ctr);
$opciones_seccion = encargossacd_desplegable_opciones($data['opciones_seccion'] ?? []);

$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones_seccion);
$oDesplGrupoCtrs->setOpcion_sel(encargossacd_desplegable_opcion_sel($Qfiltro_ctr));
$oDesplGrupoCtrs->setBlanco(encargossacd_desplegable_blanco(1));
$oDesplGrupoCtrs->setAction("fnjs_lista_ctrs();");

$oDesplCtrs = DesplCentros::build($Qfiltro_ctr, $Qid_ubi);


$apiBase = AppUrlConfig::getApiBaseUrl();
$url_ctr = $apiBase . '/src/encargossacd/ctr_get_select_data';
$oHashCtr = new HashFront();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('filtro_ctr!id_ubi');
$h_ctr = $oHashCtr->linkSinValParams();

$url_ficha = 'frontend/encargossacd/controller/ctr_get_ficha.php';
$oHashFicha = new HashFront();
$oHashFicha->setUrl($url_ficha);
$oHashFicha->setCamposForm('id_ubi');
$h_ficha = $oHashFicha->linkSinValParams();

$fase = 'fase real';

$a_campos = ['oPosicion' => $oPosicion,
    'fase' => $fase,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'url_ficha' => $url_ficha,
    'h_ficha' => $h_ficha,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
    'oDesplCtrs' => $oDesplCtrs,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('ctr_ficha.phtml', $a_campos);
