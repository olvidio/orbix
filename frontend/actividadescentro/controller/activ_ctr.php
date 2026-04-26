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
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use frontend\shared\web\PeriodoQue;
use function src\shared\domain\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');

$shell = PostRequest::getDataFromUrl('/src/actividadescentro/activ_ctr_shell_data', [
    'tipo' => $Qtipo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
]);
$Qtipo = (string)($shell['tipo'] ?? $Qtipo);

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
    'url_lista' => (string)($shell['url_lista'] ?? ''),
    'url_encargados' => (string)($shell['url_encargados'] ?? ''),
    'url_disponibles' => (string)($shell['url_disponibles'] ?? ''),
    'url_asignar' => (string)($shell['url_asignar'] ?? ''),
    'url_reordenar' => (string)($shell['url_reordenar'] ?? ''),
    'url_eliminar' => (string)($shell['url_eliminar'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\actividadescentro\\controller');
$oView->renderizar('activ_ctr.phtml', $a_campos);
