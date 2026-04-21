<?php

namespace src\misas\application;

use Ramsey\Uuid\Uuid as RamseyUuid;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;

class GuardarEncargoCentro
{
    /**
     * Inserta o actualiza un `EncargoCtr` (relacion encargo ↔ centro).
     *
     * - Si `id_item` esta vacio se crea un nuevo `EncargoCtr` con uuid v4.
     * - Si `id_item` es un uuid valido se carga el existente y se modifica.
     *
     * Devuelve texto vacio si todo fue bien, o el mensaje de error del
     * repositorio en caso contrario.
     */
    public static function execute(string $id_item, int $id_enc, int $id_ctr): string
    {
        $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);

        if (empty($id_item)) {
            $Uuid = new EncargoCtrId(RamseyUuid::uuid4()->toString());
            $EncargoCtr = new EncargoCtr();
            $EncargoCtr->setUuidItemVo($Uuid);
        } else {
            $EncargoCtr = $EncargoCtrRepository->findById(new EncargoCtrId($id_item));
            if ($EncargoCtr === null) {
                return sprintf(_('No se encuentra el encargo-centro %s'), $id_item);
            }
        }

        $EncargoCtr->setId_ubi($id_ctr);
        $EncargoCtr->setId_enc($id_enc);

        if ($EncargoCtrRepository->Guardar($EncargoCtr) === false) {
            return $EncargoCtrRepository->getErrorTxt();
        }
        return '';
    }
}
