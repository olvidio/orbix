<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\shared\domain\helpers\FuncTablasSupport;
final class CentrosUpdate
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * Actualiza datos de centro DL (labor / num / plazas según POST).
     *
     * Cada formulario de `centros_que` envía solo su bloque de campos; el resto
     * no debe tocarse (antes se pisaban con 0 / vacío).
     *
     * @param array<string, mixed> $input
     * @return string Texto de error para mostrar al usuario, o cadena vacía si OK
     */
    public function execute(array $input): string
    {
        $Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        if ($Qid_ubi === 0) {
            return '';
        }

        $oCentro = $this->centroDlRepository->findById($Qid_ubi);
        if ($oCentro === null) {
            return '';
        }

        $updated = false;
        if ($this->isLaborUpdate($input)) {
            $this->applyLabor($oCentro, $input);
            $updated = true;
        } elseif ($this->isNumUpdate($input)) {
            $this->applyNum($oCentro, $input);
            $updated = true;
        } elseif ($this->isPlazasUpdate($input)) {
            $this->applyPlazas($oCentro, $input);
            $updated = true;
        }

        if (!$updated) {
            return '';
        }

        if ($this->centroDlRepository->Guardar($oCentro) === false) {
            return (string)_("Hay un error, no se ha guardado.");
        }

        return '';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function isLaborUpdate(array $input): bool
    {
        return FuncTablasSupport::inputString($input, 'labor') === 'si';
    }

    /**
     * @param array<string, mixed> $input
     */
    private function isNumUpdate(array $input): bool
    {
        return array_key_exists('n_buzon', $input)
            || array_key_exists('num_pi', $input)
            || array_key_exists('num_cartas', $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function isPlazasUpdate(array $input): bool
    {
        return array_key_exists('num_habit_indiv', $input)
            || array_key_exists('plazas', $input)
            || array_key_exists('sede', $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function applyLabor(CentroDl $oCentro, array $input): void
    {
        $oCentro->setTipo_ctr(FuncTablasSupport::inputString($input, 'tipo_ctr'));

        $aTipo_labor = $input['tipo_labor'] ?? [];
        if (!is_array($aTipo_labor)) {
            $aTipo_labor = [];
        }
        if ($aTipo_labor === []) {
            return;
        }

        $byte = 0;
        foreach ($aTipo_labor as $bit) {
            if (!is_int($bit) && !is_string($bit) && !is_float($bit) && !is_bool($bit) && $bit !== null) {
                continue;
            }
            $byte += (int) $bit;
        }
        $oCentro->setTipo_labor($byte);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function applyNum(CentroDl $oCentro, array $input): void
    {
        $oCentro->setN_buzon(FuncTablasSupport::inputInt($input, 'n_buzon'));
        $oCentro->setNum_pi(FuncTablasSupport::inputInt($input, 'num_pi'));
        $oCentro->setNum_cartas(FuncTablasSupport::inputInt($input, 'num_cartas'));
    }

    /**
     * @param array<string, mixed> $input
     */
    private function applyPlazas(CentroDl $oCentro, array $input): void
    {
        $oCentro->setNum_habit_indiv(FuncTablasSupport::inputInt($input, 'num_habit_indiv'));
        $oCentro->setPlazas(FuncTablasSupport::inputInt($input, 'plazas'));
        $oCentro->setSede(FuncTablasSupport::isTrue(FuncTablasSupport::inputString($input, 'sede')));
    }
}
