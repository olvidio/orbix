<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use web\Hash;

$Qguardar = empty($_GET['guardar']) ? '' : $_GET['guardar'];

ob_start();
include(__DIR__ . '/certificado_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(ConfigGlobal::$dir_libs . '/vendor/autoload.php');

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

try {
    $mpdf = new Mpdf($config);
    $mpdf->SetDisplayMode('fullpage');
} catch (MpdfException $e) {
    $msg_err = $e->getMessage();
    exit($msg_err);
}
$mpdf->list_indent_first_level = 0;    // 1 or 0 - whether to indent the first level of a list
$mpdf->setHTMLFooter($footer);
try {
    $mpdf->WriteHTML($content);
} catch (MpdfException $e) {
    $msg_err = $e->getMessage();
    exit($msg_err);
}
try {
    $mpdf->Output("certificado($nom).pdf", 'D');
} catch (MpdfException $e) {
    $msg_err = $e->getMessage();
    exit($msg_err);
}

// grabar en la DB
if (!empty($Qguardar)) {
    try {
        $pdf = $mpdf->Output("certificado($nom).pdf", 'S');
    } catch (MpdfException $e) {
        $msg_err = $e->getMessage();
        exit($msg_err);
    }
    /////////// Ejecutar en el backend ///////////////////
    ///
    // Codificar el PDF en base64 para envío seguro
    $pdf_base64 = base64_encode($pdf);
    $url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
        . '/src/certificados/infrastructure/controllers/certificado_guardar_pdf.php'
    );

    $oHash = new Hash();
    $oHash->setUrl($url_lista_backend);
    $oHash->setCamposNo('pdf');
    $oHash->setArrayCamposHidden([
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'certificado' => $certificado,
        'pdf' => '',
    ]);

    $hash_params = $oHash->getArrayCampos();
    $fields=[];
    foreach ($hash_params as $key => $value) {
        $fields[] = ['name' => $key, 'contents' => $value];
    }
    // añado el pdf:
    /*
    $fields[] = [
        'name'     => 'pdf', // <-- MUY IMPORTANTE: El nombre del campo que espera el servidor
        'contents' => $pdf_base64,
        'headers'  => [
            'Content-Type' => 'application/pdf', // <-- Especifica el tipo MIME del archivo
                    ],
    ];
    */

    $data = PostRequest::getDataMultipart($url_lista_backend, $fields);

    if (!empty($data['error'])) {
        exit($data['error']);
    }
}

