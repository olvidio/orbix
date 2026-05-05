<?php

namespace src\notas\application;

use src\notas\domain\contracts\ActaRepositoryInterface;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;

/**
 * Sube (persiste) el contenido binario de un PDF firmado en el campo
 * `pdf` del acta identificada por `acta_num`.
 *
 * El contenido se lee del array `$files` que tiene la misma forma que
 * `$_FILES` (clave `acta_pdf` generada por bootstrap-fileinput en
 * `acta_ver.phtml`).
 */
final class ActaPdfSubir
{
    /**
     * @return array{error: string, http_status: int} `http_status` solo aplica si `error` no está vacío
     */
    public static function execute(array $input, array $files): array
    {
        $acta = (string) ($input['acta_num'] ?? '');
        if (empty($acta)) {
            return ['error' => _('No se encuentra el acta'), 'http_status' => 200];
        }

        $fileKey = 'acta_pdf';
        if (empty($files[$fileKey])) {
            /* bootstrap-fileinput puede llamar sin fichero en algunos flujos */
            return ['error' => '', 'http_status' => 200];
        }

        $uploadError = (int) ($files[$fileKey]['error'] ?? UPLOAD_ERR_OK);
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            return ['error' => '', 'http_status' => 200];
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            $fileName = (string) ($files[$fileKey]['name'] ?? '');

            return [
                'error' => MultipartUploadGuard::messageForPhpUploadError($uploadError, $fileName),
                'http_status' => MultipartUploadGuard::httpStatusForPhpUploadError($uploadError),
            ];
        }

        $tmpFilePath = $files[$fileKey]['tmp_name'] ?? '';
        $fileName = $files[$fileKey]['name'] ?? '';
        if (empty($tmpFilePath)) {
            return [
                'error' => sprintf(_("No se puede subir el archivo %s"), $fileName),
                'http_status' => 200,
            ];
        }

        $fp = fopen($tmpFilePath, 'rb');
        $contenido_doc = fread($fp, (int) filesize($tmpFilePath));
        fclose($fp);

        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            return ['error' => _('No se encuentra el acta'), 'http_status' => 200];
        }
        $oActa->setPdf($contenido_doc);
        if ($ActaRepository->Guardar($oActa) === false) {
            return ['error' => (string) $oActa->getErrorTxt(), 'http_status' => 200];
        }

        return ['error' => '', 'http_status' => 200];
    }
}
