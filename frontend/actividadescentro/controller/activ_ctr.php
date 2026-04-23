<?php
/**
 * Pantalla principal del modulo `actividadescentro`.
 *
 * Lista las actividades del tipo elegido en el menu (sg / sr / nagd / sssc /
 * sfsg / sfsr / sfnagd) y, para cada una, los centros encargados.
 *
 * La pantalla se limita a renderizar la barra de filtros (periodo) y los
 * contenedores vacios. El listado + todas las mutaciones se cargan via AJAX
 * contra los endpoints `/src/actividadescentro/*` definidos en
 * `src/actividadescentro/config/routes.php`.
 *
 * Migrada desde `apps/actividadescentro/controller/activ_ctr.php` +
 * `apps/actividadescentro/controller/activ_ctr_ajax.php` (dispatcher legacy)
 * siguiendo el patron de `refactor.md`.
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;
use web\PeriodoQue;
use function core\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');

$titulo = strtoupper_dlb(_("periodo del listado del año próximo"));
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

// $Qtipo viene por menu. Para la sf, deberia ser sfsg pero en el menu esta
// solo sg / sr / nagd:
if (ConfigGlobal::mi_sfsv() === 2) {
    switch ($Qtipo) {
        case 'sg':
            $Qtipo = 'sfsg';
            break;
        case 'sr':
            $Qtipo = 'sfsr';
            break;
        case 'nagd':
            $Qtipo = 'sfnagd';
            break;
    }
}

// Por cada endpoint AJAX, se construye la URL completa `url + linkSinVal()`:
// la firma cubre URL + nombres de campos; los valores viajan en el body POST.
$web = rtrim(ConfigGlobal::getWeb(), '/');
$buildHashedUrl = static function (string $url, string $campos) {
    $oHash = new Hash();
    $oHash->setUrl($url);
    $oHash->setCamposForm($campos);
    return $url . $oHash->linkSinVal();
};

$url_lista = $buildHashedUrl(
    $web . '/src/actividadescentro/lista_actividades_ctr_data',
    'tipo!year!periodo!empiezamin!empiezamax'
);
$url_encargados = $buildHashedUrl(
    $web . '/src/actividadescentro/centros_encargados_data',
    'id_activ!id_tipo_activ!dl_org'
);
$url_disponibles = $buildHashedUrl(
    $web . '/src/actividadescentro/centros_disponibles_data',
    'tipo!id_activ!inicio!fin!f_ini_act'
);
$url_asignar = $buildHashedUrl(
    $web . '/src/actividadescentro/centro_encargado_asignar',
    'id_activ!id_ubi'
);
$url_reordenar = $buildHashedUrl(
    $web . '/src/actividadescentro/centro_encargado_reordenar',
    'id_activ!id_ubi!num_orden'
);
$url_eliminar = $buildHashedUrl(
    $web . '/src/actividadescentro/centro_encargado_eliminar',
    'id_activ!id_ubi'
);

// Hash para los campos del form de filtros (input hidden `hash` del form).
// PeriodoQue::getHtml() incluye iactividad_val / iasistentes_val (hooks JS);
// deben ir en camposNo para que no desalineen h1 vs h2 en validatePost.
$oHash = new Hash();
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
    'url_lista' => $url_lista,
    'url_encargados' => $url_encargados,
    'url_disponibles' => $url_disponibles,
    'url_asignar' => $url_asignar,
    'url_reordenar' => $url_reordenar,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\\actividadescentro\\controller');
$oView->renderizar('activ_ctr.phtml', $a_campos);
