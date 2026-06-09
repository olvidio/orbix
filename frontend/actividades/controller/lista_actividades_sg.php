<?php
/**
 * Pantalla que lista actividades sf/sg (crt, cv) con los filtros de
 * periodo, tipo, lugar y delegacion.
 *
 * La logica de dominio se ha trasladado a
 * `src\actividades\application\ListaActividadesSgListado` y se consume via
 * PostRequest. Este controlador solo parsea el POST, gestiona `Posicion`
 * y construye los hashes de los formularios, firma `link_spec` y renderiza la tabla (`Lista`).
 *
 * Migrado desde frontend/actividades/controller/lista_actividades_sg.php.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\actividades\helpers\ActividadStatusId;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Lista;

require_once __DIR__ . '/../helpers/actividades_support.php';
use frontend\shared\web\PeriodoQue;
use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}

if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack !== 0)) {
    $oPosicion->goStack($QGstack);
    $Qque = $oPosicion->getParametro('que');
    $Qstatus = $oPosicion->getParametro('status');
    $Qid_tipo_activ_pos = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi = $oPosicion->getParametro('id_ubi');
    $Qperiodo = $oPosicion->getParametro('periodo');
    $Qyear = $oPosicion->getParametro('year');
    $Qdl_org = $oPosicion->getParametro('dl_org');
    $Qempiezamin = $oPosicion->getParametro('empiezamin');
    $Qempiezamax = $oPosicion->getParametro('empiezamax');
    $Qtipo_activ_sg = $oPosicion->getParametro('tipo_activ_sg');
    $oPosicion->olvidar($QGstack);

    $Qid_sel = [];
    $Qscroll_id = '';
} else {
    $Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
    $Qque = (string)filter_input(INPUT_POST, 'que');
    $Qstatus = (integer)filter_input(INPUT_POST, 'status');
    $Qtipo_activ_sg = (string)filter_input(INPUT_POST, 'tipo_activ_sg');
    $Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    if (empty($Qperiodo)) {
        switch ($Qtipo_activ_sg) {
            case 'crt':
                $Qperiodo = 'curso_crt';
                break;
            case 'cv':
                $Qperiodo = 'curso_ca';
                break;
        }
    }

    $Qstatus = empty($Qstatus) ? ActividadStatusId::ACTUAL : $Qstatus;

    $aGoBack = [
        'que' => $Qque,
        'tipo_activ_sg' => $Qtipo_activ_sg,
        'id_ubi' => $Qid_ubi,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'dl_org' => $Qdl_org,
        'status' => $Qstatus,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
    ];
    $oPosicion->setParametros($aGoBack, 1);
}

// Delegamos el calculo del listado al caso de uso backend.
$data = PostRequest::getDataFromUrl('/src/actividades/lista_actividades_sg_datos', [
    'continuar' => $Qcontinuar,
    'status' => $Qstatus,
    'tipo_activ_sg' => $Qtipo_activ_sg,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'sel' => $Qid_sel,
    'scroll_id' => $Qscroll_id,
    'stack_go' => $oPosicion->getStack(),
]);

if (!empty($data['advertencia_demasiadas']) && is_array($data['advertencia_demasiadas'])) {
    $ad = $data['advertencia_demasiadas'];
    $go_avant = HashFrontSignedLink::tryFromSpec($ad['continuar_link_spec'] ?? null);
    $go_atras = HashFrontSignedLink::tryFromSpec($ad['volver_link_spec'] ?? null);
    $numActiv = tessera_imprimir_int($ad['num_actividades'] ?? 0);
    $html_advertencia = '<h2>' . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $numActiv) . '</h2>';
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_atras . "') value=" . _("volver") . ">";
    echo $html_advertencia;
    die();
}

$a_valores = actividades_lista_valores_from_payload($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_actividades_sg');
$oTabla->setCabeceras(actividades_lista_cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setBotones(actividades_lista_botones($data['a_botones'] ?? []));
$oTabla->setDatos($a_valores);
$html_tabla = $oTabla->mostrar_tabla();
unset($data['a_cabeceras'], $data['a_botones'], $data['a_valores']);
$result_busqueda = tessera_imprimir_string($data['result_busqueda'] ?? '');
$Qid_tipo_activ = tessera_imprimir_string($data['id_tipo_activ'] ?? '');

$aOpciones = [
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'curso_ca' => _('curso ca'),
    'curso_crt' => _('curso crt'),
    'separador1' => '---------',
    'otro' => _('otro'),
];

$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setAntes(strtoupper_dlb(_("periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel(actividades_posicion_string($Qperiodo));
$oFormP->setDesplAnysOpcion_sel(actividades_posicion_string($Qyear));
$oFormP->setEmpiezaMin(actividades_posicion_string($Qempiezamin));
$oFormP->setEmpiezaMax(actividades_posicion_string($Qempiezamax));

$oHash = new HashFront();
$oHash->setUrl('frontend/actividades/controller/lista_actividades_sg.php');
$a_camposHidden = [
    'que' => $Qque,
    'tipo_activ_sg' => $Qtipo_activ_sg,
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'status' => $Qstatus,
    'filtro_lugar' => $Qfiltro_lugar,
];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('modo!id_tipo_activ!id_ubi!periodo!year!dl_org!status!empiezamin!empiezamax!filtro_lugar');

$oHashSel = new HashFront();
$oHashSel->setCamposForm('!sel!mod!queSel');
$oHashSel->setcamposNo('continuar!scroll_id');
$a_camposHiddenSel = [
    'pau' => 'a',
    'permiso' => '3',
    'tabla' => 'a_actividades',
    'tabla_pau' => 'a_actividades',
    'Gstack' => $oPosicion->getStack(),
];
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
    'oHashSel' => $oHashSel,
    'Qid_tipo_activ' => $Qid_tipo_activ,
    'que' => $Qque,
    'html_tabla' => $html_tabla,
    'result_busqueda' => $result_busqueda,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('lista_actividades_sg.phtml', $a_campos);
