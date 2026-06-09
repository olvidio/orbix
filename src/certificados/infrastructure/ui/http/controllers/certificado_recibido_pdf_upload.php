<?php

use function frontend\shared\helpers\is_true;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\certificados\domain\CertificadoRecibidoUpload;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;


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
            $oF_certificado = DateTimeLocal::createFromLocal(input_string($_POST, 'f_certificado'));
            $oF_recibido = DateTimeLocal::createFromLocal(input_string($_POST, 'f_recibido'));
            $oCertificadoRecibido = $certificadoUpload->uploadNew(
                input_int($_POST, 'id_item'),
                input_int($_POST, 'id_nom'),
                (string) $contenido_doc,
                input_string($_POST, 'idioma'),
                input_string($_POST, 'certificado'),
                is_true(input_string($_POST, 'firmado')) ?? false,
                $oF_certificado instanceof DateTimeLocal ? $oF_certificado : null,
                $oF_recibido instanceof DateTimeLocal ? $oF_recibido : null,
                input_string($_POST, 'destino') ?: null,
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
