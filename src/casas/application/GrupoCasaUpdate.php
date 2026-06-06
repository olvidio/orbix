<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\entity\GrupoCasa;

/**
 * Mutación: crea o actualiza un `GrupoCasa`.
 *
 * Sucesor de la rama `update` de `apps/casas/controller/grupo_ajax.php`.
 */
final class GrupoCasaUpdate
{
    public function __construct(
        private GrupoCasaRepositoryInterface $grupoCasaRepository,
    ) {
    }

    /**
     * @param array{
     *   id_item?: string,
     *   id_ubi_padre?: int|string,
     *   id_ubi_hijo?: int|string
     * } $input
     */
    public function execute(array $input): string
    {
        $id_item_raw = (string)($input['id_item'] ?? '');
        $id_ubi_padre = (int)($input['id_ubi_padre'] ?? 0);
        $id_ubi_hijo = (int)($input['id_ubi_hijo'] ?? 0);

        if ($id_ubi_padre === 0 || $id_ubi_hijo === 0) {
            return (string)_("debe indicar las dos casas");
        }
        if ($id_ubi_padre === $id_ubi_hijo) {
            return (string)_("No puede ser la misma casa");
        }

        if ($id_item_raw === '' || $id_item_raw === 'nuevo') {
            $oGrupo = new GrupoCasa();
            $oGrupo->setId_item($this->grupoCasaRepository->getNewId());
        } else {
            $oGrupo = $this->grupoCasaRepository->findById((int)$id_item_raw);
            if ($oGrupo === null) {
                return (string)_("no se encuentra el grupo");
            }
        }

        $oGrupo->setId_ubi_padre($id_ubi_padre);
        $oGrupo->setId_ubi_hijo($id_ubi_hijo);

        if ($this->grupoCasaRepository->Guardar($oGrupo) === false) {
            return (string)_("Hay un error, no se ha guardado.")
                . "\n" . $this->grupoCasaRepository->getErrorTxt();
        }

        return '';
    }
}
