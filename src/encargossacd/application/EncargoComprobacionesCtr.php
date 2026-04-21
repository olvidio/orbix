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
    /**
     * @return array{texto: string}
     */
    public static function ejecutar(): array
    {
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);

        $ctrsv = 0;
        $ctrsf = 0;
        $aWhere = ['id_ubi' => 'x'];
        $aOperador = ['id_ubi' => 'IS NOT NULL'];
        $cEncargosCtr = $EncargoRepository->getEncargos($aWhere, $aOperador);
        if (!is_array($cEncargosCtr)) {
            $cEncargosCtr = [];
        }
        foreach ($cEncargosCtr as $oEncargo) {
            $id_ubi = $oEncargo->getId_ubi();
            if (empty($id_ubi)) {
                continue;
            }
            $pref = substr((string)$id_ubi, 0, 1);
            if ($pref === '1') {
                $oCentroDl = $CentroDlRepository->findById($id_ubi);
                if ($oCentroDl === null) {
                    continue;
                }
                $status = $oCentroDl->isActive();
                if ($status === false) {
                    $ctrsv++;
                    $EncargoRepository->Eliminar($oEncargo);
                }
            } else {
                $oCentroSf = $CentroEllasRepository->findById($id_ubi);
                if ($oCentroSf === null) {
                    continue;
                }
                $status = $oCentroSf->isActive();
                if ($status === false) {
                    $ctrsf++;
                    $EncargoRepository->Eliminar($oEncargo);
                }
            }
        }
        $msg = '';
        $msg .= sprintf(_("se han eliminado %s encargos de centros sv \n"), $ctrsv);
        $msg .= sprintf(_("se han eliminado %s encargos de centros sf \n"), $ctrsf);
        // borrar los encargos de los sacd. No se puede hacer desde la DB porque se ha pasado a la DB comun.
        // $msg .= $EncargoSacdRepository->deleteEncargos();
        $cEncargos = $EncargoRepository->getEncargos();
        $a_Id_enc = [];
        foreach ($cEncargos as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $a_Id_enc[] = $id_enc;
        }
        $msg .= $EncargoSacdRepository->deleteEncargos($a_Id_enc);

        return ['texto' => $msg];
    }
}
