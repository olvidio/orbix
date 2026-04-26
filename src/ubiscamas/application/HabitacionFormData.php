<?php

namespace src\ubiscamas\application;

use frontend\shared\web\Posicion;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use web\Hash;

/**
 * Datos para `frontend/ubiscamas/controller/habitacion_form.php`.
 */
final class HabitacionFormData
{
    private const POSICION_SCRIPT = '/frontend/ubiscamas/controller/habitacion_form.php';

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $Qnuevo = (string)($input['nuevo'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
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

        if (empty($Qnuevo)) {
            $a_sel = isset($input['sel']) ? (array)$input['sel'] : [];
            if ($a_sel !== []) {
                $Qid_habitacion = strtok((string)($a_sel[0] ?? ''), '#');
            } else {
                $Qid_habitacion = (string)($input['id_habitacion'] ?? '');
            }

            $stack = '';
            if (isset($input['stack']) && (string)$input['stack'] !== '') {
                $stack = (string)filter_var($input['stack'], FILTER_SANITIZE_NUMBER_INT);
            }
            if ($stack !== '') {
                $oPosicion2 = new Posicion(self::POSICION_SCRIPT, $input);
                if ($oPosicion2->goStack($stack)) {
                    $oPosicion2->olvidar($stack);
                }
            }

            $HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
            $uuid_habitacion = HabitacionId::fromNullableString($Qid_habitacion);
            $oHabitacion = $uuid_habitacion !== null ? $HabitacionRepository->findById($uuid_habitacion) : null;
            if (!empty($oHabitacion)) {
                $Qid_ubi = $oHabitacion->getIdUbiVo();
                $orden = $oHabitacion->getOrdenVo()?->value() ?? 0;
                $nombre = $oHabitacion->getNombreVo()?->value() ?? '';
                $numero_camas = $oHabitacion->getNumeroCamasVo()?->value();
                $numero_camas_vip = $oHabitacion->getNumeroCamasVipVo()?->value();
                $planta = $oHabitacion->getPlantaVo()?->value() ?? '';
                $sillon = $oHabitacion->isSillon() ?? false;
                $adaptada = $oHabitacion->isAdaptada() ?? false;
                $observaciones = $oHabitacion->getObservacionesVo()?->value() ?? '';
                $despacho = $oHabitacion->isDespacho() ?? false;
                $tipoLavabo = $oHabitacion->getTipoLavaboVo()?->value();

                $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
                $a_camas = $CamaRepository->getCamasByHabitacion($uuid_habitacion);
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

            $HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
            $aLastHabitacion = $HabitacionRepository->getHabitaciones(['id_ubi' => $Qid_ubi, '_ordre' => 'orden DESC', '_limit' => 1]);
            if (!empty($aLastHabitacion)) {
                $oLastHabitacion = current($aLastHabitacion);
                $orden = (int)($oLastHabitacion->getOrdenVo()?->value() ?? 0) + 10;
            } else {
                $orden = 10;
            }
        }

        $a_tipos_tipoLavabo = TipoLavabo::getArrayTipoLavabo();

        $camposForm = 'orden!nombre!numero_camas!numero_camas_vip!planta!sillon!adaptada!observaciones!despacho!tipoLavabo';
        $camposChk = 'sillon!adaptada!despacho';
        $camposNo = 'new_camas_desc!new_camas_larga!new_camas_vip';

        $oHash = new Hash();
        $oHash->setCamposForm($camposForm);
        $oHash->setCamposChk($camposChk);
        $oHash->setCamposNo($camposNo);
        $oHash->setArraycamposHidden([
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
            'nuevo' => $Qnuevo,
        ]);

        $oHashActualizar = new Hash();
        $oHashActualizar->setCamposNo('refresh');
        $oHashActualizar->setArraycamposHidden([
            'id_habitacion' => $Qid_habitacion,
            'id_ubi' => $Qid_ubi,
        ]);

        $url_cama_form = 'frontend/ubiscamas/controller/cama_form.php';
        $oHashCamaForm = new Hash();
        $oHashCamaForm->setUrl($url_cama_form);
        $oHashCamaForm->setCamposForm('id_ubi!id_cama!id_habitacion');

        $url_cama_delete = 'src/ubiscamas/cama_delete';
        $oHashCamaDelete = new Hash();
        $oHashCamaDelete->setUrl($url_cama_delete);
        $oHashCamaDelete->setCamposForm('id_ubi!id_cama!id_habitacion');

        return [
            'hash_form_html' => $oHash->getCamposHtml(),
            'hash_actualizar_html' => $oHashActualizar->getCamposHtml(),
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
            'url_cama_form' => $url_cama_form,
            'h_cama_form_params' => $oHashCamaForm->linkSinValParams(),
            'url_cama_delete' => $url_cama_delete,
            'h_cama_delete_params' => $oHashCamaDelete->linkSinValParams(),
        ];
    }
}
