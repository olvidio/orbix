<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use frontend\shared\security\HashFront;
use frontend\certificados\helpers\CertificadosPayload;


$Qguardar = PayloadCoercion::string($_GET['guardar'] ?? '');

$footer = '';
$certificado = '';
$Qid_item = 0;
$id_nom = 0;

ob_start();
include __DIR__ . '/certificado_emitido_imprimir_mpdf.php';
$content = ob_get_clean();
if ($content === false) {
    $content = '';
}

require_once OrbixRuntime::dirLibs() . '/vendor/autoload.php';

$nomArchivo = (isset($nom) && is_string($nom) && $nom !== '')
    ? frontend\shared\web\QuitarAcentos::convert($nom)
    : 'certificado';

$config = [
    'format' => 'A4',
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 6,
    'margin_bottom' => 8,
    'margin_header' => 0,
    'margin_footer' => 5,
];

try {
    $mpdf = new Mpdf($config);
    $mpdf->SetDisplayMode('fullpage');
} catch (MpdfException $e) {
    echo $e->getMessage();
    die();
}
$mpdf->list_indent_first_level = 0;
$mpdf->setHTMLFooter($footer);
try {
    $mpdf->WriteHTML($content);
} catch (MpdfException $e) {
    echo $e->getMessage();
    die();
}

if ($Qguardar !== '') {
    try {
        $pdf = $mpdf->Output("certificado($nomArchivo).pdf", 'S');
    } catch (MpdfException $e) {
        echo $e->getMessage();
        die();
    }
    $pdf_base64 = base64_encode($pdf);
    $certificado_base64 = base64_encode($certificado);

    require_once __DIR__ . '/certificado_emitido_aviso_html.php';
    $data = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_guardar_pdf', [
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'certificado' => $certificado_base64,
        'pdf' => $pdf_base64,
    ], false));
    if (!empty($data['error'])) {
        certificado_emitido_echo_aviso_y_salir(
            PostRequest::stripInternalCallProvenance(PayloadCoercion::string($data['error']))
        );
    }
}

try {
    $mpdf->Output("certificado($nomArchivo).pdf", 'D');
} catch (MpdfException $e) {
    echo $e->getMessage();
}
