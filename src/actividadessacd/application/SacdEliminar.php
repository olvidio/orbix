<?php

namespace src\actividadessacd\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;

/**
 * Elimina un sacd ({id_activ, id_cargo}) de una actividad, incluyendo
 * la fila de `Asistencia` {id_activ, id_nom} asociada (si existe).
 */
final class SacdEliminar
{
    public function __construct(
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private AsistenteDlRepositoryInterface $asistenteDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $id_cargo = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_cargo');
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');

        if ($id_activ <= 0 || $id_cargo <= 0) {
            return _("no se sabe cual borrar");
        }

        $errors = '';

        $cActCargo = $this->actividadCargoRepository->getActividadCargos(
            ['id_activ' => $id_activ, 'id_cargo' => $id_cargo]
        );
        if (count($cActCargo) >= 1) {
            if ($this->actividadCargoRepository->Eliminar($cActCargo[0]) === false) {
                $errors .= _("hay un error, no se ha eliminado el cargo") . ' ';
            }
        }

        if ($id_nom > 0) {
            $oAsisActiv = $this->asistenteDlRepository->findById($id_activ, $id_nom);
            if ($oAsisActiv !== null) {
                if ($this->asistenteDlRepository->Eliminar($oAsisActiv) === false) {
                    $errors .= _("hay un error, no se ha eliminado la asistencia") . ' ';
                }
            }
        }

        return trim($errors);
    }
}
