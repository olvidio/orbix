<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\entity\Certificado;
use certificados\domain\entity\CertificadoDl;
use certificados\domain\repositories\CertificadoDlRepository;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use web\DateTimeLocal;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qnuevo = (integer)filter_input(INPUT_POST, 'nuevo');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qnom = (string)filter_input(INPUT_POST, 'nom');
$Qidioma = (string)filter_input(INPUT_POST, 'idioma');
$Qdestino = (string)filter_input(INPUT_POST, 'destino');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$Qfirmado = (string)filter_input(INPUT_POST, 'firmado');
$Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');
$Qfecha = (string)filter_input(INPUT_POST, 'fecha');

$Qcertificado_old = (string)filter_input(INPUT_POST, 'certificado_old');
$local = (bool)filter_input(INPUT_POST, 'local');

/* convertir las fechas a DateTimeLocal */
$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
$oFecha = DateTimeLocal::createFromLocal($Qfecha);

$error_txt = '';

if ($local) {
    $certificadoRepository = new CertificadoDlRepository();
} else {
    $certificadoRepository = new CertificadoRepository();
}

if (is_true($Qnuevo)) {
    $Qid_item = $certificadoRepository->getNewId_item();
    if ($local) {
        $oCertificado = new CertificadoDl();
    } else {
        $oCertificado = new Certificado();
    }
    $oCertificado->setId_item($Qid_item);
} else {
    $oCertificado = $certificadoRepository->findById($Qid_item);
}
$oCertificado->setId_nom($Qid_nom);
if (empty($Qnom)) {
    $oPersona = Persona::NewPersona($Qid_nom);
    if (!is_object($oPersona)) {
        $msg_err = "<br>$oPersona con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
        exit($msg_err);
    }
    $Qnom = $oPersona->getNombreApellidos();
}
$oCertificado->setNom($Qnom);
$oCertificado->setIdioma($Qidioma);
$oCertificado->setDestino($Qdestino);
$oCertificado->setCertificado($Qcertificado);
if (is_true($Qfirmado)) {
    $firmado = TRUE;
} else {
    $firmado = FALSE;
}
$oCertificado->setFirmado($firmado);
$oCertificado->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificado->setF_certificado($oF_certificado);
if (!empty($oFecha)) {
    if ($local) {
        $oCertificado->setF_recibido($oFecha);
    } else {
        $oCertificado->setF_enviado($oFecha);
    }
}

if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
    $error_txt .= $certificadoRepository->getErrorTxt();
}
// borrar el pdf en log
if (!empty($Qcertificado_old)) {
    $filename_sin_barra = str_replace('/', '_', $Qcertificado_old);
    $filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
    $filename_pdf = ConfigGlobal::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
    if (is_file($filename_pdf)) {
        unlink($filename_pdf);
    }
}

if (empty($error_txt)) {
    $jsondata['success'] = TRUE;
    $jsondata['mensaje'] = 'ok';
    $jsondata['item'] = $Qid_item;
} else {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();