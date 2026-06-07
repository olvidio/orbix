<?php

namespace src\tablonanuncios\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\value_objects\AnuncioId;

/**
 * Borrado lógico de un anuncio (marca t_eliminado).
 */
final class AnuncioDelete
{
    public function __construct(
        private AnuncioRepositoryInterface $anuncioRepository,
    ) {
    }

    public function execute(string $uuid_item): string
    {
        if ($uuid_item === '') {
            return (string) _('No se encuentra el anuncio');
        }

        $id = new AnuncioId($uuid_item);
        $oAnuncio = $this->anuncioRepository->findById($id);
        if ($oAnuncio === null) {
            return (string) _('No se encuentra el anuncio');
        }

        $oAnuncio->setT_eliminado(new DateTimeLocal());
        if ($this->anuncioRepository->Guardar($oAnuncio) === false) {
            return (string) _('error al borrar el anuncio');
        }

        return '';
    }
}
