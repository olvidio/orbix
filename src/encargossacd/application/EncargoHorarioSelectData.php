<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\services\EncargoDominioService;

/**
 * Datos para la lista de horarios de un encargo (`encargo_horario_select`).
 *
 * Se devuelven ya precalculados el texto descriptivo del horario y las fechas
 * formateadas para que el frontend solo arme `frontend\shared\web\Lista`.
 */
final class EncargoHorarioSelectData
{

    public function __construct(
        private EncargoDominioService $dominioService,
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private EncargoRepositoryInterface $encargoRepository
    ) {
    }

    /**
     * @return array{
     *     desc_enc: string,
     *     filas: list<array{
     *         id_enc: int,
     *         id_item_h: int,
     *         dia_num: string,
     *         dia_ref: string,
     *         mas_menos: string,
     *         dia_inc: string,
     *         h_ini: string,
     *         h_fin: string,
     *         n_sacd: string,
     *         mes: string,
     *         f_ini: ?string,
     *         f_fin: ?string,
     *         excep: string,
     *         texto_horario: string
     *     }>
     * }
     */
    public function execute(int $id_enc): array
    {
        $oDominio = $this->dominioService;

        $desc_enc = '';
        $oEncargo = $this->encargoRepository->findByID($id_enc);
        if ($oEncargo !== null) {
            $desc_enc = (string)$oEncargo->getDesc_enc();
        }

        $cEncargoHorarios = $this->encargoHorarioRepository->getEncargoHorarios(['id_enc' => $id_enc]);

        $filas = [];
        if ($cEncargoHorarios !== []) {
            foreach ($cEncargoHorarios as $oH) {
                $mas_menos = (string)$oH->getMas_menos();
                $dia_ref = (string)$oH->getDia_ref();
                $dia_inc = (string)$oH->getDia_inc();
                $dia_num = (string)$oH->getDia_num();
                $h_ini = self::fmtTime($oH->getH_ini());
                $h_fin = self::fmtTime($oH->getH_fin());
                $n_sacd = (string)$oH->getN_sacd();

                $f_ini_raw = $oH->getF_ini();
                $f_fin_raw = $oH->getF_fin();

                $filas[] = [
                    'id_enc' => (int)$oH->getId_enc(),
                    'id_item_h' => (int)$oH->getId_item_h(),
                    'dia_num' => $dia_num,
                    'dia_ref' => $dia_ref,
                    'mas_menos' => $mas_menos,
                    'dia_inc' => $dia_inc,
                    'h_ini' => $h_ini,
                    'h_fin' => $h_fin,
                    'n_sacd' => $n_sacd,
                    'mes' => (string)$oH->getMes(),
                    'f_ini' => empty($f_ini_raw) ? null : (string)$f_ini_raw->getFromLocal(),
                    'f_fin' => empty($f_fin_raw) ? null : (string)$f_fin_raw->getFromLocal(),
                    'excep' => '',
                    'texto_horario' => (string)$oDominio->texto_horario(
                        $mas_menos,
                        $dia_ref,
                        $dia_inc,
                        $dia_num,
                        $h_ini,
                        $h_fin,
                        $n_sacd,
                    ),
                ];
            }
        }

        return [
            'desc_enc' => $desc_enc,
            'filas' => $filas,
        ];
    }

    private static function fmtTime(mixed $time): string
    {
        if ($time === null) {
            return '';
        }
        if (is_object($time) && method_exists($time, 'value')) {
            return (string) $time->value();
        }

        return is_scalar($time) ? (string) $time : '';
    }
}
