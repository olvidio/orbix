<?php

namespace frontend\planning\support;

use frontend\shared\web\PeriodoQue;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Helper para construir los `frontend\shared\web\PeriodoQue` usados en los distintos
 * `planning_*_que.php`. Centraliza el catalogo de opciones de periodo
 * (todo el anyo + trimestres) y los valores por defecto.
 *
 * Introducido en la migracion del modulo planning (slice 2).
 */
final class PeriodoPlanningHelper
{
    /**
     * @return array<string,string>
     */
    public static function opcionesTrimestrales(): array
    {
        return [
            'tot_any' => _("todo el año"),
            'trimestre_1' => _("primer trimestre"),
            'trimestre_2' => _("segundo trimestre"),
            'trimestre_3' => _("tercer trimestre"),
            'trimestre_4' => _("cuarto trimestre"),
            'separador' => '---------',
            'otro' => _("otro"),
        ];
    }

    /**
     * Construye el `PeriodoQue` estandar de los formularios de planning.
     *
     * Espera los valores de formulario ya leidos (`$Qyear`, `$Qperiodo`,
     * `$Qempiezamin`, `$Qempiezamax`).
     */
    public static function formPeriodo(
        string $periodo,
        int|string $year,
        string $empiezaMin,
        string $empiezaMax,
        string $titulo = ''
    ): PeriodoQue {
        if ($titulo === '') {
            $titulo = FuncTablasSupport::strtoupperDlb(_("periodo del planning actividades"));
        }

        $oForm = new PeriodoQue();
        $oForm->setFormName('que');
        $oForm->setTitulo($titulo);
        $oForm->setPosiblesPeriodos(self::opcionesTrimestrales());
        $oForm->setDesplPeriodosOpcion_sel($periodo);
        $yearSel = empty($year) ? (string)date('Y') : PayloadCoercion::string($year);
        $oForm->setDesplAnysOpcion_sel($yearSel);
        $oForm->setEmpiezaMin($empiezaMin);
        $oForm->setEmpiezaMax($empiezaMax);

        return $oForm;
    }

    /**
     * Genera el texto "por defecto: periodo desde 1/... hasta 30/..." que
     * muestran los `*_que` segun el mes actual y el `mes_fin_stgr`.
     */
    public static function textoPeriodoPorDefecto(int $mesFinStgr): string
    {
        $mes = (int)date('m');
        if ($mes > $mesFinStgr) {
            return sprintf(_("(por defecto: periodo desde 1/%s hasta 31/5)"), $mesFinStgr + 1);
        }

        return sprintf(_("(por defecto: periodo desde 1/6 hasta 30/%s)"), $mesFinStgr + 1);
    }
}
