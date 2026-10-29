<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoTextoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\encargossacd\domain\entity\EncargoTexto;

/**
 * Mutacion del texto de comunicacion para un par (clave, idioma).
 * Si el texto llega vacio, se elimina la fila.
 *
 * Extraido de `EncargoTextoListasComAjax` (rama `que=update`) para eliminar
 * el dispatcher multiproposito (criterio `refactor.md`).
 */
final class ListasComTxtUpdate
{

    public function __construct(
        private EncargoTextoRepositoryInterface $encargoTextoRepository
    ) {
    }

    /**
     * @return array{ok: true}
     */
    public function execute(string $clave, string $idioma, string $comunicacion): array
    {
        $cEncargoTextos = $this->encargoTextoRepository->matching(
            Criteria::create()
                ->where(Filter::eq(EncargoTextoField::Clave, $clave))
                ->andWhere(Filter::eq(EncargoTextoField::Idioma, $idioma))
        );

        if ($cEncargoTextos !== []) {
            $oEncargoTexto = $cEncargoTextos[0];
            if ($comunicacion === '') {
                $this->encargoTextoRepository->Eliminar($oEncargoTexto);
            } else {
                $oEncargoTexto->setTexto($comunicacion);
                $this->encargoTextoRepository->Guardar($oEncargoTexto);
            }
        } else {
            $oEncargoTexto = new EncargoTexto();
            $oEncargoTexto->setId_item($this->encargoTextoRepository->getNewId());
            $oEncargoTexto->setClave($clave);
            $oEncargoTexto->setIdioma($idioma);
            $oEncargoTexto->setTexto($comunicacion);
            $this->encargoTextoRepository->Guardar($oEncargoTexto);
        }

        return ['ok' => true];
    }
}
