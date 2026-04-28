<?php
/**
 * Pantalla principal del modulo `actividadessacd`.
 *
 * Lista las actividades del tipo elegido en el menu (na / sg / sr / sssc /
 * sv / sf / sf_na / sf_sg / sf_sr / `falta_sacd` / `solape`) y, para cada
 * una, los sacd encargados.
 *
 * La pantalla se limita a renderizar la barra de filtros (periodo) y los
 * contenedores vacios. El listado + todas las mutaciones se cargan via
 * AJAX contra los endpoints `/src/actividadessacd/*` definidos en
 * `src/actividadessacd/config/routes.php`.
 *
 * Migrada desde `apps/actividadessacd/controller/activ_sacd.php` +
 * `apps/actividadessacd/controller/activ_sacd_ajax.php` (dispatcher
 * legacy) siguiendo el patron de `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use function src\shared\domain\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');

$titulo = strtoupper_dlb(_("periodo del listado del año próximo"));
$titulo .= '. ';
$titulo .= '(' . sprintf(_("actividades de %s"), $Qtipo) . ')';

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

$perm_des = isset($_SESSION['oPerm'])
    && $_SESSION['oPerm']->have_perm_oficina('des');

// Por cada endpoint AJAX, construir la URL firmada (`url + linkSinVal()`):
// la firma cubre URL + nombres de campos; los valores viajan en el body POST.
$api = AppUrlConfig::getApiBaseUrl();
$buildHashedUrl = static function (string $url, string $campos): string {
    $oHash = new HashFront();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_lista = $buildHashedUrl(
    $api . '/src/actividadessacd/lista_actividades_sacd_data',
    'tipo!year!periodo!empiezamin!empiezamax'
);
$url_solapes = $buildHashedUrl(
    $api . '/src/actividadessacd/solapes_sacd_data',
    'year!periodo!empiezamin!empiezamax'
);
$url_encargados = $buildHashedUrl(
    $api . '/src/actividadessacd/sacds_encargados_data',
    'id_activ!id_tipo_activ!dl_org'
);
$url_disponibles = $buildHashedUrl(
    $api . '/src/actividadessacd/sacds_disponibles_data',
    'id_activ!seleccion'
);
$url_asignar = $buildHashedUrl(
    $api . '/src/actividadessacd/sacd_asignar',
    'id_activ!id_nom'
);
$url_reordenar = $buildHashedUrl(
    $api . '/src/actividadessacd/sacd_reordenar',
    'id_activ!id_nom!num_orden'
);
$url_eliminar = $buildHashedUrl(
    $api . '/src/actividadessacd/sacd_eliminar',
    'id_activ!id_nom!id_cargo'
);

// Hash para los campos del form de filtros (input hidden `hash` del form).
// PeriodoQue::getHtml() incluye iactividad_val / iasistentes_val (hooks JS);
// deben ir en camposNo para que no desalineen h1 vs h2 en validatePost.
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
    'perm_des' => $perm_des,
    'url_lista' => $url_lista,
    'url_solapes' => $url_solapes,
    'url_encargados' => $url_encargados,
    'url_disponibles' => $url_disponibles,
    'url_asignar' => $url_asignar,
    'url_reordenar' => $url_reordenar,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\\actividadessacd\\controller');
$oView->renderizar('activ_sacd.phtml', $a_campos);
