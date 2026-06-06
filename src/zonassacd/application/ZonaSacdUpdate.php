<?php

namespace src\zonassacd\application;

use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;

final class ZonaSacdUpdate
{
    public function __construct(
        private ZonaSacdRepositoryInterface $zonaSacdRepository,
    ) {
    }

    /**
     * @param list<int|string> $sel
     * @return array<string, mixed>
     */
    public function execute(string $id_zona, string $id_zona_new, int $acumular, array $sel): array
    {
        $errores = [];
        if ($id_zona_new === '') {
            return ['tipo' => 'update', 'mensaje' => '', 'error' => ''];
        }
        $zonaSinAsignar = $id_zona_new === 'no';
        $idZonaNueva = $zonaSinAsignar ? 0 : (int) $id_zona_new;
        foreach ($sel as $id_nom) {
            $idNom = (int) $id_nom;
            if ($acumular === 2) {
                if ($zonaSinAsignar) {
                    $cZonaSacd = $this->zonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if ($cZonaSacd !== [] && $this->zonaSacdRepository->Eliminar($cZonaSacd[0]) === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $this->zonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $idZonaNueva]);
                    if ($cZonaSacd !== []) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setPropia(false);
                    } else {
                        $oZonaSacd = new ZonaSacd();
                        $oZonaSacd->setId_item($this->zonaSacdRepository->getNewId());
                        $oZonaSacd->setId_nom($idNom);
                        $oZonaSacd->setId_zona($idZonaNueva);
                        $oZonaSacd->setPropia(false);
                    }
                    if ($this->zonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                }
            } else {
                if ($id_zona === 'no' || $id_zona === '0') {
                    $oZonaSacd = new ZonaSacd();
                    $oZonaSacd->setId_item($this->zonaSacdRepository->getNewId());
                    $oZonaSacd->setId_nom($idNom);
                    $oZonaSacd->setId_zona($idZonaNueva);
                    $oZonaSacd->setPropia(true);
                    if ($this->zonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                } elseif ($zonaSinAsignar) {
                    $cZonaSacd = $this->zonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if ($cZonaSacd !== [] && $this->zonaSacdRepository->Eliminar($cZonaSacd[0]) === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $this->zonaSacdRepository->getZonasSacds(['id_nom' => $idNom, 'id_zona' => $id_zona]);
                    if ($cZonaSacd !== []) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setId_zona($idZonaNueva);
                        $oZonaSacd->setPropia(true);
                        if ($this->zonaSacdRepository->Guardar($oZonaSacd) === false) {
                            $errores[] = _("hay un error, no se ha guardado");
                        }
                    }
                }
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
