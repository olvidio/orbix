<?php

namespace src\actividadessacd\application;

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\actividadessacd\domain\criteria\ActividadSacdTextoField;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;

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
        $clave = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'clave');
        $idioma = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'idioma');
        $texto = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'texto');

        if ($clave === '' || $idioma === '') {
            return _("faltan parametros clave / idioma");
        }

        $cTextos = $this->actividadSacdTextoRepository->matching(Criteria::create()->where(Filter::eq(ActividadSacdTextoField::Clave, $clave))->andWhere(Filter::eq(ActividadSacdTextoField::Idioma, $idioma)));
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
