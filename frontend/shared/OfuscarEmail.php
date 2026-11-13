<?php

namespace frontend\shared;

/**
 * Compatibilidad: la implementación está en {@see \src\shared\domain\helpers\OfuscarEmail}.
 */
class OfuscarEmail
{
    public static function ofuscarEmailParcial(string $email, int $showCharsUser = 3, int $showCharsDomain = 1, string $maskChar = '*'): string
    {
        return \src\shared\domain\helpers\OfuscarEmail::ofuscarEmailParcial($email, $showCharsUser, $showCharsDomain, $maskChar);
    }
}
