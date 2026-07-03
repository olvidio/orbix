<?php

namespace src\notas\application;

use src\shared\domain\helpers\FuncTablasSupport;

use src\notas\domain\contracts\ActaRepositoryInterface;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;

/**
 * Sube (persiste) el contenido binario de un PDF firmado en el campo
 * `pdf` del acta identificada por `acta_num`.
 */
final class ActaPdfSubir
{

    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $input
     * @param array<string, array<string, mixed>> $files
     * @return array{error: string, http_status: int}
     */
    public function execute(array $input, array $files): array
    {
        $acta = FuncTablasSupport::inputString($input, 'acta_num');
        if ($acta === '') {
            return ['error' => _('No se encuentra el acta'), 'http_status' => 200];
        }

        $fileKey = 'acta_pdf';
        if (!isset($files[$fileKey])) {
            return ['error' => '', 'http_status' => 200];
        }
        /** @var array<string, mixed> $file */
        $file = $files[$fileKey];

        $uploadError = isset($file['error']) && is_int($file['error']) ? $file['error'] : UPLOAD_ERR_OK;
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            return ['error' => '', 'http_status' => 200];
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            $fileName = isset($file['name']) && is_string($file['name']) ? $file['name'] : '';

            return [
                'error' => MultipartUploadGuard::messageForPhpUploadError($uploadError, $fileName),
                'http_status' => MultipartUploadGuard::httpStatusForPhpUploadError($uploadError),
            ];
        }

        $tmpFilePath = $file['tmp_name'] ?? null;
        $fileName = isset($file['name']) && is_string($file['name']) ? $file['name'] : '';
        if (!is_string($tmpFilePath) || $tmpFilePath === '') {
            return [
                'error' => sprintf(_("No se puede subir el archivo %s"), $fileName),
                'http_status' => 200,
            ];
        }

        $fileSize = filesize($tmpFilePath);
        if ($fileSize === false || $fileSize <= 0) {
            return [
                'error' => sprintf(_("No se puede leer el archivo %s"), $fileName),
                'http_status' => 200,
            ];
        }

        $fp = fopen($tmpFilePath, 'rb');
        if ($fp === false) {
            return [
                'error' => sprintf(_("No se puede abrir el archivo %s"), $fileName),
                'http_status' => 200,
            ];
        }
        $contenido_doc = fread($fp, $fileSize);
        fclose($fp);
        if (!is_string($contenido_doc)) {
            return [
                'error' => sprintf(_("No se puede leer el archivo %s"), $fileName),
                'http_status' => 200,
            ];
        }

        $ActaRepository = $this->actaRepository;
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            return ['error' => _('No se encuentra el acta'), 'http_status' => 200];
        }
        $oActa->setPdf($contenido_doc);
        if ($ActaRepository->Guardar($oActa) === false) {
            return ['error' => $ActaRepository->getErrorTxt(), 'http_status' => 200];
        }

        return ['error' => '', 'http_status' => 200];
    }
}
