<?php

namespace src\ubiscamas\application;

use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\TipoLavabo;

/**
 * Datos para `frontend/ubiscamas/controller/habitacion_form.php`.
 * La composición de `HashFront` ocurre en {@see \frontend\ubiscamas\helpers\UbiscamasFormHashCompose::habitacionForm}.
 */
final class HabitacionFormData
{
    public function __construct(
        private HabitacionDlRepositoryInterface $habitacionRepository,
        private CamaDlRepositoryInterface $camaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qnuevo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nuevo');
        $Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $orden = '';
        $nombre = '';
        $numero_camas = '';
        $numero_camas_vip = '';
        $planta = '';
        $sillon = false;
        $adaptada = false;
        $observaciones = '';
        $despacho = false;
        $tipoLavabo = null;
        $a_camas_rows = [];
        $Qid_habitacion = '';

        if ($Qnuevo === '') {
            $a_sel = isset($input['sel']) && is_array($input['sel']) ? $input['sel'] : [];
            if ($a_sel !== []) {
                $firstSel = $a_sel[0];
                $Qid_habitacion = strtok(is_scalar($firstSel) ? (string) $firstSel : '', '#') ?: '';
            } else {
                $Qid_habitacion = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_habitacion');
            }

            $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
            $oHabitacion = $uuid_habitacion !== null
                ? $this->habitacionRepository->findById($uuid_habitacion->value())
                : null;
            if ($oHabitacion !== null) {
                $Qid_ubi = $oHabitacion->getIdUbiVo();
                $orden = $oHabitacion->getOrdenVo()->value();
                $nombre = $oHabitacion->getNombreVo()?->value() ?? '';
                $numero_camas = $oHabitacion->getNumeroCamasVo()?->value();
                $numero_camas_vip = $oHabitacion->getNumeroCamasVipVo()?->value();
                $planta = $oHabitacion->getPlantaVo()?->value() ?? '';
                $sillon = $oHabitacion->isSillon() ?? false;
                $adaptada = $oHabitacion->isAdaptada() ?? false;
                $observaciones = $oHabitacion->getObservacionesVo()?->value() ?? '';
                $despacho = $oHabitacion->isDespacho() ?? false;
                $tipoLavabo = $oHabitacion->getTipoLavaboVo()?->value();

                $a_camas = $this->camaRepository->getCamasByHabitacion($uuid_habitacion);
                foreach ($a_camas as $oCama) {
                    $a_camas_rows[] = [
                        'id_cama' => $oCama->getIdCama(),
                        'descripcion' => $oCama->getDescripcion(),
                        'larga' => (bool)$oCama->isLarga(),
                        'vip' => (bool)$oCama->isVip(),
                    ];
                }
            }
        }

        if ($Qid_habitacion === '') {
            $numero_camas = 1;
            $numero_camas_vip = 1;

            $aLastHabitacion = $this->habitacionRepository->getHabitaciones([
                'id_ubi' => $Qid_ubi,
                '_ordre' => 'orden DESC',
                '_limit' => 1,
            ]);
            if ($aLastHabitacion !== []) {
                $oLastHabitacion = current($aLastHabitacion);
                $orden = $oLastHabitacion->getOrdenVo()->value() + 10;
            } else {
                $orden = 10;
            }
        }

        $a_tipos_tipoLavabo = TipoLavabo::getArrayTipoLavabo();

        $camposForm = 'orden!nombre!numero_camas!numero_camas_vip!planta!sillon!adaptada!observaciones!despacho!tipoLavabo';
        $camposChk = 'sillon!adaptada!despacho';
        $camposNo = 'new_camas_desc!new_camas_larga!new_camas_vip';

        return [
            'hash_form' => [
                'campos_form' => $camposForm,
                'campos_chk' => $camposChk,
                'campos_no' => $camposNo,
                'campos_hidden' => [
                    'id_habitacion' => $Qid_habitacion,
                    'id_ubi' => $Qid_ubi,
                    'nuevo' => $Qnuevo,
                ],
            ],
            'hash_actualizar' => [
                'campos_no' => 'refresh',
                'campos_hidden' => [
                    'id_habitacion' => $Qid_habitacion,
                    'id_ubi' => $Qid_ubi,
                ],
            ],
            'cama_form_hash' => [
                'url' => 'frontend/ubiscamas/controller/cama_form.php',
                'campos_form' => 'id_ubi!id_cama!id_habitacion',
            ],
            'cama_delete_hash' => [
                'url' => 'src/ubiscamas/cama_delete',
                'campos_form' => 'id_ubi!id_cama!id_habitacion',
            ],
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
            'orden' => $orden,
            'nombre' => $nombre,
            'numero_camas' => $numero_camas,
            'numero_camas_vip' => $numero_camas_vip,
            'planta' => $planta,
            'sillon' => $sillon,
            'adaptada' => $adaptada,
            'observaciones' => $observaciones,
            'despacho' => $despacho,
            'tipoLavabo' => $tipoLavabo,
            'a_tipos_tipoLavabo' => $a_tipos_tipoLavabo,
            'a_camas' => $a_camas_rows,
        ];
    }
}
