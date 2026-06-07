<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Fachada frontend para comprobaciones multipart sin `use src\...` en controladores.
 */
final class MultipartUploadHelper
{
    public static function isPostTooLarge(): bool
    {
        return \src\shared\infrastructure\ui\http\MultipartUploadGuard::isPostTooLarge();
    }

    public static function textPostMaxExceededPhp(): string
    {
        return \src\shared\infrastructure\ui\http\MultipartUploadGuard::textPostMaxExceededPhp();
    }

    public static function messageForPhpUploadError(int $errorCode, string $fileName): string
    {
        return \src\shared\infrastructure\ui\http\MultipartUploadGuard::messageForPhpUploadError($errorCode, $fileName);
    }
}
