<?php

use frontend\shared\PostRequest;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} else {
    $stack = '';
}
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));



$Qna = tessera_imprimir_string(filter_input(INPUT_POST, 'na'));
$Qid_ctr_n = tessera_imprimir_string(filter_input(INPUT_POST, 'id_ctr_n'));
$Qid_ctr_agd = tessera_imprimir_string(filter_input(INPUT_POST, 'id_ctr_agd'));
$Qiasistentes_val = tessera_imprimir_string(filter_input(INPUT_POST, 'iasistentes_val'));
$Qiactividad_val = tessera_imprimir_string(filter_input(INPUT_POST, 'actividad_val'));
$Qperiodo = tessera_imprimir_string(filter_input(INPUT_POST, 'periodo'));
$Qyear = tessera_imprimir_string(filter_input(INPUT_POST, 'year'));
$Qempiezamax = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamax'));
$Qempiezamin = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamin'));
$Qref = tessera_imprimir_string(filter_input(INPUT_POST, 'ref'));
$Qgrupo_estudios = tessera_imprimir_string(filter_input(INPUT_POST, 'grupo_estudios'));
$Qca_estudios = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_estudios'));
$Qca_repaso = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_repaso'));
$Qca_todos = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_todos'));

$dq = actividadestudios_ca_posibles_que_from_payload(
    actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/ca_posibles_que_data', []))
);
$grupo_estudios = $dq['grupo_estudios'];
$mi_grupo = $dq['mi_grupo'];
$aCentrosNExt = $dq['aCentrosNExt'];
$aCentrosAgdExt = $dq['aCentrosAgdExt'];

$oDesplCtrN = new Desplegable();
$oDesplCtrN->setNombre('id_ctr_n');
$oDesplCtrN->setOpciones($aCentrosNExt);
$oDesplCtrN->setOpcion_sel($Qid_ctr_n);
$oDesplCtrN->setBlanco(actividadestudios_desplegable_blanco(1));
$oDesplCtrN->setAction("fnjs_n_a('n')");

$oDesplCtrAgd = new Desplegable();
$oDesplCtrAgd->setNombre('id_ctr_agd');
$oDesplCtrAgd->setOpciones($aCentrosAgdExt);
$oDesplCtrAgd->setOpcion_sel($Qid_ctr_agd);
$oDesplCtrAgd->setBlanco(actividadestudios_desplegable_blanco(1));
$oDesplCtrAgd->setAction("fnjs_n_a('agd')");

$any = empty($Qyear) ? date('Y') : $Qyear;
$aOpciones = array(
    'verano' => _("verano"),
    'curso_ca' => _("curso"),
    'separador' => '---------',
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador1' => '---------',
    'otro' => _("otro")
);
$oFormP = new frontend\shared\web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(src\shared\domain\helpers\strtoupper_dlb(_("periodo de las actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($any);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new HashFront();
$oHash->setCamposForm('id_ctr_agd!id_ctr_n!texto!empiezamax!empiezamin!periodo!ref!iactividad_val!iasistentes_val!year');
$oHash->setCamposNo('na!grupo_estudios!ca_estudios!ca_repaso!ca_todos');
$a_camposHidden = array(
    'asistentes_val' => 1,
    'actividades_val' => 2
);
$oHash->setArraycamposHidden($a_camposHidden);

if ($Qgrupo_estudios === 'todos') {
    $chk_todos = 'checked';
    $chk_grupo = '';
} else {
    $chk_todos = '';
    $chk_grupo = 'checked';
}

if (empty($stack) && empty($Qca_todos)) {
    $Qca_todos = TRUE;
}

$chk_estudios = is_true($Qca_estudios) ? 'checked' : '';
$chk_repaso = is_true($Qca_repaso) ? 'checked' : '';
$chk_ca_todos = is_true($Qca_todos) ? 'checked' : '';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'grupo_estudios' => $grupo_estudios,
    'mi_grupo' => $mi_grupo,
    'oFormP' => $oFormP,
    'oDesplCtrAgd' => $oDesplCtrAgd,
    'oDesplCtrN' => $oDesplCtrN,
    'na' => $Qna,
    'ref' => $Qref,
    'chk_todos' => $chk_todos,
    'chk_grupo' => $chk_grupo,
    'chk_estudios' => $chk_estudios,
    'chk_repaso' => $chk_repaso,
    'chk_ca_todos' => $chk_ca_todos,
    'locale_us' => OrbixRuntime::isLocaleUs(),
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('ca_posibles_que.phtml', $a_campos);
