<?php
// get the HTML
/*
$_POST = $_GET;

$id_item = (string)filter_input(INPUT_POST, 'id_item');
*/

use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;

$Qguardar = empty($_GET['guardar']) ? '' : $_GET['guardar'];

ob_start();
include(__DIR__ . '/certificado_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(core\ConfigGlobal::$dir_libs . '/vendor/autoload.php');

//echo "$content";
//exit();
// quitar los acentos , ñ etc. del nombre
$nom = web\QuitarAcentos::convert($nom);

$config = [
    'format' => 'A4',
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 6,
    'margin_bottom' => 8,
    'margin_header' => 0,
    'margin_footer' => 5,
];
$mpdf = new \Mpdf\Mpdf($config);
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;    // 1 or 0 - whether to indent the first level of a list
$mpdf->setHTMLFooter($footer);
$mpdf->WriteHTML($content);
$mpdf->Output("certificado($nom).pdf", 'D');

// grabar en la DB
if (!empty($Qguardar)) {
    $pdf = $mpdf->Output("certificado($nom).pdf", 'S'); // as string
    $oCertificado->setDocumento($pdf);
    $CertificadoRepository->Guardar($oCertificado);
    // también hay que guardarlo en las notas afectadas
    $oF_certificado = $oCertificado->getF_certificado();
    // Se supone que si accedo a esta página es porque soy una región del stgr.
    $esquema_region_stgr = $_SESSION['session_auth']['esquema'];
    $gesPersonaNotaOtraRegionStgr = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
    $gesPersonaNotaOtraRegionStgr->addCertificado($id_nom, $certificado, $oF_certificado);
}

