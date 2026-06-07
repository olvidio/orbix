<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\services\EncargoDominioService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Lectura de la ficha de atencion sacerdotal de un centro.
 *
 * Puerto del antiguo `frontend/encargossacd/controller/ctr_get_ficha.php`. Devuelve
 * arrays planos/estructurados para que el controlador frontend arme `frontend\shared\web\Desplegable`
 * y la HTML sin instanciar nada de `src\`.
 */
final class CtrGetFichaData
{

    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private EncargoDominioService $dominioService,
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private EncargoTipoRepositoryInterface $encargoTipoRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi, int $seleccion_sacd): array
    {
        $f_hoy = date('Y-m-d');

        $oDominio = $this->dominioService;

        [$chk_prelatura, $chk_de_paso, $chk_sssc, $aOpcionesSacd] =
            $this->personaSacdRepository->getArraySacdyCheckBox($seleccion_sacd);
        $aOpcionesSacdSssc = null;

        $oCentroDl = $this->centroDlRepository->findById($id_ubi);
        $tipo_centro = $oCentroDl !== null ? (string)$oCentroDl->getTipo_ctr() : '';

        $cEncargos = $this->encargoRepository->getEncargos(
            ['id_ubi' => $id_ubi, 'id_tipo_enc' => '(1|2|3).0'],
            ['id_tipo_enc' => '~'],
        );

        $encargos = [];

        if ($cEncargos === []) {
            $id_ubi_txt = (string)$id_ubi;
            $cl_checked = ((int)($id_ubi_txt[0] ?? 0) === 2) ? '' : 'checked';

            $encargos[] = self::encargoVacio($cl_checked);
            $mod = 'nuevo';
        } else {
            $mod = 'editar';
            foreach ($cEncargos as $oEncargo) {
                $id_tipo_enc = (int)$oEncargo->getId_tipo_enc();
                $oEncargoTipo = $this->encargoTipoRepository->findById($id_tipo_enc);
                $mod_horario = $oEncargoTipo !== null ? (int)$oEncargoTipo->getMod_horario() : 0;
                $id_enc = (int)$oEncargo->getId_enc();

                $encargo = self::encargoVacio('');
                $encargo['id_enc'] = $id_enc;
                $encargo['id_tipo_enc'] = $id_tipo_enc;
                $encargo['mod_horario'] = $mod_horario;
                $encargo['desc_enc'] = (string)($oEncargo->getDesc_enc() ?? '');
                $encargo['observ'] = (string)($oEncargo->getObserv() ?? '');

                $cHorarios = $this->cargarHorariosEncargo($this->encargoHorarioRepository, $id_enc, $f_hoy);
                if ($mod_horario !== 3) {
                    foreach ($cHorarios as $oHor) {
                        $modulo = $oHor->getDia_ref();
                        $val = (string)($oHor->getDia_inc() ?? '');
                        if ($modulo === 'm') {
                            $encargo['dedic_ctr_m'] = $val;
                        } elseif ($modulo === 't') {
                            $encargo['dedic_ctr_t'] = $val;
                        } elseif ($modulo === 'v') {
                            $encargo['dedic_ctr_v'] = $val;
                        }
                    }
                }

                $cSacd = $this->cargarEncargosSacd($this->encargoSacdRepository, $id_enc, $f_hoy);
                $s = 0;
                foreach ($cSacd as $oEncargoSacd) {
                    $modo = (int)$oEncargoSacd->getModo();
                    switch ($modo) {
                        case 2:
                            $encargo['cl_checked'] = 'checked';
                            // fallthrough
                        case 3:
                            $encargo['actual_id_sacd_titular'] = (int)$oEncargoSacd->getId_nom();
                            $this->rellenarHorarioTitular(
                                $this->encargoSacdHorarioRepository,
                                $oDominio,
                                $encargo,
                                $id_enc,
                                (int)$encargo['actual_id_sacd_titular'],
                                $mod_horario,
                            );
                            break;
                        case 4:
                            $encargo['actual_id_sacd_suplente'] = (int)$oEncargoSacd->getId_nom();
                            break;
                        case 5:
                            $s++;
                            $id_nom = (int)$oEncargoSacd->getId_nom();
                            $colab = $this->construirColaborador(
                                $this->encargoSacdHorarioRepository,
                                $oDominio,
                                $id_enc,
                                $id_nom,
                                $mod_horario,
                                $s,
                            );
                            $encargo['dedic_m'] = self::assignAtIndex($encargo['dedic_m'], $s, $colab['dedic_m']);
                            $encargo['dedic_t'] = self::assignAtIndex($encargo['dedic_t'], $s, $colab['dedic_t']);
                            $encargo['dedic_v'] = self::assignAtIndex($encargo['dedic_v'], $s, $colab['dedic_v']);
                            $encargo['dedic_sacd'] = self::assignAtIndex($encargo['dedic_sacd'], $s, $colab['dedic_sacd']);

                            if (!array_key_exists($id_nom, $aOpcionesSacd)) {
                                if ($aOpcionesSacdSssc === null) {
                                    [, , , $aOpcionesSacdSssc] = $this->personaSacdRepository->getArraySacdyCheckBox(10);
                                }
                            }
                            $encargo['colaboradores'][] = [
                                's' => $s,
                                'id_nom' => $id_nom,
                                'necesita_sssc' => !array_key_exists($id_nom, $aOpcionesSacd),
                            ];
                            break;
                    }
                }

                $encargo['sacd_num'] = 1 + $s;
                $encargos[] = $encargo;
            }
        }

        $num_enc = count($encargos);

        return [
            'mod' => $mod,
            'tipo_centro' => $tipo_centro,
            'num_enc' => $num_enc,
            'chk_prelatura' => (string)$chk_prelatura,
            'chk_de_paso' => (string)$chk_de_paso,
            'chk_sssc' => (string)$chk_sssc,
            'opciones_sacd' => self::arrayStringKeyed($aOpcionesSacd),
            'opciones_sacd_sssc' => $aOpcionesSacdSssc !== null ? self::arrayStringKeyed($aOpcionesSacdSssc) : null,
            'encargos' => $encargos,
            'perm_des' => self::tienePermDes(),
        ];
    }

    /**
     * @return array{
     *     id_enc: int,
     *     id_tipo_enc: int,
     *     mod_horario: int,
     *     desc_enc: string,
     *     observ: string,
     *     cl_checked: string,
     *     actual_id_sacd_titular: int,
     *     actual_id_sacd_suplente: int,
     *     dedic_ctr_m: string,
     *     dedic_ctr_t: string,
     *     dedic_ctr_v: string,
     *     dedic_m: array<int, string>,
     *     dedic_t: array<int, string>,
     *     dedic_v: array<int, string>,
     *     dedic_sacd: array<int, string>,
     *     colaboradores: list<array<string, mixed>>,
     *     sacd_num: int
     * }
     */
    private static function encargoVacio(string $cl_checked): array
    {
        return [
            'id_enc' => 0,
            'id_tipo_enc' => 0,
            'mod_horario' => 0,
            'desc_enc' => '',
            'observ' => '',
            'cl_checked' => $cl_checked,
            'actual_id_sacd_titular' => 0,
            'actual_id_sacd_suplente' => 0,
            'dedic_ctr_m' => '',
            'dedic_ctr_t' => '',
            'dedic_ctr_v' => '',
            'dedic_m' => [0 => ''],
            'dedic_t' => [0 => ''],
            'dedic_v' => [0 => ''],
            'dedic_sacd' => [0 => ''],
            'colaboradores' => [],
            'sacd_num' => 1,
        ];
    }

    /**
     * @return list<\src\encargossacd\domain\entity\EncargoHorario>
     */
    private function cargarHorariosEncargo(
        EncargoHorarioRepositoryInterface $repo,
        int $id_enc,
        string $f_hoy,
    ): array {
        $base = ['id_enc' => $id_enc, '_ordre' => 'f_ini DESC'];

        $aWhere0 = $base + ['f_fin' => 'x'];
        $cHor0 = $repo->getEncargoHorarios($aWhere0, ['f_fin' => 'IS NULL']);

        $aWhere1 = $base + ['f_fin' => "'$f_hoy'"];
        $cHor1 = $repo->getEncargoHorarios($aWhere1, ['f_fin' => '>']);

        return array_merge($cHor0, $cHor1);
    }

    /**
     * @return list<\src\encargossacd\domain\entity\EncargoSacd>
     */
    private function cargarEncargosSacd(
        EncargoSacdRepositoryInterface $repo,
        int $id_enc,
        string $f_hoy,
    ): array {
        $base = ['id_enc' => $id_enc, '_ordre' => 'modo,f_ini DESC'];

        $aWhere0 = $base + ['f_fin' => 'x'];
        $c0 = $repo->getEncargosSacd($aWhere0, ['f_fin' => 'IS NULL']);

        $aWhere1 = $base + ['f_fin' => "'$f_hoy'"];
        $c1 = $repo->getEncargosSacd($aWhere1, ['f_fin' => '>']);

        return array_merge($c0, $c1);
    }

    /**
     * @param array{
     *     id_enc: int,
     *     id_tipo_enc: int,
     *     mod_horario: int,
     *     desc_enc: string,
     *     observ: string,
     *     cl_checked: string,
     *     actual_id_sacd_titular: int,
     *     actual_id_sacd_suplente: int,
     *     dedic_ctr_m: string,
     *     dedic_ctr_t: string,
     *     dedic_ctr_v: string,
     *     dedic_m: array<int, string>,
     *     dedic_t: array<int, string>,
     *     dedic_v: array<int, string>,
     *     dedic_sacd: array<int, string>,
     *     colaboradores: list<array<string, mixed>>,
     *     sacd_num: int
     * } $encargo
     */
    private function rellenarHorarioTitular(
        EncargoSacdHorarioRepositoryInterface $repo,
        EncargoDominioService $oDominio,
        array &$encargo,
        int $id_enc,
        int $id_nom,
        int $mod_horario,
    ): void {
        $cH = $repo->getEncargoSacdHorarios(
            [
                'id_enc' => $id_enc,
                'id_nom' => $id_nom,
                'f_fin' => 'x',
                '_ordre' => 'f_ini DESC',
            ],
            ['f_fin' => 'IS NULL'],
        );
        if ($cH === []) {
            return;
        }

        if ($mod_horario === 3) {
            $txt = '';
            $h = 0;
            foreach ($cH as $oH) {
                $h++;
                $parcial = $oDominio->texto_horario(
                    (string) ($oH->getMas_menos() ?? ''),
                    (string) ($oH->getDia_ref() ?? ''),
                    (string) ($oH->getDia_inc() ?? ''),
                    (string) ($oH->getDia_num() ?? ''),
                    $this->timeToString($oH->getH_ini()),
                    $this->timeToString($oH->getH_fin()),
                    '',
                );
                if ($h > 1) {
                    $txt .= ' y ';
                }
                $txt .= $parcial;
            }
            $encargo['dedic_sacd'] = self::assignAtIndex($encargo['dedic_sacd'], 0, $txt === '' ? _('crear horario') : $txt);

            return;
        }

        foreach ($cH as $oH) {
            $modulo = $oH->getDia_ref();
            $val = (string)($oH->getDia_inc() ?? '');
            if ($modulo === 'm') {
                $encargo['dedic_m'] = self::assignAtIndex($encargo['dedic_m'], 0, $val);
            } elseif ($modulo === 't') {
                $encargo['dedic_t'] = self::assignAtIndex($encargo['dedic_t'], 0, $val);
            } elseif ($modulo === 'v') {
                $encargo['dedic_v'] = self::assignAtIndex($encargo['dedic_v'], 0, $val);
            }
        }
    }

    /**
     * @return array{dedic_m: string, dedic_t: string, dedic_v: string, dedic_sacd: string}
     */
    private function construirColaborador(
        EncargoSacdHorarioRepositoryInterface $repo,
        EncargoDominioService $oDominio,
        int $id_enc,
        int $id_nom,
        int $mod_horario,
        int $s,
    ): array {
        $out = [
            'dedic_m' => '',
            'dedic_t' => '',
            'dedic_v' => '',
            'dedic_sacd' => '',
        ];

        $cH = $repo->getEncargoSacdHorarios(
            [
                'id_enc' => $id_enc,
                'id_nom' => $id_nom,
                'f_fin' => 'x',
                '_ordre' => 'f_ini DESC',
            ],
            ['f_fin' => 'IS NULL'],
        );
        if ($cH === []) {
            return $out;
        }

        if ($mod_horario === 3) {
            $txt = '';
            $h = 0;
            foreach ($cH as $oH) {
                $h++;
                $parcial = $oDominio->texto_horario(
                    (string) ($oH->getMas_menos() ?? ''),
                    (string) ($oH->getDia_ref() ?? ''),
                    (string) ($oH->getDia_inc() ?? ''),
                    (string) ($oH->getDia_num() ?? ''),
                    $this->timeToString($oH->getH_ini()),
                    $this->timeToString($oH->getH_fin()),
                    '',
                );
                if ($h > 1) {
                    $txt .= ' y ';
                }
                $txt .= $parcial;
            }
            $out['dedic_sacd'] = $txt === '' ? _('crear horario') : $txt;

            return $out;
        }

        foreach ($cH as $oH) {
            $modulo = $oH->getDia_ref();
            $val = (string)($oH->getDia_inc() ?? '');
            if ($modulo === 'm') {
                $out['dedic_m'] = $val;
            } elseif ($modulo === 't') {
                $out['dedic_t'] = $val;
            } elseif ($modulo === 'v') {
                $out['dedic_v'] = $val;
            }
        }

        return $out;
    }

    private function timeToString(mixed $time): string
    {
        if ($time === null) {
            return '';
        }
        return is_scalar($time) || (is_object($time) && method_exists($time, '__toString'))
            ? (string) $time
            : (is_object($time) && method_exists($time, 'value') ? (string) $time->value() : '');
    }

    /**
     * @param array<int|string, mixed> $in
     * @return array<string, string>
     */
    private function arrayStringKeyed(array $in): array
    {
        $out = [];
        foreach ($in as $k => $v) {
            $out[(string) $k] = is_scalar($v) ? (string) $v : '';
        }

        return $out;
    }

    /**
     * @param array<int, string> $values
     * @return array<int, string>
     */
    /**
     * @param array<int, string>|mixed $values
     * @return array<int, string>
     */
    private static function assignAtIndex(mixed $values, int $index, string $value): array
    {
        $list = is_array($values) ? $values : [0 => ''];
        $list[$index] = $value;
        $out = [];
        foreach ($list as $k => $v) {
            $out[(int) $k] = is_scalar($v) ? (string) $v : '';
        }

        return $out;
    }

    private function tienePermDes(): bool
    {
        if (empty($_SESSION['oPerm'])) {
            return false;
        }
        $oPerm = $_SESSION['oPerm'];
        if (!is_object($oPerm) || !method_exists($oPerm, 'have_perm_oficina')) {
            return false;
        }

        return $oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd');
    }
}
