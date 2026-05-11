<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Filas de la tabla del dossier 1302 ({@see Select_cargos_personas_en_actividad}).
 * Extraído para poder testear la lógica sin HTML ni {@see Persona::findPersonaEnGlobal}.
 */
final class SelectCargosPersonasEnActividadTableData
{
    /**
     * @return array<int|string, mixed> mismo formato que {@see Lista::setDatos}
     */
    public static function buildValorRows(
        int $mi_sfsv,
        int $elim_asis_default,
        iterable $cCargosEnActividad,
        CargoRepositoryInterface $cargoRepo,
        ActividadAllRepositoryInterface $actAllRepo,
        array $refPerm,
        mixed $qidSel,
        mixed $qscrollId,
    ): array {
        $c = 0;
        $a_valores = [];
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $id_item = $oActividadCargo->getId_item();
            $id_activ = $oActividadCargo->getId_activ();
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $cargoRepo->findById($id_cargo);
            $tipo_cargo = $oCargo?->getTipoCargoVo()?->value() ?? '';
            $cargo = $oCargo?->getCargoVo()?->value() ?? '';
            if ($tipo_cargo === 'sacd' && $mi_sfsv === 2) {
                continue;
            }

            $oActividad = $actAllRepo->findById($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();

            $chk_puede_agd = is_true($oActividadCargo->isPuede_agd()) ? 'si' : 'no';
            $observ = (string) ($oActividadCargo->getObserv() ?? '');

            $id_tipo = substr((string) $id_tipo_activ, 0, 3);
            $act = !empty($refPerm[$id_tipo]) ? $refPerm[$id_tipo] : '';
            $permiso = (!empty($act) && !empty($act['perm'])) ? 3 : 1;

            $a_valores[$c]['sel'] = $permiso === 3 ? "$id_item#$elim_asis_default" : '';
            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = $nom_activ;
            $a_valores[$c][3] = $chk_puede_agd;
            $a_valores[$c][4] = $observ;
        }

        if (!empty($a_valores)) {
            if (!empty($qidSel)) {
                $a_valores['select'] = $qidSel;
            }
            if (!empty($qscrollId)) {
                $a_valores['scroll_id'] = $qscrollId;
            }
        }

        return $a_valores;
    }
}
