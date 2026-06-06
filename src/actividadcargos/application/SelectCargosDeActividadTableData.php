<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;
use function src\shared\domain\helpers\is_true;

/**
 * Filas de la tabla del dossier 3102 ({@see Select_cargos_de_actividad}).
 * Extraído para poder testear la lógica sin HTML ni sesión.
 */
final class SelectCargosDeActividadTableData
{
    /**
     * @param iterable<ActividadCargo> $cCargosEnActividad
     * @param callable(?int): (PersonaDl|PersonaPub|null) $findPersona
     * @param array<string, array{perm?: mixed, obj?: mixed, nom?: mixed}> $aRefPerm
     * @return array{a_valores: array<int|string, mixed>, msg_err: string}
     */
    public static function buildValorRows(
        int $elim_asis_default,
        int $mi_sfsv,
        iterable $cCargosEnActividad,
        CargoRepositoryInterface $cargoRepo,
        callable $findPersona,
        array $aRefPerm,
        mixed $qidSel,
        mixed $qscrollId,
    ): array {
        $msg_err = '';
        $c = 0;
        $a_valores = [];
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $id_schema = $oActividadCargo->getId_schema();
            $id_item = $oActividadCargo->getId_item();
            $id_nom = $oActividadCargo->getId_nom();
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $cargoRepo->findById($id_cargo);
            $tipo_cargo = '';
            $cargo = '';
            if ($oCargo !== null) {
                $tipo_cargo = $oCargo->getTipoCargoVo()?->value() ?? '';
                $cargo = $oCargo->getCargoVo()->value();
            }
            if ($tipo_cargo === 'sacd' && $mi_sfsv === 2) {
                continue;
            }

            $oPersona = $findPersona($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ': line ' . __LINE__;
                continue;
            }

            $nom = $oPersona->getPrefApellidosNombre();
            $ctr_dl = $oPersona->getCentro_o_dl();
            $chk_puede_agd = is_true($oActividadCargo->isPuede_agd()) ? 'si' : 'no';
            $observ = (string) ($oActividadCargo->getObserv() ?? '');

            $permiso = 1;
            $id_tabla = $oPersona->getId_tabla();
            if ($id_tabla !== '') {
                $a_act = $aRefPerm[$id_tabla] ?? null;
                $permiso = (!empty($a_act) && !empty($a_act['perm'])) ? 3 : 1;
            } else {
                $permiso = 3;
            }

            if ($permiso === 3) {
                $a_valores[$c]['sel'] = "$id_nom#$id_item#$elim_asis_default#$id_schema";
            } else {
                $a_valores[$c]['sel'] = '';
            }
            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom  ($ctr_dl)";
            $a_valores[$c][3] = $chk_puede_agd;
            $a_valores[$c][4] = $observ;
        }

        if ($a_valores !== []) {
            if (!empty($qidSel)) {
                $a_valores['select'] = $qidSel;
            }
            if (!empty($qscrollId)) {
                $a_valores['scroll_id'] = $qscrollId;
            }
        }

        return ['a_valores' => $a_valores, 'msg_err' => $msg_err];
    }
}
