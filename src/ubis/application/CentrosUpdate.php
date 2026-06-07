<?php

namespace src\ubis\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use function src\shared\domain\helpers\is_true;

final class CentrosUpdate
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * Actualiza datos de centro DL (labor / num / plazas según POST).
     *
     * @param array<string, mixed> $input
     * @return string Texto de error para mostrar al usuario, o cadena vacía si OK
     */
    public function execute(array $input): string
    {
        $Qid_ubi = input_int($input, 'id_ubi');
        if ($Qid_ubi === 0) {
            return '';
        }

        $Qtipo_ctr = input_string($input, 'tipo_ctr');
        $Qlabor = input_string($input, 'labor');
        $aTipo_labor = $input['tipo_labor'] ?? [];
        if (!is_array($aTipo_labor)) {
            $aTipo_labor = [];
        }

        $Qn_buzon = input_int($input, 'n_buzon');
        $Qnum_pi = input_int($input, 'num_pi');
        $Qnum_cartas = input_int($input, 'num_cartas');
        $Qnum_habit_indiv = input_int($input, 'num_habit_indiv');
        $Qplazas = input_int($input, 'plazas');
        $Qsede = input_string($input, 'sede');

        $CentroDlRepository = $this->centroDlRepository;
        $oCentro = $CentroDlRepository->findById($Qid_ubi);
        if ($oCentro === null) {
            return '';
        }

        $oCentro->setTipo_ctr($Qtipo_ctr);
        if ($Qlabor === 'si' && !empty($aTipo_labor)) {
            $byte = 0;
            foreach ($aTipo_labor as $bit) {
                if (!is_int($bit) && !is_string($bit) && !is_float($bit) && !is_bool($bit) && $bit !== null) {
                    continue;
                }
                $byte += (int) $bit;
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
