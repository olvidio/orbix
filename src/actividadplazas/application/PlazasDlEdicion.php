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
    public static function obtenerOCrearDesdeCalendario(int $idActiv, int $idDl, string $dlTabla): ?ActividadPlazas
    {
        $dlRepo = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        $cLocal = $dlRepo->getActividadesPlazas([
            'id_activ' => $idActiv,
            'id_dl' => $idDl,
            'dl_tabla' => $dlTabla,
        ]);
        if (is_array($cLocal) && $cLocal !== [] && $cLocal !== false) {
            return $cLocal[0];
        }

        $calRepo = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $cCal = $calRepo->getActividadesPlazas([
            'id_activ' => $idActiv,
            'id_dl' => $idDl,
            'dl_tabla' => $dlTabla,
        ]);
        if (!is_array($cCal) || $cCal === [] || $cCal === false) {
            $cCal = $calRepo->getActividadesPlazas([
                'id_activ' => $idActiv,
                'id_dl' => $idDl,
            ]);
        }
        if (!is_array($cCal) || $cCal === [] || $cCal === false) {
            return null;
        }

        $src = self::elegirFilaCalendario($cCal, $dlTabla);
        $o = new ActividadPlazas();
        $o->setId_activ($idActiv);
        $o->setId_dl($idDl);
        $o->setDlTablaVo($dlTabla);
        $o->setPlazasVo($src->getPlazasVo());
        $cedidas = $src->getArrayCedidas();
        if (is_array($cedidas)) {
            $o->setCedidas($cedidas);
        }
        if ($dlRepo->Guardar($o) === false) {
            return null;
        }

        return $o;
    }

    /**
     * @param array<int, ActividadPlazas> $filas
     */
    private static function elegirFilaCalendario(array $filas, string $dlTabla): ActividadPlazas
    {
        foreach ($filas as $fila) {
            if ($fila->getDlTablaVo()->value() === $dlTabla) {
                return $fila;
            }
        }

        return $filas[0];
    }
}
