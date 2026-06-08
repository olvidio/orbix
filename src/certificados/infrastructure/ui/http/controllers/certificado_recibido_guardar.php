<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoRecibidoRepositoryInterface $certificadoRecibidoRepository */
$certificadoRecibidoRepository = DependencyResolver::get(CertificadoRecibidoRepositoryInterface::class);

$Qnuevo = input_int($_POST, 'nuevo');
$Qid_item = input_int($_POST, 'id_item');
$Qid_nom = input_int($_POST, 'id_nom');
$Qnom = input_string($_POST, 'nom');
$Qidioma = input_string($_POST, 'idioma');
$Qdestino = input_string($_POST, 'destino');
$Qcertificado = input_string($_POST, 'certificado');
$Qfirmado = input_string($_POST, 'firmado');
$Qf_certificado = input_string($_POST, 'f_certificado');
$Qf_recibido = input_string($_POST, 'f_recibido');
$Qcertificado_old = input_string($_POST, 'certificado_old');

$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
$oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);

$error_txt = '';

if (is_true($Qnuevo)) {
    $Qid_item = (int) $certificadoRecibidoRepository->getNewId_item();
    $oCertificadoRecibido = new CertificadoRecibido();
    $oCertificadoRecibido->setId_item($Qid_item);
} else {
    $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
    if ($oCertificadoRecibido === null) {
        ContestarJson::enviar(_('No se encuentra el certificado'), 'ok');
        return;
    }
}

$oCertificadoRecibido->setId_nom($Qid_nom);
if ($Qnom === '') {
    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if ($oPersona === null) {
        $error_txt .= "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ': line ' . __LINE__;
        ContestarJson::enviar($error_txt, 'ok');
        return;
    }
    $Qnom = $oPersona->getNombreApellidos();
}

$oCertificadoRecibido->setNom($Qnom);
$oCertificadoRecibido->setIdiomaVo($Qidioma);
$oCertificadoRecibido->setDestino($Qdestino);
$oCertificadoRecibido->setCertificado($Qcertificado);
$oCertificadoRecibido->setFirmado(is_true($Qfirmado));
$oCertificadoRecibido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificadoRecibido->setF_certificado($oF_certificado instanceof DateTimeLocal ? $oF_certificado : null);
if ($oF_recibido instanceof DateTimeLocal) {
    $oCertificadoRecibido->setF_recibido($oF_recibido);
}

if ($certificadoRecibidoRepository->Guardar($oCertificadoRecibido) === false) {
    $error_txt .= $certificadoRecibidoRepository->getErrorTxt();
}

if ($Qcertificado_old !== '') {
    $filename_sin_barra = str_replace('/', '_', $Qcertificado_old);
    $filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
    $filename_pdf = ServerConf::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
    if (is_file($filename_pdf)) {
        unlink($filename_pdf);
    }
}

$data = ['mensaje' => 'ok', 'item' => $Qid_item];
ContestarJson::enviar($error_txt, $data);
