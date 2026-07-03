<?php

declare(strict_types=1);

namespace frontend\misas\support;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\web\PeriodoQue;

/**
 * Helper para construir el `<td>` HTML del desplegable "Período" usado en las
 * pantallas de búsqueda / cambio de estado del módulo misas.
 *
 * Centraliza el bloque de configuración de `PeriodoQue` (título, opciones,
 * opción seleccionada, fechas por defecto) que se repetía en 6 controladores
 * frontend idénticos salvo por las opciones y la opción inicial.
 */
class PeriodoTdHelper
{
    /**
     * @param array<string, string> $opciones  lista de claves-valores del desplegable
     *                                         (por ejemplo `['esta_semana' => _('esta semana'), ...]`).
     * @param string                $selected  clave del desplegable seleccionada por defecto.
     */
    public static function build(array $opciones, string $selected): string
    {
        $oFormP = new PeriodoQue();
        $oFormP->setFormName('frm_nuevo_periodo');
        $oFormP->setTitulo(FuncTablasSupport::strtoupperDlb(_('seleccionar un periodo')));
        $oFormP->setPosiblesPeriodos($opciones);
        $oFormP->setDesplPeriodosOpcion_sel($selected);
        $oFormP->setisDesplAnysVisible(false);

        $shoy = date('d/m/Y');
        $oFormP->setEmpiezaMin($shoy);
        $oFormP->setEmpiezaMax($shoy);

        return $oFormP->getTd();
    }
}
