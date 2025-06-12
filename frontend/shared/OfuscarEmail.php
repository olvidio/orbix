<?php

namespace frontend\shared;

class OfuscarEmail
{

    /**
     * Ofusca una dirección de correo electrónico, ocultando parte del nombre de usuario y parte del dominio.
     *
     * @param string $email La dirección de correo electrónico a ofuscar.
     * @param int $showCharsUser El número de caracteres a mostrar al principio del nombre de usuario. Por defecto 3.
     * @param int $showCharsDomain El número de caracteres a mostrar al principio del nombre del dominio (antes del TLD). Por defecto 1.
     * @param string $maskChar El carácter a usar para la ofuscación (por defecto '*').
     * @return string La dirección de correo electrónico ofuscada, o la original si no es un formato válido.
     */
    public static function ofuscarEmailParcial($email, $showCharsUser = 3, $showCharsDomain = 1, $maskChar = '*')
    {
        // Validar si el email tiene un formato básico válido con '@'
        if (!is_string($email) || !str_contains($email, '@')) {
            return $email; // Retorna el email original si no tiene un formato esperado
        }

        $partes = explode('@', $email);
        $nombreUsuario = $partes[0];
        $dominioCompleto = $partes[1];

        // --- Ofuscar el nombre de usuario ---
        if (mb_strlen($nombreUsuario) <= $showCharsUser) {
            // Si el nombre de usuario es igual o más corto que los caracteres a mostrar,
            // muestra el primer carácter y el resto lo ofusca (para no revelar todo).
            $ofuscadoUsuario = mb_substr($nombreUsuario, 0, 1) . str_repeat($maskChar, mb_strlen($nombreUsuario) - 1);
        } else {
            $ofuscadoUsuario = mb_substr($nombreUsuario, 0, $showCharsUser) . str_repeat($maskChar, mb_strlen($nombreUsuario) - $showCharsUser);
        }

        // --- Ofuscar el dominio (excluyendo el TLD) ---
        $partesDominio = explode('.', $dominioCompleto);
        $nombreDominio = $partesDominio[0];
        $tld = implode('.', array_slice($partesDominio, 1)); // Reconstruir el TLD (.com, .es, .co.uk, etc.)

        if (mb_strlen($nombreDominio) <= $showCharsDomain) {
            // Si el nombre del dominio es igual o más corto que los caracteres a mostrar,
            // muestra el primer carácter y el resto lo ofusca.
            $ofuscadoDominio = mb_substr($nombreDominio, 0, 1) . str_repeat($maskChar, mb_strlen($nombreDominio) - 1);
        } else {
            $ofuscadoDominio = mb_substr($nombreDominio, 0, $showCharsDomain) . str_repeat($maskChar, mb_strlen($nombreDominio) - $showCharsDomain);
        }

        // Unir las partes ofuscadas
        return $ofuscadoUsuario . '@' . $ofuscadoDominio . '.' . $tld;
    }
}