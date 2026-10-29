<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\criteria\ActividadPlazasField as F;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

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
        $cLocal = $this->actividadPlazasDlRepository->matching(
            Criteria::create()
                ->where(Filter::eq(F::IdActiv, $idActiv))
                ->andWhere(Filter::eq(F::IdDl, $idDl))
                ->andWhere(Filter::eq(F::DlTabla, $dlTabla))
        );
        if ($cLocal !== []) {
            $local = $cLocal[0];
            // Registro local vacío (p. ej. creado antes de existir calendario):
            // rellenar plazas desde el calendario común sin pisar cedidas ya guardadas.
            if (($local->getPlazasVo()?->value() ?? 0) <= 0) {
                $src = $this->buscarFilaCalendario($idActiv, $idDl, $dlTabla);
                if ($src !== null) {
                    $local->setPlazasVo($src->getPlazasVo());
                    $cedidasLocal = $local->getArrayCedidas();
                    if (empty($cedidasLocal)) {
                        $cedidas = $src->getArrayCedidas();
                        if (is_array($cedidas) && $cedidas !== []) {
                            $local->setCedidas($cedidas);
                        }
                    }
                    if ($this->actividadPlazasDlRepository->Guardar($local) === false) {
                        return null;
                    }
                }
            }

            return $local;
        }

        $o = new ActividadPlazas();
        $o->setId_activ($idActiv);
        $o->setId_dl($idDl);
        $o->setDlTablaVo($dlTabla);

        $src = $this->buscarFilaCalendario($idActiv, $idDl, $dlTabla);
        if ($src !== null) {
            $o->setPlazasVo($src->getPlazasVo());
            $cedidas = $src->getArrayCedidas();
            if (is_array($cedidas)) {
                $o->setCedidas($cedidas);
            }
        }
        // Sin fila de calendario también se crea el registro local: hace falta
        // para ceder plazas «conseguidas» de otras dl (cupo sin calendario propio).
        if ($this->actividadPlazasDlRepository->Guardar($o) === false) {
            return null;
        }

        return $o;
    }

    private function buscarFilaCalendario(int $idActiv, int $idDl, string $dlTabla): ?ActividadPlazas
    {
        $cCal = $this->actividadPlazasRepository->matching(
            Criteria::create()
                ->where(Filter::eq(F::IdActiv, $idActiv))
                ->andWhere(Filter::eq(F::IdDl, $idDl))
                ->andWhere(Filter::eq(F::DlTabla, $dlTabla))
        );
        if ($cCal === []) {
            $cCal = $this->actividadPlazasRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(F::IdActiv, $idActiv))
                    ->andWhere(Filter::eq(F::IdDl, $idDl))
            );
        }
        if ($cCal === []) {
            return null;
        }

        return $this->elegirFilaCalendario($cCal, $dlTabla);
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
