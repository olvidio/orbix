<?php

namespace src\actividadessacd\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Reordena un sacd en el listado de cargos sacd de una actividad.
 */
final class SacdReordenar
{
    public function __construct(
        private CargoRepositoryInterface $cargoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $id_nom = FuncTablasSupport::inputInt($input, 'id_nom');
        $direccion = FuncTablasSupport::inputString($input, 'num_orden');

        if ($id_activ <= 0 || $id_nom <= 0) {
            return _("faltan parametros id_activ / id_nom");
        }
        if ($direccion !== 'mas' && $direccion !== 'menos') {
            return _("direccion de orden incorrecta (mas / menos)");
        }

        $aIdCargos_sacd = $this->cargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $cCargos = $this->actividadCargoRepository->getActividadCargos(
            ['id_activ' => $id_activ, 'id_cargo' => $txt_where_cargos],
            ['id_cargo' => 'IN']
        );

        $errors = '';
        $i_max = count($cCargos);
        for ($i = 0; $i < $i_max; $i++) {
            if ((int)$cCargos[$i]->getId_nom() !== $id_nom) {
                continue;
            }
            if ($direccion === 'mas' && $i >= 1) {
                $anterior_id_nom = (int)$cCargos[$i - 1]->getId_nom();
                if ($anterior_id_nom === 0) {
                    break;
                }
                $cCargos[$i - 1]->setId_nom($id_nom);
                if ($this->actividadCargoRepository->Guardar($cCargos[$i - 1]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
                $cCargos[$i]->setId_nom($anterior_id_nom);
                if ($this->actividadCargoRepository->Guardar($cCargos[$i]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
            } elseif ($direccion === 'menos' && $i < ($i_max - 1)) {
                $post_id_nom = (int)$cCargos[$i + 1]->getId_nom();
                if ($post_id_nom === 0) {
                    break;
                }
                $cCargos[$i + 1]->setId_nom($id_nom);
                if ($this->actividadCargoRepository->Guardar($cCargos[$i + 1]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
                $cCargos[$i]->setId_nom($post_id_nom);
                if ($this->actividadCargoRepository->Guardar($cCargos[$i]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
            }
            break;
        }
        return trim($errors);
    }
}
