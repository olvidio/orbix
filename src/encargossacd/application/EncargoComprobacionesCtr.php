<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo
 * `frontend/encargossacd/controller/comprobaciones.php`).
 */
final class EncargoComprobacionesCtr
{

    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository
    ) {
    }

    /**
     * @return array{texto: string}
     */
    public function ejecutar(): array
    {
        $ctrsv = 0;
        $ctrsf = 0;
        $aWhere = ['id_ubi' => 'x'];
        $aOperador = ['id_ubi' => 'IS NOT NULL'];
        $cEncargosCtr = $this->encargoRepository->getEncargos($aWhere, $aOperador);
        foreach ($cEncargosCtr as $oEncargo) {
            $id_ubi = $oEncargo->getId_ubi();
            if (empty($id_ubi)) {
                continue;
            }
            $pref = substr((string)$id_ubi, 0, 1);
            if ($pref === '1') {
                $oCentroDl = $this->centroDlRepository->findById($id_ubi);
                if ($oCentroDl === null) {
                    continue;
                }
                $status = $oCentroDl->isActive();
                if ($status === false) {
                    $ctrsv++;
                    $this->encargoRepository->Eliminar($oEncargo);
                }
            } else {
                $oCentroSf = $this->centroEllasRepository->findById($id_ubi);
                if ($oCentroSf === null) {
                    continue;
                }
                $status = $oCentroSf->isActive();
                if ($status === false) {
                    $ctrsf++;
                    $this->encargoRepository->Eliminar($oEncargo);
                }
            }
        }
        $msg = '';
        $msg .= sprintf(_("se han eliminado %s encargos de centros sv \n"), $ctrsv);
        $msg .= sprintf(_("se han eliminado %s encargos de centros sf \n"), $ctrsf);
        // borrar los encargos de los sacd. No se puede hacer desde la DB porque se ha pasado a la DB comun.
        // $msg .= $this->encargoSacdRepository->deleteEncargos();
        $cEncargos = $this->encargoRepository->getEncargos();
        $a_Id_enc = [];
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $a_Id_enc[] = $id_enc;
        }
        $msg .= $this->encargoSacdRepository->deleteEncargos($a_Id_enc);

        return ['texto' => $msg];
    }
}
