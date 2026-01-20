<?php

namespace src\encargossacd\domain\services;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;

class EncargoDominioService
{
    public function calcular_dia($mas_menos, $dia_ref, $dia_inc)
    {
        $dia = empty($mas_menos) ? $dia_ref : '';
        if (!empty($dia_inc) && empty($dia)) {
            if ($mas_menos === "-") {
                $dia = $dia_ref - $dia_inc;
                if ($dia < 0) {
                    $dia = 7 + $dia;
                }
            }
            if ($mas_menos === "+") {
                $dia = $dia_ref + $dia_inc;
                if ($dia > 7) {
                    $dia = $dia - 7;
                }
            }
        }
        return $dia;
    }

    public function dedicacion_horas($id_nom, $id_enc)
    {
        $EncargoSacdHorarioRepository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $aWhere['id_enc'] = $id_enc;
        $aWhere['id_nom'] = $id_nom;
        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $cEncargoSacdHorario = $EncargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if (is_array($cEncargoSacdHorario) && count($cEncargoSacdHorario) == 0) {
            return false;
        }
        $dedic_h = 0;
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $dia_inc = $oEncargoSacdHorario->getDia_inc();
            switch ($oEncargoSacdHorario->getDiaRefVo()->value()) {
                case "m":
                    // supongo que la mañana es de 5 horas (para que dé 35 h/semana)
                    $dedic_h += $dia_inc * 5;
                    break;
                case "t":
                    $dedic_h += $dia_inc * 2;
                    break;
                case "v":
                    $dedic_h += $dia_inc * 3;
                    break;
            }
        }
        return $dedic_h;
    }

    public function texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd = '')
    {
        $texto_horario = '';
        // texto que describe el horario original
        $dia_txt = '';
        $dia = $this->calcular_dia($mas_menos, $dia_ref, $dia_inc);
        if (empty($mas_menos)) {
            if (!empty($dia_num)) {
                $dia_txt = _("el") . " " . EncargoConstants::OPCIONES_ORDINALES[$dia_num] . " ";
            }
            $dia_txt .= EncargoConstants::OPCIONES_DIA_SEMANA[$dia];
        } else {
            if ($mas_menos == "-") {
                $dia_txt = EncargoConstants::OPCIONES_DIA_SEMANA[$dia] . " " . _("antes del") . " " . EncargoConstants::OPCIONES_ORDINALES[$dia_num] . " " . EncargoConstants::OPCIONES_DIA_REF[$dia_ref];
            }
            if ($mas_menos == "+") {
                $dia_txt = EncargoConstants::OPCIONES_DIA_SEMANA[$dia] . " " . _("después del") . " " . EncargoConstants::OPCIONES_ORDINALES[$dia_num] . " " . EncargoConstants::OPCIONES_DIA_REF[$dia_ref];
            }
        }
        if (!empty($dia_txt)) $texto_horario = $dia_txt . ", de " . $h_ini . " a " . $h_fin;
        if (!empty($n_sacd)) $texto_horario .= " (" . $n_sacd . " sacd)";

        return $texto_horario;
    }

    public function texto_horario_ex($mes, $f_ini, $f_fin, $horario, $mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd)
    {
        //igual que la anterior para las excepciones
        if (!empty($mes)) {
            $txt = sprintf(_("excepto el mes de %s"), EncargoConstants::OPCIONES_MES[$mes]);
        } else {
            $txt = sprintf(_("excpeto del %s al %s"), $f_ini, $f_fin);
        }

        if ($horario === "t") {
            $texto_h = $this->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
            $txt .= " " . _("que se cambia a") . ": " . $texto_h;
        } else {
            $txt .= " " . _("que se anula");
        }
        return $txt;
    }

    public function db_txt_h_sacd($id_enc, $id_nom)
    {
        $oDbl = $GLOBALS['oDBE'];
        $sql = "SELECT * FROM encargo_sacd_horario WHERE id_enc=$id_enc AND id_nom=$id_nom";
        $oDBSt_q_h = $oDbl->query($sql);
        $txt = "";
        $h = 0;
        foreach ($oDBSt_q_h->fetchAll() as $row_h) {
            $h++;
            extract($row_h);
            if ($h > 1) $txt .= " " . _("y") . " ";
            $txt .= $this->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
        }
        return $txt;
    }
}
