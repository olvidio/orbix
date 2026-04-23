<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;

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
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $direccion = (string)($input['num_orden'] ?? '');

        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("faltan parametros id_activ / id_ubi");
        }
        if ($direccion !== 'mas' && $direccion !== 'menos') {
            return _("direccion de orden incorrecta (mas / menos)");
        }

        $repo = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $cCentros = $repo->getCentrosEncargados([
            'id_activ' => $id_activ,
            '_ordre' => 'num_orden',
        ]);

        $errors = '';
        $i_max = is_array($cCentros) ? count($cCentros) : 0;
        for ($i = 0; $i < $i_max; $i++) {
            if ($cCentros[$i]->getId_ubi() !== $id_ubi) {
                continue;
            }
            $num_orden_actual = (int)$cCentros[$i]->getNum_orden();

            if ($direccion === 'mas' && $i >= 1) {
                $oAnterior = $cCentros[$i - 1];
                $num_orden_anterior = (int)$oAnterior->getNum_orden();
                $oAnterior->setNum_orden($num_orden_actual);
                if ($repo->Guardar($oAnterior) === false) {
                    $errors .= _("error al ordenar (1)") . ' ';
                }
                $oActual = $cCentros[$i];
                $oActual->setNum_orden($num_orden_anterior);
                if ($repo->Guardar($oActual) === false) {
                    $errors .= _("error al ordenar (2)") . ' ';
                }
            } elseif ($direccion === 'menos' && $i < ($i_max - 1)) {
                $oPosterior = $cCentros[$i + 1];
                $num_orden_posterior = (int)$oPosterior->getNum_orden();
                $oPosterior->setNum_orden($num_orden_actual);
                if ($repo->Guardar($oPosterior) === false) {
                    $errors .= _("error al ordenar (3)") . ' ';
                }
                $oActual = $cCentros[$i];
                $oActual->setNum_orden($num_orden_posterior);
                if ($repo->Guardar($oActual) === false) {
                    $errors .= _("error al ordenar (4)") . ' ';
                }
            }
            break;
        }
        return trim($errors);
    }
}
