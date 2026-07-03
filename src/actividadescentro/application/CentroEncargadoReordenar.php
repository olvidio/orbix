<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;

/**
 * Reordena un `CentroEncargado` en el listado de centros encargados de una
 * actividad, subiendole o bajandole prioridad (`mas` / `menos`).
 *
 * Intercambia `num_orden` con el vecino superior (`mas`) o inferior
 * (`menos`) en el orden actual. Es una operacion de dos UPDATEs; si falla
 * alguno, se concatenan los errores.
 *
 * Sucesor de la funcion `ordena()` y rama `orden` + `num_orden=mas|menos`
 * del dispatcher legacy `activ_ctr_ajax.php`.
 */
final class CentroEncargadoReordenar
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $id_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $direccion = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'num_orden');

        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("faltan parametros id_activ / id_ubi");
        }
        if ($direccion !== 'mas' && $direccion !== 'menos') {
            return _("direccion de orden incorrecta (mas / menos)");
        }

        $cCentros = $this->centroEncargadoRepository->getCentrosEncargados([
            'id_activ' => $id_activ,
            '_ordre' => 'num_orden',
        ]);

        $errors = '';
        $i_max = count($cCentros);
        for ($i = 0; $i < $i_max; $i++) {
            $oActual = $cCentros[$i];
            if ($oActual->getId_ubi() !== $id_ubi) {
                continue;
            }
            $num_orden_actual = (int) ($oActual->getNum_orden() ?? 0);

            if ($direccion === 'mas' && $i >= 1) {
                $oAnterior = $cCentros[$i - 1];
                $num_orden_anterior = (int) ($oAnterior->getNum_orden() ?? 0);
                $oAnterior->setNum_orden($num_orden_actual);
                if ($this->centroEncargadoRepository->Guardar($oAnterior) === false) {
                    $errors .= _("error al ordenar (1)") . ' ';
                }
                $oActual->setNum_orden($num_orden_anterior);
                if ($this->centroEncargadoRepository->Guardar($oActual) === false) {
                    $errors .= _("error al ordenar (2)") . ' ';
                }
            } elseif ($direccion === 'menos' && $i < ($i_max - 1)) {
                $oPosterior = $cCentros[$i + 1];
                $num_orden_posterior = (int) ($oPosterior->getNum_orden() ?? 0);
                $oPosterior->setNum_orden($num_orden_actual);
                if ($this->centroEncargadoRepository->Guardar($oPosterior) === false) {
                    $errors .= _("error al ordenar (3)") . ' ';
                }
                $oActual->setNum_orden($num_orden_posterior);
                if ($this->centroEncargadoRepository->Guardar($oActual) === false) {
                    $errors .= _("error al ordenar (4)") . ' ';
                }
            }
            break;
        }
        return trim($errors);
    }
}
