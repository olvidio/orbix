<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;

/**
 * Mutación: elimina un `GrupoCasa` por `id_item`.
 *
 * Sucesor de la rama `eliminar` de `apps/casas/controller/grupo_ajax.php`.
 */
final class GrupoCasaEliminar
{
    public function __construct(
        private GrupoCasaRepositoryInterface $grupoCasaRepository,
    ) {
    }

    /**
     * @param array{id_item?: int|string} $input
     */
    public function execute(array $input): string
    {
        $id_item = (int)($input['id_item'] ?? 0);
        if ($id_item === 0) {
            return (string)_("debe indicar el grupo a eliminar");
        }

        $oGrupo = $this->grupoCasaRepository->findById($id_item);
        if ($oGrupo === null) {
            return (string)_("no se encuentra el grupo");
        }

        if ($this->grupoCasaRepository->Eliminar($oGrupo) === false) {
            return (string)_("Hay un error, no se ha eliminado.")
                . "\n" . $this->grupoCasaRepository->getErrorTxt();
        }

        return '';
    }
}
