<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use web\DateTimeLocal;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado_num');
$Qcopia = (string)filter_input(INPUT_POST, 'copia');
$Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');

$Qcertificado_old = (string)filter_input(INPUT_POST, 'certificado_old');

/* convertir las fechas a DateTimeLocal */
$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);

$error_txt = '';

$certificadoRepository = new CertificadoRepository();
$oCertificado = $certificadoRepository->findById($Qid_item);
//$oCertificado->setId_nom($Qid_nom);
$oCertificado->setCertificado($Qcertificado);
if (is_true($Qcopia)) {
    $copia = TRUE;
} else {
    $copia = FALSE;
}
$oCertificado->setCopia($copia);
$oCertificado->setPropio(FALSE);
$oCertificado->setF_certificado($oF_certificado);

if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
    $error_txt .= $certificadoRepository->getErrorTxt();
}
// borrar el pdf en log
$filename_sin_barra = str_replace('/', '_', $Qcertificado_old);
$filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
$filename_pdf = ConfigGlobal::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
unlink($filename_pdf);

if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();