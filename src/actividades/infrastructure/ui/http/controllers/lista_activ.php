<?php
/**
 * Endpoint backend: tabla con las actividades que cumplen los filtros.
 *
 * Migrado desde frontend/actividades/controller/lista_activ.php. La logica
 * de datos (construir `aWhere`, cabeceras y filas) vive en
 * src\actividades\application\ListaActivTabla. Aqui se orquesta input,
 * `web\Posicion` y renderizacion HTML.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use src\actividades\application\ListaActivTabla;
use src\actividades\domain\value_objects\StatusId;
use web\Lista;

$mi_sfsv = ConfigGlobal::mi_sfsv();

$oPosicion->recordar();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
$stack = isset($_POST['stack'])
    ? filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT)
    : '';

// Si vuelvo con el parametro 'continuar', los datos no estan en el POST sino
// en `oPosicion`. Me paso la referencia del stack donde esta la informacion.
if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack !== '')) {
    $oPosicion->goStack($QGstack);

    $Qque = $oPosicion->getParametro('que');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi = $oPosicion->getParametro('id_ubi');
    $Qperiodo = $oPosicion->getParametro('periodo');
    $Qyear = $oPosicion->getParametro('year');
    $Qdl_org = $oPosicion->getParametro('dl_org');
    $Qempiezamin = $oPosicion->getParametro('empiezamin');
    $Qempiezamax = $oPosicion->getParametro('empiezamax');
    $Qstatus = $oPosicion->getParametro('status');
    $Qc_activ = $oPosicion->getParametro('c_activ');
    $Qasist = $oPosicion->getParametro('asist');
    $Qseccion = $oPosicion->getParametro('seccion');

    $Qid_sel = $oPosicion->getParametro('id_sel');
    $Qscroll_id = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($QGstack);

    $Qssfsv = '';
    $Qsasistentes = '';
    $Qsactividad = '';
    $Qsnom_tipo = '';
} else {
    $Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
    // Si vengo por medio de Posicion, borro la ultima.
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
    $Qque = (string)filter_input(INPUT_POST, 'que');
    $Qstatus = (integer)filter_input(INPUT_POST, 'status');
    $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }

    $Qc_activ = (array)filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qasist = (array)filter_input(INPUT_POST, 'asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qseccion = (array)filter_input(INPUT_POST, 'seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (empty($Qstatus)) {
        $Qa_status = (array)filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qstatus = empty($Qa_status) ? StatusId::ACTUAL : $Qa_status;
    }

    $Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');

    $aGoBack = [
        'que' => $Qque,
        'status' => $Qstatus,
        'id_tipo_activ' => $Qid_tipo_activ,
        'filtro_lugar' => $Qfiltro_lugar,
        'id_ubi' => $Qid_ubi,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'dl_org' => $Qdl_org,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
        'c_activ' => $Qc_activ,
        'asist' => $Qasist,
        'seccion' => $Qseccion,
    ];
    $oPosicion->setParametros($aGoBack, 1);
}

$tituloPrevio = '';
if ($Qque === 'list_active_inv_sg' || $Qque === 'list_activ_sr') {
    // Viene del formulario que_lista_activ_sg.php / que_lista_activ_sr.
    $tituloPrevio = (string)filter_input(INPUT_POST, 'titulo');
}

$oPerm = $_SESSION['oPerm'];
$useCase = new ListaActivTabla();
$data = $useCase->execute(
    [
        'que' => $Qque,
        'status' => $Qstatus,
        'id_tipo_activ' => $Qid_tipo_activ,
        'filtro_lugar' => $Qfiltro_lugar,
        'id_ubi' => $Qid_ubi,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'dl_org' => $Qdl_org,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
        'c_activ' => $Qc_activ,
        'asist' => $Qasist,
        'seccion' => $Qseccion,
        'ssfsv' => $Qssfsv,
        'sasistentes' => $Qsasistentes,
        'sactividad' => $Qsactividad,
        'snom_tipo' => $Qsnom_tipo,
        'titulo' => $tituloPrevio,
    ],
    [
        'mi_sfsv' => $mi_sfsv,
        'perm_vcsd' => $oPerm->have_perm_oficina('vcsd'),
        'perm_des' => $oPerm->have_perm_oficina('des'),
        'perm_sg' => $oPerm->have_perm_oficina('sg'),
        'perm_admin' => $oPerm->have_perm_oficina('admin'),
        'is_dmz' => ConfigGlobal::is_dmz(),
    ]
);

// ----------------------------- html -----------------------------------
?>
<?= $oPosicion->mostrar_left_slide(1) ?>
    <h3><?= $data['titulo'] ?></h3>
<?php
$oTabla = new Lista();
$oTabla->setId_tabla('lista_activ');
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($data['a_valores']);
echo $oTabla->mostrar_tabla();
