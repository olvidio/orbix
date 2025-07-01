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

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;
use function core\curso_est;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
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
$oInicurs_ca = curso_est("inicio", $any);
$oFincurs_ca = curso_est("fin", $any);
$inicurs_ca_iso = $oInicurs_ca->getIso();
$fincurs_ca_iso = $oFincurs_ca->getIso();
$inicurs_ca_local = $oInicurs_ca->getFromLocal();
$fincurs_ca_local = $oFincurs_ca->getFromLocal();
$titulo = ucfirst(sprintf(_("lista de certificados emitidos entre %s y %s y no enviados"), $inicurs_ca_local, $fincurs_ca_local));

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'titulo' => $Qtitulo,
    'certificado' => $Qcertificado);
$oPosicion->setParametros($aGoBack, 1);


/////////// Consulta al backend ///////////////////
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/certificados/infrastructure/controllers/certificado_lista_datos.php'
);

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'certificado' => $Qcertificado,
    'inicurs_ca_iso' => $inicurs_ca_iso,
    'fincurs_ca_iso' => $fincurs_ca_iso,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_cabeceras = $data['a_cabeceras'];
$a_valores = $data['a_valores'];
$a_botones = $data['a_botones'];

if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (!empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('certificado_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('certificado');

$oHash1 = new Hash();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');

$oHashDown = new Hash();
$oHashDown->setUrl('frontend/certificados/controller/certificado_pdf_download.php');
$oHashDown->setCamposForm('key');
$h_download = $oHashDown->linkConVal();

$txt_eliminar = _("¿Está seguro que quiere eliminar el certificado?");

$a_campos['oHash'] = $oHash;
$a_campos['oHash1'] = $oHash1;
$a_campos['h_download'] = $h_download;
$a_campos['oTabla'] = $oTabla;
$a_campos['oPosicion'] = $oPosicion;
$a_campos['titulo'] = $titulo;
$a_campos['txt_eliminar'] = $txt_eliminar;

$oView = new ViewNewPhtml('frontend\certificados\controller');
$oView->renderizar('certificado_lista.phtml', $a_campos);
