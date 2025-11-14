<?php
/**
 * Esta página muestra un formulario con las opciones para escoger a una persona.
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ViewPhtml;
use src\asignaturas\application\repositories\AsignaturaRepository;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qnumero = (string)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
if ($Qb_c == 'b') {
    $chk_b = 'checked';
    $chk_c = '';
} else {
    $chk_b = '';
    $chk_c = 'checked';
}
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$chk_c1 = empty($Qc1) ? '' : 'checked';
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$chk_c2 = empty($Qc2) ? '' : 'checked';
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$chk_n = empty($Qpersonas_n) ? '' : 'checked';
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$chk_agd = empty($Qpersonas_agd) ? '' : 'checked';

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qid_asignatura = (string)filter_input(INPUT_POST, 'id_asignatura');

$Qlista = (string)filter_input(INPUT_POST, 'lista');
$chk_lista = empty($Qlista) ? '' : 'checked';

$AsignaturaRepository = new AsignaturaRepository();;
$aOpciones = $AsignaturaRepository->getArrayAsignaturasConSeparador();
$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setOpcion_sel($Qid_asignatura);

$oHash = new Hash();
$oHash->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash->setCamposForm('numero!b_c');

$oHash1 = new Hash();
$oHash1->setcamposChk('personas_n!personas_agd!c1!c2');
$oHash1->setCamposForm('id_asignatura!b_c');


$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'numero' => $Qnumero,
    'chk_n' => $chk_n,
    'chk_agd' => $chk_agd,
    'chk_b' => $chk_b,
    'chk_c' => $chk_c,
    'chk_c1' => $chk_c1,
    'chk_c2' => $chk_c2,
    'chk_lista' => $chk_lista,
];

$oView = new ViewPhtml('notas\controller');
$oView->renderizar('asig_faltan_que.phtml', $a_campos);