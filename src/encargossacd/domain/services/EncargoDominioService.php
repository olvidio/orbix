<?php

namespace src\encargossacd\domain\services;

use PDO;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\shared\infrastructure\GlobalPdo;

class EncargoDominioService
{
    public function __construct(
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
    ) {
    }

    public function calcular_dia(string $mas_menos, int|string $dia_ref, int|string|null $dia_inc): int|string
    {
        $dia = $mas_menos === '' ? $dia_ref : '';
        if ($dia_inc !== null && $dia_inc !== '' && $dia_inc !== 0 && $dia === '') {
            $diaRefNum = is_numeric($dia_ref) ? (int) $dia_ref : 0;
            $diaIncNum = is_numeric($dia_inc) ? (int) $dia_inc : 0;
            if ($mas_menos === '-') {
                $dia = $diaRefNum - $diaIncNum;
                if ($dia < 0) {
                    $dia = 7 + $dia;
                }
            }
            if ($mas_menos === '+') {
                $dia = $diaRefNum + $diaIncNum;
                if ($dia > 7) {
                    $dia = $dia - 7;
                }
            }
        }

        return $dia;
    }

    public function dedicacion_horas(int $id_nom, int $id_enc): int|false
    {
        $aWhere = ['id_enc' => $id_enc, 'id_nom' => $id_nom, 'f_fin' => 'x'];
        $aOperador = ['f_fin' => 'IS NULL'];
        $cEncargoSacdHorario = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhere, $aOperador);
        if ($cEncargoSacdHorario === []) {
            return false;
        }
        $dedic_h = 0;
        foreach ($cEncargoSacdHorario as $oEncargoSacdHorario) {
            $dia_inc = (int) ($oEncargoSacdHorario->getDia_inc() ?? 0);
            switch ($oEncargoSacdHorario->getDiaRefVo()?->value()) {
                case 'm':
                    $dedic_h += $dia_inc * 5;
                    break;
                case 't':
                    $dedic_h += $dia_inc * 2;
                    break;
                case 'v':
                    $dedic_h += $dia_inc * 3;
                    break;
            }
        }

        return $dedic_h;
    }

    public function texto_horario(
        string $mas_menos,
        int|string $dia_ref,
        int|string $dia_inc,
        int|string $dia_num,
        string $h_ini,
        string $h_fin,
        int|string $n_sacd = '',
    ): string {
        $texto_horario = '';
        $dia_txt = '';
        $dia = $this->calcular_dia($mas_menos, $dia_ref, $dia_inc);
        $diaKey = (string) $dia;
        $diaRefKey = (string) $dia_ref;
        $diaNumKey = (string) $dia_num;
        if ($mas_menos === '') {
            if ($dia_num !== '' && $dia_num !== 0) {
                $dia_txt = _('el') . ' ' . EncargoConstants::OPCIONES_ORDINALES[$diaNumKey] . ' ';
            }
            $dia_txt .= EncargoConstants::OPCIONES_DIA_SEMANA[$diaKey] ?? '';
        } else {
            if ($mas_menos === '-') {
                $dia_txt = (EncargoConstants::OPCIONES_DIA_SEMANA[$diaKey] ?? '')
                    . ' ' . _('antes del') . ' '
                    . (EncargoConstants::OPCIONES_ORDINALES[$diaNumKey] ?? '') . ' '
                    . (EncargoConstants::OPCIONES_DIA_REF[$diaRefKey] ?? '');
            }
            if ($mas_menos === '+') {
                $dia_txt = (EncargoConstants::OPCIONES_DIA_SEMANA[$diaKey] ?? '')
                    . ' ' . _('después del') . ' '
                    . (EncargoConstants::OPCIONES_ORDINALES[$diaNumKey] ?? '') . ' '
                    . (EncargoConstants::OPCIONES_DIA_REF[$diaRefKey] ?? '');
            }
        }
        if ($dia_txt !== '') {
            $texto_horario = $dia_txt . ', de ' . $h_ini . ' a ' . $h_fin;
        }
        if ($n_sacd !== '' && $n_sacd !== 0) {
            $texto_horario .= ' (' . $n_sacd . ' sacd)';
        }

        return $texto_horario;
    }

    public function texto_horario_ex(
        int|string $mes,
        string $f_ini,
        string $f_fin,
        string $horario,
        string $mas_menos,
        int|string $dia_ref,
        int|string $dia_inc,
        int|string $dia_num,
        string $h_ini,
        string $h_fin,
        int|string $n_sacd,
    ): string {
        if ($mes !== '' && $mes !== 0) {
            $mesKey = (string) $mes;
            $txt = sprintf(_('excepto el mes de %s'), EncargoConstants::OPCIONES_MES[$mesKey] ?? '');
        } else {
            $txt = sprintf(_('excpeto del %s al %s'), $f_ini, $f_fin);
        }

        if ($horario === 't') {
            $texto_h = $this->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
            $txt .= ' ' . _('que se cambia a') . ': ' . $texto_h;
        } else {
            $txt .= ' ' . _('que se anula');
        }

        return $txt;
    }

    public function db_txt_h_sacd(int $id_enc, int $id_nom): string
    {
        $oDbl = GlobalPdo::get('oDBE');
        $sql = "SELECT * FROM encargo_sacd_horario WHERE id_enc=$id_enc AND id_nom=$id_nom";
        $oDBSt_q_h = $oDbl->query($sql);
        $txt = '';
        $h = 0;
        if ($oDBSt_q_h === false) {
            return $txt;
        }
        foreach ($oDBSt_q_h->fetchAll(PDO::FETCH_ASSOC) as $row_h) {
            if (!is_array($row_h)) {
                continue;
            }
            $h++;
            $mas_menos = is_scalar($row_h['mas_menos'] ?? null) ? (string) $row_h['mas_menos'] : '';
            $dia_ref = $this->mixedToIntOrString($row_h['dia_ref'] ?? '');
            $dia_inc = $this->mixedToIntOrString($row_h['dia_inc'] ?? '');
            $dia_num = $this->mixedToIntOrString($row_h['dia_num'] ?? '');
            $h_ini = is_scalar($row_h['h_ini'] ?? null) ? (string) $row_h['h_ini'] : '';
            $h_fin = is_scalar($row_h['h_fin'] ?? null) ? (string) $row_h['h_fin'] : '';
            $n_sacd = $this->mixedToIntOrString($row_h['n_sacd'] ?? '');
            if ($h > 1) {
                $txt .= ' ' . _('y') . ' ';
            }
            $txt .= $this->texto_horario($mas_menos, $dia_ref, $dia_inc, $dia_num, $h_ini, $h_fin, $n_sacd);
        }

        return $txt;
    }

    private function mixedToIntOrString(mixed $value): int|string
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return '';
    }
}
