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
    public static function execute(array $input): string
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

        $repo = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        if ($id_item_raw === '' || $id_item_raw === 'nuevo') {
            $oGrupo = new GrupoCasa();
            $oGrupo->setId_item($repo->getNewId());
        } else {
            $oGrupo = $repo->findById((int)$id_item_raw);
            if ($oGrupo === null) {
                return (string)_("no se encuentra el grupo");
            }
        }

        $oGrupo->setId_ubi_padre($id_ubi_padre);
        $oGrupo->setId_ubi_hijo($id_ubi_hijo);

        if ($repo->Guardar($oGrupo) === false) {
            return (string)_("Hay un error, no se ha guardado.")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
