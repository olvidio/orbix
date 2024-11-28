<?php
/**
 * Esta página muestra una tabla con los certificados.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        25/2/23.
 *
 */

use certificados\domain\CertificadoSelect;
use core\ConfigGlobal;
use function core\curso_est;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = ConfigGlobal::mi_delef();
$mi_region = ConfigGlobal::mi_region();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_sel = '';
$Qscroll_id = '';
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');

// otros?
$titulo = $Qtitulo;
// restp
$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $any = (int)date('Y') + 1;
} else {
    $any = (int)date('Y');
}
$inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
$fincurs_ca = curso_est("fin", $any)->format('Y-m-d');
$txt_curso = "$inicurs_ca - $fincurs_ca";
$titulo = ucfirst(sprintf(_("lista de certificados emitidos en el curso %s y no enviados"), $txt_curso));

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'titulo' => $Qtitulo,
    'certificado' => $Qcertificado);
$oPosicion->setParametros($aGoBack, 1);

$a_campos = CertificadoSelect::getCamposVista($Qcertificado, $Qid_sel, $Qscroll_id, $inicurs_ca, $fincurs_ca);

$txt_eliminar = _("¿Está seguro que quiere eliminar el certificado?");

$a_campos['oPosicion'] = $oPosicion;
$a_campos['titulo'] = $titulo;
$a_campos['txt_eliminar'] = $txt_eliminar;

$oView = new core\View('certificados/controller');
$oView->renderizar('certificado_select.phtml', $a_campos);
