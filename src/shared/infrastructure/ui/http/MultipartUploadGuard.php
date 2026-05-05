<?php

declare(strict_types=1);

namespace src\shared\infrastructure\ui\http;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Comprobaciones comunes para POST multipart: límites PHP (post_max_size / upload)
 * y errores {@see UPLOAD_ERR_*}.
 */
final class MultipartUploadGuard
{
    /**
     * Interpreta directivas php.ini tipo "8M", "512K", "1G".
     */
    public static function parseIniSizeToBytes(string $iniDirectiva): int
    {
        $raw = ini_get($iniDirectiva);
        if (is_numeric($raw)) {
            return (int) $raw;
        }
        $raw = trim((string) $raw);
        if ($raw === '') {
            return 0;
        }
        $metric = strtoupper(substr($raw, -1));
        $val = (int) $raw;

        return match ($metric) {
            'K' => $val * 1024,
            'M' => $val * 1048576,
            'G' => $val * 1073741824,
            default => $val,
        };
    }

    public static function isPostTooLarge(): bool
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return false;
        }
        $postMaxBytes = self::parseIniSizeToBytes('post_max_size');
        if ($postMaxBytes <= 0) {
            return false;
        }
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

        return $contentLength > $postMaxBytes;
    }

    public static function textPostMaxExceededPhp(): string
    {
        return _('El tamaño total de la petición supera el límite configurado en el servidor (post_max_size).');
    }

    /**
     * Termina la petición con JSON (success/mensaje) y HTTP 413.
     */
    public static function exitIfPostTooLargeJson(): void
    {
        if (!self::isPostTooLarge()) {
            return;
        }

        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(413);
        echo json_encode([
            'success' => false,
            'mensaje' => self::textPostMaxExceededPhp(),
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    public static function httpStatusForPhpUploadError(int $errorCode): int
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 413,
            default => 200,
        };
    }

    public static function messageForPhpUploadError(int $errorCode, string $fileName): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => self::textFileExceedsServerLimit(),
            UPLOAD_ERR_PARTIAL => sprintf(_('El archivo %s se subió solo en parte.'), $fileName),
            UPLOAD_ERR_NO_FILE => _('No se ha seleccionado ningún archivo.'),
            UPLOAD_ERR_NO_TMP_DIR => _('Error del servidor: falta el directorio temporal para subidas.'),
            UPLOAD_ERR_CANT_WRITE => sprintf(_('No se pudo guardar el archivo %s en disco.'), $fileName),
            UPLOAD_ERR_EXTENSION => _('La subida fue bloqueada por una extensión de PHP.'),
            default => sprintf(_('Error al subir el archivo %s.'), $fileName),
        };
    }

    private static function textFileExceedsServerLimit(): string
    {
        $maxBytes = UploadedFile::getMaxFilesize();
        $maxMb = $maxBytes > 0 ? round((float) $maxBytes / 1048576, 1) : 0.0;

        return sprintf(
            _('El archivo supera el tamaño máximo permitido por el servidor (aprox. %s MB).'),
            (string) $maxMb
        );
    }

    /**
     * @return array{name: string, tmp_name: string}
     */
    public static function requireUploadedFileOrExit(string $inputKey): array
    {
        $postMaxBytes = self::parseIniSizeToBytes('post_max_size');
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
            self::respondJsonTooLargeAndExit(self::textPostMaxExceededPhp());
        }

        $respondTooLarge = static function (string $mensaje): void {
            self::respondJsonTooLargeAndExit($mensaje);
        };

        if (!isset($_FILES[$inputKey])) {
            $isMultipart = isset($_SERVER['CONTENT_TYPE'])
                && str_contains((string) $_SERVER['CONTENT_TYPE'], 'multipart/form-data');
            if ($isMultipart && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
                $respondTooLarge(self::textPostMaxExceededPhp());
            }
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'mensaje' => _('No se ha recibido el archivo.'),
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        $fileName = (string) $_FILES[$inputKey]['name'];
        $uploadError = (int) ($_FILES[$inputKey]['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($uploadError !== UPLOAD_ERR_OK) {
            if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
                $respondTooLarge(self::messageForPhpUploadError($uploadError, $fileName));
            }
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'mensaje' => self::messageForPhpUploadError($uploadError, $fileName),
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        return [
            'name' => $fileName,
            'tmp_name' => (string) $_FILES[$inputKey]['tmp_name'],
        ];
    }

    private static function respondJsonTooLargeAndExit(string $mensaje): void
    {
        http_response_code(413);
        echo json_encode(['success' => false, 'mensaje' => $mensaje], JSON_THROW_ON_ERROR);
        exit;
    }
}
