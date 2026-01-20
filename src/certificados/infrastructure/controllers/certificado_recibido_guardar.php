<?php

use core\ConfigGlobal;
use core\ServerConf;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use web\ContestarJson;
use function core\is_true;

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

$certificadoRecibidoRepository = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);

if (is_true($Qnuevo)) {
    $Qid_item = $certificadoRecibidoRepository->getNewId_item();
    $oCertificadoRecibido = new CertificadoRecibido();
    $oCertificadoRecibido->setId_item($Qid_item);
} else {
    $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
}
$oCertificadoRecibido->setId_nom($Qid_nom);
if (empty($Qnom)) {
    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if (!is_object($oPersona)) {
        $error_txt .= "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
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
    $firmado = false;
}
$oCertificadoRecibido->setFirmado($firmado);
$oCertificadoRecibido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificadoRecibido->setF_certificado($oF_certificado);

if (!empty($oF_recibido)) {
    $oCertificadoRecibido->setF_recibido($oF_recibido);
}

if ($certificadoRecibidoRepository->Guardar($oCertificadoRecibido) === false) {
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