<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ServerConf;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoRecibidoRepository;
use src\certificados\domain\entity\CertificadoRecibido;
use web\ContestarJson;
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
$Qf_enviado = (string)filter_input(INPUT_POST, 'f_enviado');
$Qf_recibido = (string)filter_input(INPUT_POST, 'f_recibido');

$Qcertificado_old = (string)filter_input(INPUT_POST, 'certificado_old');

/* convertir las fechas a DateTimeLocal */
$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
$oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);
$oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);

$error_txt = '';

$certificadoRecibidoRepository = new CertificadoRecibidoRepository();

if (is_true($Qnuevo)) {
    $Qid_item = $certificadoRecibidoRepository->getNewId_item();
    $oCertificadoRecibido = new CertificadoRecibido();
    $oCertificadoRecibido->setId_item($Qid_item);
} else {
    $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
}
$oCertificadoRecibido->setId_nom($Qid_nom);
if (empty($Qnom)) {
    $oPersona = Persona::NewPersona($Qid_nom);
    if (!is_object($oPersona)) {
        $error_txt .= "<br>$oPersona con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
        ContestarJson::enviar($error_txt, 'ok');
        exit();
    }
    $Qnom = $oPersona->getNombreApellidos();
}

$oCertificadoRecibido->setNom($Qnom);
$oCertificadoRecibido->setIdioma($Qidioma);
$oCertificadoRecibido->setDestino($Qdestino);
$oCertificadoRecibido->setCertificado($Qcertificado);
if (is_true($Qfirmado)) {
    $firmado = TRUE;
} else {
    $firmado = FALSE;
}
$oCertificadoRecibido->setFirmado($firmado);
$oCertificadoRecibido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificadoRecibido->setF_certificado($oF_certificado);

if (!empty($oF_recibido)) {
    $oCertificadoRecibido->setF_recibido($oF_recibido);
}

if ($certificadoRecibidoRepository->Guardar($oCertificadoRecibido) === FALSE) {
    $error_txt .= $certificadoRecibidoRepository->getErrorTxt();
}
// borrar el pdf en log
if (!empty($Qcertificado_old)) {
    $filename_sin_barra = str_replace('/', '_', $Qcertificado_old);
    $filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
    $filename_pdf = ServerConf::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
    if (is_file($filename_pdf)) {
        unlink($filename_pdf);
    }
}

$data['mensaje'] = 'ok';
$data['item'] = $Qid_item;

ContestarJson::enviar($error_txt, $data);