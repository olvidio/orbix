<?php
/**
 * Pantalla de filtro para el balance de plazas entre dos dl:
 * muestra un desplegable con las dl disponibles y un `#comparativa`
 * vacio que se rellena via AJAX con `plazas_balance_dl.php` (frontend,
 * devuelve HTML) al cambiar el valor del select.
 *
 * Migrada desde `apps/actividadplazas/controller/plazas_balance_que.php`
 * siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use src\ubis\application\services\DelegacionDropdown;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use src\actividades\domain\entity\TiposActividades;

require_once 'frontend/shared/global_header_front.inc';

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
if (empty($Qid_tipo_activ)) {
    $Qssfsv = '';
    $mi_sfsv = OrbixRuntime::miSfsv();
    if ($mi_sfsv === 1) {
        $Qssfsv = 'sv';
    }
    if ($mi_sfsv === 2) {
        $Qssfsv = 'sf';
    }
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $oTipoActiv = new TiposActividades();
    $oTipoActiv->setSfsvText($Qssfsv);
    $oTipoActiv->setAsistentesText($Qsasistentes);
    $oTipoActiv->setActividadText($Qsactividad);
    $Qid_tipo_activ = (string)$oTipoActiv->getId_tipo_activ();
}

$desplDelegaciones = Desplegable::desdeOpciones(DelegacionDropdown::activasOrdenNombre(), 'dl');
$desplDelegaciones->setAction('fnjs_comparativa()');

$mi_dele = OrbixRuntime::miDelef();
$txt = sprintf(_("comparar %s con:"), $mi_dele);

$url_balance_dl = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadplazas/controller/plazas_balance_dl.php';
$oHash = new HashFront();
$oHash->setUrl($url_balance_dl);
$oHash->setCamposForm('dl!id_tipo_activ');
$h = $oHash->linkSinValParams();

$a_campos = [
    'Qid_tipo_activ' => $Qid_tipo_activ,
    'h' => $h,
    'txt' => $txt,
    'desplDelegaciones' => $desplDelegaciones,
    'url_balance_dl' => $url_balance_dl,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('plazas_balance_que.phtml', $a_campos);
