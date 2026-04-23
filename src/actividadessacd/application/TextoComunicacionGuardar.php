<?php

namespace src\actividadessacd\application;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;

/**
 * Guarda/actualiza/elimina el texto de comunicacion de `{clave, idioma}`.
 *
 * Reglas (mismas que el legacy `com_sacd_txt_ajax.php` rama `update`):
 *  - Si ya existe la fila y `texto === ''` → se elimina.
 *  - Si ya existe y `texto !== ''` → se actualiza.
 *  - Si no existe → se crea.
 */
final class TextoComunicacionGuardar
{
    /**
     * @param array{clave?: string, idioma?: string, texto?: string} $input
     */
    public static function execute(array $input): string
    {
        $clave = (string)($input['clave'] ?? '');
        $idioma = TextoComunicacionData::normalizarIdioma((string)($input['idioma'] ?? ''));
        $texto = (string)($input['texto'] ?? '');

        if ($clave === '' || $idioma === '') {
            return _("faltan parametros clave / idioma");
        }

        $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
        $cTextos = $ActividadSacdTextoRepository->getActividadSacdTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);
        $existe = is_array($cTextos) && count($cTextos) > 0;

        if ($existe) {
            $oTexto = $cTextos[0];
            if ($texto === '') {
                if ($ActividadSacdTextoRepository->Eliminar($oTexto) === false) {
                    return _("hay un error, no se ha eliminado el texto");
                }
                return '';
            }
            $oTexto->setTexto($texto);
            if ($ActividadSacdTextoRepository->Guardar($oTexto) === false) {
                return _("hay un error, no se ha guardado el texto");
            }
            return '';
        }

        // no existe: solo creo si hay algo que guardar
        if ($texto === '') {
            return '';
        }
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item($ActividadSacdTextoRepository->getNewId());
        $oTexto->setClave($clave);
        $oTexto->setIdioma($idioma);
        $oTexto->setTexto($texto);
        if ($ActividadSacdTextoRepository->Guardar($oTexto) === false) {
            return _("hay un error, no se ha guardado el texto");
        }
        return '';
    }
}
