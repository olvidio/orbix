<?php

namespace src\shared\infrastructure\ui\http;

/**
 * Resuelve la ruta «legacy» de una petición (`?r=/src/...` o la URL) al fichero que
 * public/index.php puede cargar con `require`.
 *
 * Solo admite controladores HTTP de un módulo:
 * `/src/<modulo>/infrastructure/ui/http/controllers/<nombre>.php`. La ruta se valida tal como
 * llega (sin `..`, sin otros directorios) y, además, el fichero real tiene que coincidir con ella:
 * un enlace simbólico o cualquier normalización que lleve a otro sitio se rechaza.
 */
final class RutaControladorSrc
{
    private const PATRON = '#^/src/[A-Za-z0-9_]+/infrastructure/ui/http/controllers/[A-Za-z0-9_]+\.php$#D';

    public static function resolver(string $raizProyecto, string $ruta): ?string
    {
        if (preg_match(self::PATRON, $ruta) !== 1) {
            return null;
        }
        $raiz = realpath($raizProyecto);
        if ($raiz === false) {
            return null;
        }
        $fichero = realpath($raiz . $ruta);
        if ($fichero === false || !is_file($fichero) || $fichero !== $raiz . $ruta) {
            return null;
        }

        return $fichero;
    }
}
