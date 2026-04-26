<?php

namespace frontend\shared\web;

class QuitarAcentos
{

    /**
     * para nombre de archivo
     */
    public static function convert($source)
    {
        $target = self::normalize($source);
        $target = str_replace(['/', '.', ' '], '_', $target);
        $target = str_replace('ñ', 'n', $target);
        $target = str_replace('Ñ', 'N', $target);

        return preg_replace('/[^a-zA-Z0-9_.-]/', '', $target);
    }

    /**
     * para ordenar
     */
    public static function to_sort($source)
    {
        $target = self::normalize($source);
        $target = str_replace('ñ', 'nzz', $target);
        $target = str_replace('Ñ', 'Nzz', $target);

        return preg_replace('/[^a-zA-Z0-9_.-]/', '', $target);
    }

    /**
     * Pasos comunes: saneo de caracteres ilegales en mb_convert_encoding + vocales con tilde.
     */
    private static function normalize(string $source): string
    {
        // Evitar que mb_convert_encoding rellene de '?' los caracteres ilegales.
        $encoding = mb_detect_encoding($source, 'auto');
        $target = str_replace('?', '[question_mark]', $source);
        $target = mb_convert_encoding($target, 'UTF-8', $encoding);
        $target = str_replace('?', '', $target);
        $target = str_replace('[question_mark]', '?', $target);

        $target = preg_replace('/á|à|â|ã|ª/', 'a', $target);
        $target = preg_replace('/Á|À|Â|Ã/', 'A', $target);
        $target = preg_replace('/é|è|ê/', 'e', $target);
        $target = preg_replace('/É|È|Ê/', 'E', $target);
        $target = preg_replace('/í|ì|î/', 'i', $target);
        $target = preg_replace('/Í|Ì|Î/', 'I', $target);
        $target = preg_replace('/ó|ò|ô|õ|º/', 'o', $target);
        $target = preg_replace('/Ó|Ò|Ô|Õ/', 'O', $target);
        $target = preg_replace('/ú|ù|û/', 'u', $target);
        return preg_replace('/Ú|Ù|Û/', 'U', $target);
    }
}
