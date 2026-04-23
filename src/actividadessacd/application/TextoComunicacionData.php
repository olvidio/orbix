<?php

namespace src\actividadessacd\application;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;

/**
 * Devuelve el texto de comunicacion asociado a `{clave, idioma}`. Si no
 * existe, devuelve cadena vacia (mismo comportamiento que el legacy
 * `com_sacd_txt_ajax.php` rama `get_texto`).
 *
 * Sucesor de la rama `get_texto` del dispatcher legacy.
 */
final class TextoComunicacionData
{
    /**
     * @param array{clave?: string, idioma?: string} $input
     * @return array{texto: string}
     */
    public static function execute(array $input): array
    {
        $clave = (string)($input['clave'] ?? '');
        $idioma = self::normalizarIdioma((string)($input['idioma'] ?? ''));
        if ($clave === '' || $idioma === '') {
            return ['texto' => ''];
        }

        $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
        $cTextos = $ActividadSacdTextoRepository->getActividadSacdTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);
        if (!is_array($cTextos) || count($cTextos) === 0) {
            return ['texto' => ''];
        }
        return ['texto' => (string)$cTextos[0]->getTexto()];
    }

    /**
     * Acepta tanto `ca` como `ca_ES.UTF-8`, devolviendo siempre los 2
     * primeros caracteres (igual criterio que el legacy `substr(0, strpos '_')`,
     * pero tolerante a valores ya cortos).
     */
    public static function normalizarIdioma(string $idioma): string
    {
        if ($idioma === '') {
            return '';
        }
        $pos = strpos($idioma, '_');
        if ($pos === false) {
            return $idioma;
        }
        return substr($idioma, 0, $pos);
    }
}
