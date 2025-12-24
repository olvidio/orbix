<?php

use core\ConfigGlobal;
use core\ServerConf;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\personas\domain\entity\Persona;
use web\ContestarJson;
use web\DateTimeLocal;
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
$Qf_enviado = (string)filter_input(INPUT_POST, 'f_enviado');

$Qcertificado_old = (string)filter_input(INPUT_POST, 'certificado_old');

/* convertir las fechas a DateTimeLocal */
$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
$oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);
$oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);

$error_txt = '';

$certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);

if (is_true($Qnuevo)) {
    $Qid_item = $certificadoEmitidoRepository->getNewId_item();
    $oCertificadoEmitido = new CertificadoEmitido();
    $oCertificadoEmitido->setId_item($Qid_item);
} else {
    $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
}
$oCertificadoEmitido->setId_nom($Qid_nom);
if (empty($Qnom)) {
    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if (!is_object($oPersona)) {
        $error_txt .= "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
        ContestarJson::enviar($error_txt, 'ok');
        exit();
    }
    $Qnom = $oPersona->getNombreApellidos();
}

$oCertificadoEmitido->setNom($Qnom);
$oCertificadoEmitido->setIdioma($Qidioma);
$oCertificadoEmitido->setDestino($Qdestino);
$oCertificadoEmitido->setCertificado($Qcertificado);
if (is_true($Qfirmado)) {
    $firmado = TRUE;
} else {
    $firmado = false;
}
$oCertificadoEmitido->setFirmado($firmado);
$oCertificadoEmitido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificadoEmitido->setF_certificado($oF_certificado);
if (!empty($oF_enviado)) {
    $oCertificadoEmitido->setF_enviado($oF_enviado);
}

if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
    $error_txt .= $certificadoEmitidoRepository->getErrorTxt();
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