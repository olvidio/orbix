<?php

use src\certificados\domain\CertificadoRecibidoUpload;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use src\shared\domain\helpers\FuncTablasSupport;


header('Content-Type: application/json; charset=UTF-8');

/** @var CertificadoRecibidoUpload $certificadoUpload */
$certificadoUpload = DependencyResolver::get(CertificadoRecibidoUpload::class);

$uploaded = MultipartUploadGuard::requireUploadedFileOrExit('certificado_pdf');
$tmpFilePath = $uploaded['tmp_name'];
$fileName = $uploaded['name'];

$error_txt = '';
$jsondata = ['success' => true];

if ($tmpFilePath === '') {
    $error_txt = sprintf(_('No se puede subir el archivo %s'), $fileName);
} else {
    $fp = fopen($tmpFilePath, 'rb');
    if ($fp === false) {
        $error_txt = sprintf(_('No se puede abrir el archivo %s'), $fileName);
    } else {
        $fileSize = filesize($tmpFilePath);
        $contenido_doc = $fileSize > 0 ? fread($fp, $fileSize) : '';
        fclose($fp);
        if ($contenido_doc === false) {
            $error_txt = sprintf(_('No se puede leer el archivo %s'), $fileName);
        } else {
            $oF_certificado = DateTimeLocal::createFromLocal(FuncTablasSupport::inputString($_POST, 'f_certificado'));
            $oF_recibido = DateTimeLocal::createFromLocal(FuncTablasSupport::inputString($_POST, 'f_recibido'));
            $oCertificadoRecibido = $certificadoUpload->uploadNew(
                FuncTablasSupport::inputInt($_POST, 'id_item'),
                FuncTablasSupport::inputInt($_POST, 'id_nom'),
                (string) $contenido_doc,
                FuncTablasSupport::inputString($_POST, 'idioma'),
                FuncTablasSupport::inputString($_POST, 'certificado'),
                FuncTablasSupport::isTrue(FuncTablasSupport::inputString($_POST, 'firmado')) ?? false,
                $oF_certificado instanceof DateTimeLocal ? $oF_certificado : null,
                $oF_recibido instanceof DateTimeLocal ? $oF_recibido : null,
                FuncTablasSupport::inputString($_POST, 'destino') ?: null,
            );
            if (!is_object($oCertificadoRecibido)) {
                $error_txt .= (string) $oCertificadoRecibido;
            }
        }
    }
}

if ($error_txt !== '') {
    $jsondata = ['success' => false, 'mensaje' => $error_txt];
}

echo json_encode($jsondata, JSON_THROW_ON_ERROR);
exit;
