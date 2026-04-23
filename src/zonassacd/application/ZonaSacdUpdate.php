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
        // `$nuevaZona` llega como string desde el form (id numerico o `'no'`).
        // Guardamos la version int porque las entidades/contratos de dominio
        // la manejan asi; mantenemos un booleano auxiliar para el caso
        // "quitar la zona" (equivalente al viejo `'no'`).
        $zonaSinAsignar = $id_zona_new === 'no';
        $idZonaNueva = $zonaSinAsignar ? 0 : (int)$id_zona_new;
        foreach ($sel as $id_nom) {
            $idNom = (int)$id_nom;
            if ($acumular === 2) {
                if ($zonaSinAsignar) {
                    // `ZonaSacd` es una entidad de dominio, no tiene `DBEliminar()`;
                    // la eliminacion va por el repositorio.
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $ZonaSacdRepository->Eliminar($cZonaSacd[0]) === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $idZonaNueva]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        // `setPropia` espera `bool`; con `'f'` PHP lo coerciona
                        // silenciosamente a `true` en modo no-strict (bug).
                        $oZonaSacd->setPropia(false);
                    } else {
                        $oZonaSacd = new ZonaSacd();
                        $oZonaSacd->setId_item((int)$ZonaSacdRepository->getNewId());
                        $oZonaSacd->setId_nom($idNom);
                        $oZonaSacd->setId_zona($idZonaNueva);
                        $oZonaSacd->setPropia(false);
                    }
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                }
            } else {
                if ($id_zona === 'no' || $id_zona == 0) {
                    $oZonaSacd = new ZonaSacd();
                    $oZonaSacd->setId_item((int)$ZonaSacdRepository->getNewId());
                    $oZonaSacd->setId_nom($idNom);
                    $oZonaSacd->setId_zona($idZonaNueva);
                    $oZonaSacd->setPropia(true);
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                } elseif ($zonaSinAsignar) {
                    // Idem: eliminamos via `ZonaSacdRepositoryInterface::Eliminar`.
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $ZonaSacdRepository->Eliminar($cZonaSacd[0]) === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setId_zona($idZonaNueva);
                        $oZonaSacd->setPropia(true);
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
