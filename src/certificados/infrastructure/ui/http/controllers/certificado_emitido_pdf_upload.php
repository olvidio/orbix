<?php
/**
 * Subida AJAX del PDF (bootstrap-fileinput / FormData multipart).
 */

use src\certificados\domain\CertificadoEmitidoUpload;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;

use function frontend\shared\helpers\is_true;

require_once 'frontend/shared/global_header_front.inc';

header('Content-Type: application/json; charset=UTF-8');

$uploaded = MultipartUploadGuard::requireUploadedFileOrExit('certificado_pdf');
$tmpFilePath = $uploaded['tmp_name'];
$fileName = $uploaded['name'];

$error_txt = '';

if ($tmpFilePath !== '') {
    $fp = fopen($tmpFilePath, 'rb');
    if ($fp === false) {
        $error_txt = sprintf(_("No se puede abrir el archivo %s"), $fileName);
    } else {
        $contenido_doc = fread($fp, filesize($tmpFilePath));
        fclose($fp);
        if ($contenido_doc === false) {
            $error_txt = sprintf(_("No se puede leer el archivo %s"), $fileName);
        } else {
            $certificadoUpload = new CertificadoEmitidoUpload();

            $Qsolo_pdf = (integer) filter_input(INPUT_POST, 'solo_pdf');
            if (is_true($Qsolo_pdf)) {
                $Qid_item = (integer) filter_input(INPUT_POST, 'id_item');
                $rta = $certificadoUpload->uploadTxtFirmado($Qid_item, $contenido_doc);
                if (!is_object($rta)) {
                    $error_txt = $rta;
                }
            } else {
                $Qid_nom = (integer) filter_input(INPUT_POST, 'id_nom');
                $Qcertificado = (string) filter_input(INPUT_POST, 'certificado');
                $Qfirmado = (string) filter_input(INPUT_POST, 'firmado');
                $Qf_certificado = (string) filter_input(INPUT_POST, 'f_certificado');
                $Qidioma = (string) filter_input(INPUT_POST, 'idioma');
                $Qdestino = (string) filter_input(INPUT_POST, 'destino');
                $Qf_enviado = (string) filter_input(INPUT_POST, 'f_enviado');
                /* convertir las fechas a DateTimeLocal */
                $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
                $oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);

                if (is_true($Qfirmado)) {
                    $firmado = true;
                } else {
                    $firmado = false;
                }

                $rta = $certificadoUpload->uploadNew($Qid_nom, $contenido_doc, $Qidioma, $Qcertificado, $firmado, $oF_certificado, $oF_enviado, $Qdestino);

                if (!is_object($rta)) {
                    $error_txt = $rta;
                }
            }
        }
    }
} else {
    $error_txt = sprintf(_("No se puede subir el archivo %s"), $fileName);
}

if (!empty($error_txt)) {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = true;
}

echo json_encode($jsondata, JSON_THROW_ON_ERROR);
exit;
