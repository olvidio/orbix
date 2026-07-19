<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Pantalla principal del modulo `actividadescentro`.
 *
 * Lista las actividades del tipo elegido en el menu (sg / sr / nagd / sssc /
 * sfsg / sfsr / sfnagd) y, para cada una, los centros encargados.
 *
 * La pantalla se limita a renderizar la barra de filtros (periodo) y los
 * contenedores vacios. El listado + todas las mutaciones se cargan via AJAX
 * contra los endpoints `/src/actividadescentro/*` definidos en
 * `src/actividadescentro/config/routes.php`. Solo capa frontend en imports.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');

$shell = PostRequest::getDataFromUrl('/src/actividadescentro/activ_ctr_shell_data', [
    'tipo' => $Qtipo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
]);
$Qtipo = \frontend\shared\helpers\PayloadCoercion::string($shell['tipo'] ?? $Qtipo);

$signShellEndpoint = static function (array $spec): string {
    $path = (string)($spec['path'] ?? '');
    $camposForm = (string)($spec['campos_form'] ?? '');
    if ($path === '') {
        return '';
    }
    $url = AppUrlConfig::browserUrlFromAppRelative($path);
    $oHashEndpoint = new HashFront();
    $oHashEndpoint->setUrl($url);
    $oHashEndpoint->setCamposForm($camposForm);

    return $url . $oHashEndpoint->linkSinVal();
};

$titulo = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("periodo del listado del año próximo"));
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
$oFormP->setTitulo($titulo);
$oFormP->setFormName('frm_cond');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setBoton("<input type=\"button\" name=\"buscar\" value=\"" . _("buscar") . "\" onclick=\"fnjs_ver();\">");

$oHash = new HashFront();
$oHash->setCamposForm('empiezamax!empiezamin!periodo!year!tipo');
$oHash->setCamposNo('iactividad_val!iasistentes_val');
$oHash->setArraycamposHidden([
    'tipo' => $Qtipo,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'tipo' => $Qtipo,
    'url_lista' => $signShellEndpoint(is_array($shell['url_lista'] ?? null) ? $shell['url_lista'] : []),
    'url_encargados' => $signShellEndpoint(is_array($shell['url_encargados'] ?? null) ? $shell['url_encargados'] : []),
    'url_disponibles' => $signShellEndpoint(is_array($shell['url_disponibles'] ?? null) ? $shell['url_disponibles'] : []),
    'url_asignar' => $signShellEndpoint(is_array($shell['url_asignar'] ?? null) ? $shell['url_asignar'] : []),
    'url_reordenar' => $signShellEndpoint(is_array($shell['url_reordenar'] ?? null) ? $shell['url_reordenar'] : []),
    'url_eliminar' => $signShellEndpoint(is_array($shell['url_eliminar'] ?? null) ? $shell['url_eliminar'] : []),
];

$oView = new ViewNewPhtml('frontend\\actividadescentro\\controller');
$oView->renderizar('activ_ctr.phtml', $a_campos);
