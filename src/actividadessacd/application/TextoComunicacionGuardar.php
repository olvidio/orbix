<?php

namespace src\actividadessacd\application;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use function src\shared\domain\helpers\input_string;

/**
 * Guarda/actualiza/elimina el texto de comunicacion de `{clave, idioma}`.
 */
final class TextoComunicacionGuardar
{
    public function __construct(
        private ActividadSacdTextoRepositoryInterface $actividadSacdTextoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $clave = input_string($input, 'clave');
        $idioma = input_string($input, 'idioma');
        $texto = input_string($input, 'texto');

        if ($clave === '' || $idioma === '') {
            return _("faltan parametros clave / idioma");
        }

        $cTextos = $this->actividadSacdTextoRepository->getActividadSacdTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);
        $existe = count($cTextos) > 0;

        if ($existe) {
            $oTexto = $cTextos[0];
            if ($texto === '') {
                if ($this->actividadSacdTextoRepository->Eliminar($oTexto) === false) {
                    return _("hay un error, no se ha eliminado el texto");
                }
                return '';
            }
            $oTexto->setTexto($texto);
            if ($this->actividadSacdTextoRepository->Guardar($oTexto) === false) {
                return _("hay un error, no se ha guardado el texto");
            }
            return '';
        }

        if ($texto === '') {
            return '';
        }
        $oTexto = new ActividadSacdTexto();
        $oTexto->setId_item((int)$this->actividadSacdTextoRepository->getNewId());
        $oTexto->setClave($clave);
        $oTexto->setIdioma($idioma);
        $oTexto->setTexto($texto);
        if ($this->actividadSacdTextoRepository->Guardar($oTexto) === false) {
            return _("hay un error, no se ha guardado el texto");
        }
        return '';
    }
}
