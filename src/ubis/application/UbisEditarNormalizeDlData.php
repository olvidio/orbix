<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Ajusta `obj_pau` a CentroDl/CasaDl cuando la ficha es de la delegación actual.
 */
final class UbisEditarNormalizeDlData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
    ) {
    }

    public function execute(int $id_ubi, string $tipo_ubi, string $nombre_ubi, string $Qobj_pau): string
    {
        if ($tipo_ubi === 'ctrdl') {
            $oUbi_new = $this->centroDlRepository->findById($id_ubi);
            if ($oUbi_new === null) {
                return $Qobj_pau;
            }
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                return 'CentroDl';
            }
        }
        if ($tipo_ubi === 'cdcdl') {
            $oUbi_new = $this->casaDlRepository->findById($id_ubi);
            if ($oUbi_new === null) {
                return $Qobj_pau;
            }
            $nombre_ubi_new = $oUbi_new->getNombre_ubi();
            if ($nombre_ubi == $nombre_ubi_new) {
                return 'CasaDl';
            }
        }

        return $Qobj_pau;
    }
}
