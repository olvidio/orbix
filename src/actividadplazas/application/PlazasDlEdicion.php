<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;

/**
 * Resuelve el registro local (da_plazas_dl) para editar concedidas/pedidas/cedidas.
 * La pantalla lee el calendario común (da_plazas); lo que modifica la dl se guarda en da_plazas_dl.
 */
final class PlazasDlEdicion
{
    public function __construct(
        private ActividadPlazasDlRepositoryInterface $actividadPlazasDlRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
    ) {
    }

    public function obtenerOCrearDesdeCalendario(int $idActiv, int $idDl, string $dlTabla): ?ActividadPlazas
    {
        $cLocal = $this->actividadPlazasDlRepository->getActividadesPlazas([
            'id_activ' => $idActiv,
            'id_dl' => $idDl,
            'dl_tabla' => $dlTabla,
        ]);
        if ($cLocal !== []) {
            return $cLocal[0];
        }

        $cCal = $this->actividadPlazasRepository->getActividadesPlazas([
            'id_activ' => $idActiv,
            'id_dl' => $idDl,
            'dl_tabla' => $dlTabla,
        ]);
        if ($cCal === []) {
            $cCal = $this->actividadPlazasRepository->getActividadesPlazas([
                'id_activ' => $idActiv,
                'id_dl' => $idDl,
            ]);
        }
        $o = new ActividadPlazas();
        $o->setId_activ($idActiv);
        $o->setId_dl($idDl);
        $o->setDlTablaVo($dlTabla);

        if ($cCal !== []) {
            $src = $this->elegirFilaCalendario($cCal, $dlTabla);
            $o->setPlazasVo($src->getPlazasVo());
            $cedidas = $src->getArrayCedidas();
            if (is_array($cedidas)) {
                $o->setCedidas($cedidas);
            }
        }
        if ($this->actividadPlazasDlRepository->Guardar($o) === false) {
            return null;
        }

        return $o;
    }

    /**
     * @param array<int, ActividadPlazas> $filas
     */
    private function elegirFilaCalendario(array $filas, string $dlTabla): ActividadPlazas
    {
        foreach ($filas as $fila) {
            if ($fila->getDlTablaVo()->value() === $dlTabla) {
                return $fila;
            }
        }

        return $filas[0];
    }
}
