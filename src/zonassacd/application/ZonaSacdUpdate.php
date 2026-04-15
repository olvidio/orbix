<?php

namespace src\zonassacd\application;

use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

class ZonaSacdUpdate
{
    public static function execute(string $id_zona, string $id_zona_new, int $acumular, array $sel): array
    {
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $errores = [];
        if (empty($id_zona_new)) {
            return ['tipo' => 'update', 'mensaje' => '', 'error' => ''];
        }
        $nuevaZona = $id_zona_new === 'no' ? '' : $id_zona_new;
        foreach ($sel as $id_nom) {
            if ($acumular === 2) {
                if (empty($nuevaZona)) {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $cZonaSacd[0]->DBEliminar() === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $nuevaZona]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setPropia('f');
                    } else {
                        $oZonaSacd = new ZonaSacd();
                        $oZonaSacd->setId_item($ZonaSacdRepository->getNewId());
                        $oZonaSacd->setId_nom($id_nom);
                        $oZonaSacd->setId_zona($nuevaZona);
                        $oZonaSacd->setPropia('f');
                    }
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                }
            } else {
                if ($id_zona === 'no' || $id_zona == 0) {
                    $oZonaSacd = new ZonaSacd();
                    $oZonaSacd->setId_item($ZonaSacdRepository->getNewId());
                    $oZonaSacd->setId_nom($id_nom);
                    $oZonaSacd->setId_zona($nuevaZona);
                    $oZonaSacd->setPropia('t');
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                } elseif (empty($nuevaZona)) {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $cZonaSacd[0]->DBEliminar() === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setId_zona($nuevaZona);
                        $oZonaSacd->setPropia('t');
                        if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                            $errores[] = _("hay un error, no se ha guardado");
                        }
                    }
                }
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
