<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;

/**
 * Guarda la valoración de quien preguntó a la ayuda: si la respuesta le sirvió y, si no,
 * qué buscaba.
 */
final class AyudaConsultaValorar
{
    private const MAX_COMENTARIO = 1000;

    public function __construct(
        private readonly AyudaConsultaRepositoryInterface $repository,
    ) {
    }

    /**
     * @return string `''` si se ha guardado; si no, el texto del error.
     */
    public function execute(string $uuid_item, bool $util, string $comentario): string
    {
        $consulta = $this->repository->findById($uuid_item);
        if ($consulta === null) {
            return _("No se encuentra la consulta que se quiere valorar.");
        }

        $consulta->setUtil($util);
        $comentario = trim($comentario);
        if ($comentario !== '') {
            $consulta->setComentario(mb_substr($comentario, 0, self::MAX_COMENTARIO));
        }

        if ($this->repository->Guardar($consulta) === false) {
            return _("No se ha podido guardar la valoración.");
        }

        return '';
    }
}
