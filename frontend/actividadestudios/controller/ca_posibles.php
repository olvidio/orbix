<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Periodo;

/**
 * Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        5/3/03.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$obj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qgrupo_estudios = (string)filter_input(INPUT_POST, 'grupo_estudios');
$Qtexto = (string)filter_input(INPUT_POST, 'texto');
$Qref = (string)filter_input(INPUT_POST, 'ref');
$Qidca = (string)filter_input(INPUT_POST, 'idca');
$Qca_estudios = (string)filter_input(INPUT_POST, 'ca_estudios');
$Qca_repaso = (string)filter_input(INPUT_POST, 'ca_repaso');
$Qca_todos = (string)filter_input(INPUT_POST, 'ca_todos');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (empty($a_sel)) {
    $Qid_ctr_agd = (integer)filter_input(INPUT_POST, 'id_ctr_agd');
    $Qid_ctr_n = (integer)filter_input(INPUT_POST, 'id_ctr_n');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qyear = (integer)filter_input(INPUT_POST, 'year');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    if (empty($Qid_ctr_agd) && empty($Qid_ctr_n)) {
        $msg_txt = _("debe seleccionar un centro o grupo de centros");
        exit($msg_txt);
    }

    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_ca';
    }

    $oPeriodo = Periodo::conCalendarioDesdeBackend();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $aGoBack = array(
        'id_ctr_agd' => $Qid_ctr_agd,
        'id_ctr_n' => $Qid_ctr_n,
        'na' => $Qna,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
        'grupo_estudios' => $Qgrupo_estudios,
        'ref' => $Qref,
        'ca_estudios' => $Qca_estudios,
        'ca_repaso' => $Qca_repaso,
        'ca_todos' => $Qca_todos,
    );
    $oPosicion->setParametros($aGoBack, 1);
}

$data = PostRequest::getDataFromUrl('/src/actividadestudios/ca_posibles_data', PostRequest::requestPayloadForHash());

if (($data['modo'] ?? '') === 'lista') {
    if (!empty($data['msg_txt'])) {
        echo "<div class='no_print'>" . $data['msg_txt'] . "</div>";
    }
    $spec = $data['pagina_link_spec'] ?? null;
    $pagina = is_array($spec) ? HashFrontSignedLink::fromSpec($spec) : '';

    $a_campos = ['oPosicion' => $oPosicion,
        'msg_txt' => $data['msg_txt'] ?? '',
        'titulo' => $data['titulo'] ?? '',
        'stgr' => $data['stgr'] ?? '',
        'aActividades' => $data['aActividades'] ?? [],
        'pagina' => $pagina,
    ];
    $oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
    $oView->renderizar('ca_posibles_lista.phtml', $a_campos);
} else {
    foreach ($data['tabla_filas'] ?? [] as $fila) {
        $a_campos = ['oPosicion' => $oPosicion,
            'msg_txt' => $fila['msg_txt'] ?? '',
            'texto' => $fila['texto'] ?? '',
            'nc_bienio' => $fila['nc_bienio'] ?? 0,
            'nc_cuadrienio1' => $fila['nc_cuadrienio1'] ?? 0,
            'nc_cuadrienio2' => $fila['nc_cuadrienio2'] ?? 0,
            'nc_cuadrienio' => $fila['nc_cuadrienio'] ?? 0,
            'nc_repaso' => $fila['nc_repaso'] ?? 0,
            'nc_ce' => $fila['nc_ce'] ?? 0,
            'nc_otros' => $fila['nc_otros'] ?? 0,
            'stgr' => $fila['stgr'] ?? '',
            'ctr' => $fila['ctr'] ?? '',
            'ref' => $fila['ref'] ?? '',
            'height' => $fila['height'] ?? 1,
            'cPersonas' => $fila['cPersonas'] ?? [],
            'aActividades' => $fila['aActividades'] ?? [],
        ];
        $oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
        $oView->renderizar('ca_posibles_cuadro.phtml', $a_campos);
    }
}
