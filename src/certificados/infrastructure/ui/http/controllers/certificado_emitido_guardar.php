<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

use src\certificados\application\CertificadoEmitidoGuardarMessages;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\LocaleCode;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository */
$certificadoEmitidoRepository = DependencyResolver::get(CertificadoEmitidoRepositoryInterface::class);

$Qnuevo = input_int($_POST, 'nuevo');
$Qid_item = input_int($_POST, 'id_item');
$Qid_nom = input_int($_POST, 'id_nom');
$Qnom = input_string($_POST, 'nom');
$Qidioma = input_string($_POST, 'idioma');
$Qdestino = input_string($_POST, 'destino');
$Qcertificado = input_string($_POST, 'certificado');
$Qfirmado = input_string($_POST, 'firmado');
$Qf_certificado = input_string($_POST, 'f_certificado');
$Qf_enviado = input_string($_POST, 'f_enviado');
$Qcertificado_old = input_string($_POST, 'certificado_old');

$oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
$oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);

$error_txt = '';

if (is_true($Qnuevo)) {
    $Qid_item = (int) $certificadoEmitidoRepository->getNewId_item();
    $oCertificadoEmitido = new CertificadoEmitido();
    $oCertificadoEmitido->setId_item($Qid_item);
} else {
    $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
    if ($oCertificadoEmitido === null) {
        ContestarJson::enviar(_('No se encuentra el certificado'), 'ok');
        return;
    }
}

$oCertificadoEmitido->setId_nom($Qid_nom);
if ($Qnom === '') {
    $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
    if ($oPersona === null) {
        $error_txt .= "<br>No encuentro a nadie con id_nom: $Qid_nom en  " . __FILE__ . ': line ' . __LINE__;
        ContestarJson::enviar($error_txt, 'ok');
        return;
    }
    $Qnom = $oPersona->getNombreApellidos();
}

$oCertificadoEmitido->setNom($Qnom);
$oCertificadoEmitido->setIdiomaVo(LocaleCode::fromNullableString($Qidioma));
$oCertificadoEmitido->setDestino($Qdestino);
$oCertificadoEmitido->setCertificado($Qcertificado);
$oCertificadoEmitido->setFirmado(is_true($Qfirmado) ?? false);
$oCertificadoEmitido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
$oCertificadoEmitido->setF_certificado($oF_certificado instanceof DateTimeLocal ? $oF_certificado : null);
if ($oF_enviado instanceof DateTimeLocal) {
    $oCertificadoEmitido->setF_enviado($oF_enviado);
}

$guardado = false;
try {
    $guardado = $certificadoEmitidoRepository->Guardar($oCertificadoEmitido);
} catch (\Throwable $e) {
    $error_txt .= CertificadoEmitidoGuardarMessages::fromThrowable($e);
}
if ($guardado === false && $error_txt === '') {
    $error_txt .= CertificadoEmitidoGuardarMessages::fromDatabaseError(
        $certificadoEmitidoRepository->getErrorTxt(),
    );
}

if ($guardado && $Qcertificado_old !== '') {
    $filename_sin_barra = str_replace('/', '_', $Qcertificado_old);
    $filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
    $filename_pdf = ServerConf::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
    if (is_file($filename_pdf)) {
        unlink($filename_pdf);
    }
}

$data = ['mensaje' => 'ok', 'item' => $Qid_item];
ContestarJson::enviar($error_txt, $data);
