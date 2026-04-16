<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use function core\is_true;

final class CentrosUpdate
{
    /**
     * Actualiza datos de centro DL (labor / num / plazas según POST).
     *
     * @param array<string, mixed> $input
     * @return string Texto de error para mostrar al usuario, o cadena vacía si OK
     */
    public static function execute(array $input): string
    {
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        if ($Qid_ubi === 0) {
            return '';
        }

        $Qtipo_ctr = (string)($input['tipo_ctr'] ?? '');
        $Qlabor = (string)($input['labor'] ?? '');
        $aTipo_labor = $input['tipo_labor'] ?? [];
        if (!is_array($aTipo_labor)) {
            $aTipo_labor = [];
        }

        $Qn_buzon = (int)($input['n_buzon'] ?? 0);
        $Qnum_pi = (int)($input['num_pi'] ?? 0);
        $Qnum_cartas = (int)($input['num_cartas'] ?? 0);
        $Qnum_habit_indiv = (int)($input['num_habit_indiv'] ?? 0);
        $Qplazas = (int)($input['plazas'] ?? 0);
        $Qsede = (string)($input['sede'] ?? '');

        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($Qid_ubi);
        if ($oCentro === null) {
            return '';
        }

        $oCentro->setTipo_ctr($Qtipo_ctr);
        if ($Qlabor === 'si' && !empty($aTipo_labor) && count($aTipo_labor) > 0) {
            $byte = 0;
            foreach ($aTipo_labor as $bit) {
                $byte = $byte + (int)$bit;
            }
            $oCentro->setTipo_labor($byte);
        }
        $oCentro->setN_buzon($Qn_buzon);
        $oCentro->setNum_pi($Qnum_pi);
        $oCentro->setNum_cartas($Qnum_cartas);
        $oCentro->setNum_habit_indiv($Qnum_habit_indiv);
        $oCentro->setPlazas($Qplazas);
        $oCentro->setSede(is_true($Qsede));

        if ($CentroDlRepository->Guardar($oCentro) === false) {
            return (string)_("Hay un error, no se ha guardado.");
        }

        return '';
    }
}
