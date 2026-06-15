<?php
/**
 * Esta página muestra una tabla con los certificados.
 *
 * @package    delegacion
 * @subpackage    estudios
 */

use frontend\shared\config\OrbixRuntime;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use function frontend\shared\helpers\curso_est;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_sel = '';
$Qscroll_id = '';
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = tessera_imprimir_string($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = tessera_imprimir_string($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
}
list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(($aGoBack ?? list_nav_build_return_parametros_from_post()), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));


$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');

$titulo = $Qtitulo;
$mes = date('m');
$oConfig = certificados_o_config();
$fin_m = $oConfig !== null ? $oConfig->getMesFinStgr() : 12;
if ($mes > $fin_m) {
    $any = (int)date('Y') + 1;
} else {
    $any = (int)date('Y');
}
$oInicurs_ca = curso_est('inicio', $any);
$oFincurs_ca = curso_est('fin', $any);
$inicurs_ca_iso = $oInicurs_ca->getIso();
$fincurs_ca_iso = $oFincurs_ca->getIso();
$inicurs_ca_local = $oInicurs_ca->getFromLocal();
$fincurs_ca_local = $oFincurs_ca->getFromLocal();
$titulo = ucfirst(sprintf(_('lista de certificados emitidos entre %s y %s y no enviados'), $inicurs_ca_local, $fincurs_ca_local));

$aGoBack = [
    'titulo' => $Qtitulo,
    'certificado' => $Qcertificado,
];
$oPosicion->setParametros($aGoBack, 1);

if (!(OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r')) {
    exit(_('Solamente lo pueden ver las regiones del stgr'));
}

$data = certificados_post_data(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_lista_datos', [
    'certificado' => $Qcertificado,
    'inicurs_ca_iso' => $inicurs_ca_iso,
    'fincurs_ca_iso' => $fincurs_ca_iso,
]));
$tabla = certificados_emitido_lista_tabla_from_payload($data);
$a_valores = $tabla['valores'];

if ($Qid_sel !== '') {
    $a_valores['select'] = $Qid_sel;
}
if ($Qscroll_id !== '') {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('certificado_emitido_lista');
$oTabla->setCabeceras($tabla['cabeceras']);
$oTabla->setBotones($tabla['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('certificado');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh!id_sel');

$pdf_signed_urls = [];
foreach ($a_valores as $idx => $row) {
    if (!is_int($idx) || !is_array($row) || !isset($row['sel'])) {
        continue;
    }
    $id = tessera_imprimir_int($row['sel']);
    if ($id <= 0) {
        continue;
    }
    $pdf_signed_urls[(string) $id] = SignedDownloadToken::urlCertificadoEmitido($id);
}

$txt_eliminar = _('¿Está seguro que quiere eliminar el certificado?');

$a_campos = [
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'pdf_signed_urls_json' => json_encode($pdf_signed_urls, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
    'oTabla' => $oTabla,
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewPhtml('frontend\certificados\controller');
$oView->renderizar('certificado_emitido_lista.phtml', $a_campos);
