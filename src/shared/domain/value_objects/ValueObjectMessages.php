<?php

namespace src\shared\domain\value_objects;

/**
 * Mensajes de error consistentes para value objects.
 */
final class ValueObjectMessages
{
    public static function withValueContext(string $message, string|int $value): string
    {
        if (is_string($value)) {
            return sprintf(
                '%s (length=%d, value=%s)',
                $message,
                mb_strlen($value),
                self::safeRepr($value)
            );
        }

        return sprintf('%s (value=%s)', $message, self::safeRepr((string) $value));
    }

    public static function safeRepr(string $value): string
    {
        $flags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_SUBSTITUTE')) {
            $flags |= JSON_INVALID_SUBSTITUTE;
        }
        $json = json_encode($value, $flags);
        if (!is_string($json)) {
            return '(valor no codificable)';
        }
        if (strlen($json) > 160) {
            return substr($json, 0, 157) . '...';
        }

        return $json;
    }
}
