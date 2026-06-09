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

use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use function frontend\shared\helpers\curso_est;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_sel = '';
$Qscroll_id = '';
//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
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

// comprobar que sou un región del stgr
if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_("Solamente lo pueden ver las regiones del stgr"));
}

/////////// Consulta al backend ///////////////////
$url_backend = '/src/certificados/certificado_emitido_lista_datos';
$a_campos_backend = [
    'certificado' => $Qcertificado,
    'inicurs_ca_iso' => $inicurs_ca_iso,
    'fincurs_ca_iso' => $fincurs_ca_iso,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
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
$oTabla->setId_tabla('certificado_emitido_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('certificado');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');

$pdf_signed_urls = [];
foreach ($a_valores as $idx => $row) {
    if (!is_int($idx) || !is_array($row) || !isset($row['sel'])) {
        continue;
    }
    $id = (int)$row['sel'];
    if ($id <= 0) {
        continue;
    }
    $pdf_signed_urls[(string)$id] = SignedDownloadToken::urlCertificadoEmitido($id);
}

$txt_eliminar = _("¿Está seguro que quiere eliminar el certificado?");

$a_campos = [];
$a_campos['oHash'] = $oHash;
$a_campos['oHash1'] = $oHash1;
$a_campos['pdf_signed_urls_json'] = json_encode($pdf_signed_urls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
$a_campos['oTabla'] = $oTabla;
$a_campos['oPosicion'] = $oPosicion;
$a_campos['titulo'] = $titulo;
$a_campos['txt_eliminar'] = $txt_eliminar;

$oView = new ViewNewPhtml('frontend\certificados\controller');
$oView->renderizar('certificado_emitido_lista.phtml', $a_campos);
