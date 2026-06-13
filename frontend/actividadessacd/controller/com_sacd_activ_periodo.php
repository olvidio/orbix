<?php

/**
 * Pantalla unificada para la comunicacion de actividades a los sacd.
 *
 * Cubre los dos flujos de entrada legacy:
 *
 *  1. Menu → pantalla con formulario de seleccion de periodo + botones
 *     "buscar" y "enviar mail" (legacy `com_sacd_activ_periodo.php`).
 *  2. `frontend/personas/view/personas_select.phtml` → posteo con
 *     `que=un_sacd` + `sel[]` → la pantalla se abre y se auto-dispara
 *     la busqueda sobre el unico sacd seleccionado (legacy
 *     `com_sacd_activ.php` con `Qque=un_sacd`).
 *
 * Los datos se cargan siempre via AJAX contra los endpoints
 *   `/src/actividadessacd/comunicacion_activ_sacd_data`
 *   `/src/actividadessacd/comunicacion_activ_sacd_enviar`
 * definidos en `src/actividadessacd/config/routes.php`.
 *
 * Migrada desde `apps/actividadessacd/controller/com_sacd_activ_periodo.php` +
 * `apps/actividadessacd/controller/com_sacd_activ.php` +
 * `apps/actividadessacd/model/ComunicarActividadesSacd.php` +
 * `apps/actividadessacd/model/ActividadesSacdFunciones.php` +
 * `apps/actividadessacd/view/com_sacd_activ_periodo.html.twig` +
 * `apps/actividadessacd/view/com_sacd_activ_print.phtml` +
 * `apps/actividadessacd/view/com_un_sacd_activ_print.phtml`,
 * siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');
$Qque = (string)filter_input(INPUT_POST, 'que');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');

// Posteo desde personas_select: viene `sel[]` con `id_nom#id_tabla`.
if ($Qque === 'un_sacd') {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) {
        $Qid_nom = (int)strtok((string)$a_sel[0], '#');
    }
    if ($Qperiodo === '') {
        $Qperiodo = 'curso_crt';
        $Qyear = (string)date('Y');
    }
}

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$aOpciones = [
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro"),
];

$oFormP = new PeriodoQue();
$oFormP->setFormName('seleccion');
$oFormP->setTitulo(src\shared\domain\helpers\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$sBotonBuscar = "<input type=button name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_ver();\">";
$sBotonEnviar = "<input type=button name=\"enviar\" value=\"" . _("enviar mail") . "\" onclick=\"fnjs_enviar_mails();\">";
$oFormP->setBoton("$sBotonBuscar  $sBotonEnviar");

$pageData = PostRequest::getDataFromUrl('/src/actividadessacd/com_sacd_activ_periodo_page_data', []);
$perm_mod_txt = (bool)($pageData['perm_mod_txt'] ?? true);

$oHash = new HashFront();
$oHash->setCamposForm('empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$a_camposHidden = [
    'sacd' => 'uno',
    'id_nom' => $Qid_nom,
    'que' => $Qque === '' ? 'nagd' : $Qque,
    'propuesta' => $Qpropuesta,
];
$oHash->setArraycamposHidden($a_camposHidden);

$api = AppUrlConfig::getApiBaseUrl();
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHashUrl = new HashFront();
    $oHashUrl->setUrl($url);
    $oHashUrl->setCamposForm($campos);
    return $url . $oHashUrl->linkSinVal();
};

$camposForm = 'que!id_nom!propuesta!periodo!year!empiezamin!empiezamax!sel';
$url_data = $buildHashedUrl(
    $api . '/src/actividadessacd/comunicacion_activ_sacd_data',
    $camposForm
);
$url_enviar = $buildHashedUrl(
    $api . '/src/actividadessacd/comunicacion_activ_sacd_enviar',
    $camposForm
);
$url_com_txt = HashFront::link('frontend/actividadessacd/controller/com_sacd_txt.php');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'Qque' => $Qque === '' ? 'nagd' : $Qque,
    'perm_mod_txt' => $perm_mod_txt,
    'url_data' => $url_data,
    'url_enviar' => $url_enviar,
    'url_com_txt' => $url_com_txt,
    'auto_cargar' => $Qque === 'un_sacd',
];

$oView = new ViewNewPhtml('frontend\\actividadessacd\\controller');
$oView->renderizar('com_sacd_activ_periodo.phtml', $a_campos);
