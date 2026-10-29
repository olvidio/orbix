<?php

namespace src\encargossacd\application\services;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoSacdHorarioField;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdHorarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;

/**
 * Texto de dedicación horaria (m/t/v) para la pantalla de propuestas.
 */
final class PropuestasHorarioTexto
{
    public function __construct(
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private PropuestaEncargoSacdHorarioRepositoryInterface $propuestaHorarioRepository,
    ) {
    }

    public function actualTxt(int $id_enc, int $id_sacd): string
    {
        if ($id_enc <= 0 || $id_sacd <= 0) {
            return '';
        }

        return $this->formatTxt($this->dedicacionActual($id_enc, $id_sacd));
    }

    public function propuestaTxt(int $id_enc, int $id_sacd): string
    {
        if ($id_enc <= 0 || $id_sacd <= 0) {
            return '';
        }

        return $this->formatTxt($this->dedicacionPropuesta($id_enc, $id_sacd));
    }

    /**
     * @return array{m: int, t: int, v: int}
     */
    private function dedicacionActual(int $id_enc, int $id_sacd): array
    {
        return $this->dedicacionDesdeHorarios(
            $this->encargoSacdHorarioRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(EncargoSacdHorarioField::IdEnc, $id_enc))
                    ->andWhere(Filter::eq(EncargoSacdHorarioField::IdNom, $id_sacd))
                    ->andWhere(Filter::isNull(EncargoSacdHorarioField::FFin))
                    ->orderBy(EncargoSacdHorarioField::FIni, OrderDirection::Desc)
            ),
        );
    }

    /**
     * @return array{m: int, t: int, v: int}
     */
    private function dedicacionPropuesta(int $id_enc, int $id_sacd): array
    {
        return $this->dedicacionDesdeHorarios(
            $this->propuestaHorarioRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdEnc, $id_enc))
                    ->andWhere(Filter::eq(PropuestaEncargoSacdHorarioField::IdNom, $id_sacd))
                    ->andWhere(Filter::isNull(PropuestaEncargoSacdHorarioField::FFin))
                    ->orderBy(PropuestaEncargoSacdHorarioField::FIni, OrderDirection::Desc)
            ),
        );
    }

    /**
     * @param list<\src\encargossacd\domain\entity\EncargoSacdHorario> $horarios
     * @return array{m: int, t: int, v: int}
     */
    private function dedicacionDesdeHorarios(array $horarios): array
    {
        $dedic_m = 0;
        $dedic_t = 0;
        $dedic_v = 0;
        foreach ($horarios as $oHorario) {
            $modulo = (string) ($oHorario->getDia_ref() ?? '');
            match ($modulo) {
                'm' => $dedic_m = (int) ($oHorario->getDia_inc() ?? 0),
                't' => $dedic_t = (int) ($oHorario->getDia_inc() ?? 0),
                'v' => $dedic_v = (int) ($oHorario->getDia_inc() ?? 0),
                default => null,
            };
        }

        return ['m' => $dedic_m, 't' => $dedic_t, 'v' => $dedic_v];
    }

    /**
     * @param array{m: int, t: int, v: int} $dedicacion
     */
    private function formatTxt(array $dedicacion): string
    {
        $m = $dedicacion['m'];
        $t = $dedicacion['t'];
        $v = $dedicacion['v'];

        $html = '';
        $html .= _("m") . ':';
        $html .= "[$m]";
        $html .= '; ';
        $html .= _("t1") . ':';
        $html .= "[$t]";
        $html .= '; ';
        $html .= _("t2") . ':';
        $html .= "[$v]";

        return $html;
    }
}
