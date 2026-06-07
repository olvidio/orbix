<?php

namespace src\misas\application;

use Ramsey\Uuid\Uuid as RamseyUuid;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;

class GuardarEncargoCentro
{

    public function __construct(
        private readonly EncargoCtrRepositoryInterface $encargoCtrRepository,
    ) {
    }
    /**
     * Inserta o actualiza un `EncargoCtr` (relacion encargo ↔ centro).
     *
     * - Si `id_item` esta vacio se crea un nuevo `EncargoCtr` con uuid v4.
     * - Si `id_item` es un uuid valido se carga el existente y se modifica.
     *
     * Devuelve texto vacio si todo fue bien, o el mensaje de error del
     * repositorio en caso contrario.
     */
    public function execute(string $id_item, int $id_enc, int $id_ctr): string
    {

        if (empty($id_item)) {
            $Uuid = new EncargoCtrId(RamseyUuid::uuid4()->toString());
            $EncargoCtr = new EncargoCtr();
            $EncargoCtr->setUuidItemVo($Uuid);
        } else {
            $EncargoCtr = $this->encargoCtrRepository->findById(new EncargoCtrId($id_item));
            if ($EncargoCtr === null) {
                return sprintf(_('No se encuentra el encargo-centro %s'), $id_item);
            }
        }

        $EncargoCtr->setId_ubi($id_ctr);
        $EncargoCtr->setId_enc($id_enc);

        if ($this->encargoCtrRepository->Guardar($EncargoCtr) === false) {
            return $this->encargoCtrRepository->getErrorTxt();
        }
        return '';
    }
}
