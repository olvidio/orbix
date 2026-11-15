<?php

use src\certificados\domain\CertificadoEmitidoUpload;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;


header('Content-Type: application/json; charset=UTF-8');

/** @var CertificadoEmitidoUpload $certificadoUpload */
$certificadoUpload = DependencyResolver::get(CertificadoEmitidoUpload::class);

// Solo con la cápsula del formulario (la misma que guarda el certificado): fija el certificado y la persona.
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'certificado_emitido_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$idItemCtx = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
$idNomCtx = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
$uploaded = MultipartUploadGuard::requireUploadedFileOrExit('certificado_pdf');
$tmpFilePath = $uploaded['tmp_name'];
$fileName = $uploaded['name'];

$error_txt = '';
$jsondata = ['success' => true];

if ($tmpFilePath === '') {
    $error_txt = sprintf(_("No se puede subir el archivo %s"), $fileName);
} else {
    $fp = fopen($tmpFilePath, 'rb');
    if ($fp === false) {
        $error_txt = sprintf(_("No se puede abrir el archivo %s"), $fileName);
    } else {
        $fileSize = filesize($tmpFilePath);
        $contenido_doc = $fileSize > 0 ? fread($fp, $fileSize) : '';
        fclose($fp);
        if ($contenido_doc === false) {
            $error_txt = sprintf(_("No se puede leer el archivo %s"), $fileName);
        } elseif (!\src\shared\domain\helpers\DocumentoPdf::esPdf($contenido_doc)) {
            // Se guarda y se descarga como PDF: no aceptar otros ficheros.
            $error_txt = sprintf(_("El archivo %s no es un PDF"), $fileName);
        } else {
            // `solo_pdf`: PDF firmado de un certificado que ya existe (el de la cápsula). Antes se leía como
            // entero y se pasaba a `isTrue()`, que solo entiende textos: nunca entraba aquí y creaba otro certificado.
            if (\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'solo_pdf') === 1 && $idItemCtx > 0) {
                $rta = $certificadoUpload->uploadTxtFirmado($idItemCtx, (string) $contenido_doc);
                if (!is_object($rta)) {
                    $error_txt = (string) $rta;
                }
            } else {
                $oF_certificado = DateTimeLocal::createFromLocal(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_certificado'));
                $oF_enviado = DateTimeLocal::createFromLocal(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_enviado'));
                $rta = $certificadoUpload->uploadNew(
                    $idNomCtx,
                    (string) $contenido_doc,
                    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'idioma'),
                    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'certificado'),
                    \src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'firmado')) ?? false,
                    $oF_certificado instanceof DateTimeLocal ? $oF_certificado : null,
                    $oF_enviado instanceof DateTimeLocal ? $oF_enviado : null,
                    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'destino') ?: null,
                );
                if (!is_object($rta)) {
                    $error_txt = (string) $rta;
                }
            }
        }
    }
}

if ($error_txt !== '') {
    $jsondata = ['success' => false, 'mensaje' => $error_txt];
}

echo json_encode($jsondata, JSON_THROW_ON_ERROR);
