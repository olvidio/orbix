<?php

namespace frontend\shared\web;

class QuitarAcentos
{
    /**
     * para nombre de archivo
     */
    public static function convert(string $source): string
    {
        $target = self::normalize($source);
        $target = str_replace(['/', '.', ' '], '_', $target);
        $target = str_replace('ñ', 'n', $target);
        $target = str_replace('Ñ', 'N', $target);

        return self::pregReplace('/[^a-zA-Z0-9_.-]/', '', $target);
    }

    /**
     * para ordenar
     */
    public static function to_sort(string $source): string
    {
        $target = self::normalize($source);
        $target = str_replace('ñ', 'nzz', $target);
        $target = str_replace('Ñ', 'Nzz', $target);

        return self::pregReplace('/[^a-zA-Z0-9_.-]/', '', $target);
    }

    private static function pregReplace(string $pattern, string $replacement, string $subject): string
    {
        $result = preg_replace($pattern, $replacement, $subject);

        return is_string($result) ? $result : '';
    }

    private static function detectEncoding(string $source): ?string
    {
        $encoding = mb_detect_encoding($source, 'auto');
        if (!is_string($encoding) || $encoding === '') {
            return null;
        }

        return $encoding;
    }

    /**
     * Pasos comunes: saneo de caracteres ilegales en mb_convert_encoding + vocales con tilde.
     */
    private static function normalize(string $source): string
    {
        // Evitar que mb_convert_encoding rellene de '?' los caracteres ilegales.
        $encoding = self::detectEncoding($source);
        $target = str_replace('?', '[question_mark]', $source);
        $converted = mb_convert_encoding($target, 'UTF-8', $encoding);
        $target = is_string($converted) ? $converted : $target;
        $target = str_replace('?', '', $target);
        $target = str_replace('[question_mark]', '?', $target);

        $target = self::pregReplace('/á|à|â|ã|ª/', 'a', $target);
        $target = self::pregReplace('/Á|À|Â|Ã/', 'A', $target);
        $target = self::pregReplace('/é|è|ê/', 'e', $target);
        $target = self::pregReplace('/É|È|Ê/', 'E', $target);
        $target = self::pregReplace('/í|ì|î/', 'i', $target);
        $target = self::pregReplace('/Í|Ì|Î/', 'I', $target);
        $target = self::pregReplace('/ó|ò|ô|õ|º/', 'o', $target);
        $target = self::pregReplace('/Ó|Ò|Ô|Õ/', 'O', $target);
        $target = self::pregReplace('/ú|ù|û/', 'u', $target);

        return self::pregReplace('/Ú|Ù|Û/', 'U', $target);
    }
}
