<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Cambia el tipo de una actividad existente y regenera el proceso asociado.
 * Sustituye la lógica del antiguo case `cambiar_tipo` de actividad_update.php.
 */
final class ActividadCambiarTipo
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error_txt: string, tipo_error?: string}
     */
    public function execute(array $input): array
    {
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tipo_activ');
        $Qisfsv_val = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'isfsv_val');
        $Qiasistentes_val = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'iasistentes_val');
        $Qiactividad_val = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'iactividad_val');
        $Qinom_tipo_val = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'inom_tipo_val');

        $Qdl_org = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl_org');
        $Qnum_asistentes = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'num_asistentes');
        $Qstatus = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'status');
        $Qid_repeticion = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_repeticion');
        $Qplazas = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plazas');
        $Qtarifa = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tarifa');
        $Qnom_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nom_activ');
        $Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $Qlugar_esp = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'lugar_esp');
        $Qdesc_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'desc_activ');
        $Qf_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_ini');
        $Qf_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_fin');
        $Qobserv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ');
        $Qnivel_stgr = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'nivel_stgr');
        $Qobserv_material = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ_material');
        $Qh_ini = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'h_ini');
        $Qh_fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'h_fin');

        if ($Qid_tipo_activ !== 0 && strpos((string) $Qid_tipo_activ, '.') === false) {
            $valor_id_tipo_activ = $Qid_tipo_activ;
        } else {
            $condta = $Qisfsv_val . $Qiasistentes_val . $Qiactividad_val . $Qinom_tipo_val;
            if (strpos($condta, '.') === false) {
                $valor_id_tipo_activ = (int) $condta;
            } else {
                return ['error_txt' => _("debe seleccionar un tipo de actividad"), 'tipo_error' => 'tipo'];
            }
        }

        $oActividad = $this->actividadDlRepository->findById($Qid_activ);
        if ($oActividad === null) {
            return ['error_txt' => _('actividad no encontrada')];
        }

        $oActividad->setId_tipo_activ($valor_id_tipo_activ);
        if (isset($input['dl_org'])) {
            $dl_org = strtok($Qdl_org, '#');
            $oActividad->setDl_org(is_string($dl_org) ? $dl_org : '');
        } else {
            $oActividad->setDl_org('');
        }
        $oActividad->setNom_activ($Qnom_activ);
        $oActividad->setId_ubi($Qid_ubi);
        $oActividad->setDesc_activ($Qdesc_activ);
        $oF_ini = $Qf_ini === '' ? null : DateTimeLocal::createFromLocal($Qf_ini);
        $oActividad->setF_ini($oF_ini instanceof DateTimeLocal ? $oF_ini : null);
        $oF_fin = $Qf_fin === '' ? null : DateTimeLocal::createFromLocal($Qf_fin);
        $oActividad->setF_fin($oF_fin instanceof DateTimeLocal ? $oF_fin : null);
        $oActividad->setPrecioVo(Dinero::fromInput($input['precio'] ?? null));
        $oActividad->setNum_asistentes($Qnum_asistentes);
        $oActividad->setStatus($Qstatus);
        $oActividad->setObserv($Qobserv);
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setLugar_esp($Qlugar_esp);
        $oActividad->setTarifa($Qtarifa);
        $oH_ini = $Qh_ini === '' ? null : TimeLocal::fromString($Qh_ini);
        $oActividad->setH_ini($oH_ini);
        $oH_fin = $Qh_fin === '' ? null : TimeLocal::fromString($Qh_fin);
        $oActividad->setH_fin($oH_fin);
        $oActividad->setPlazas($Qplazas);

        $error_txt = '';
        if ($this->actividadDlRepository->Guardar($oActividad) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $this->actividadDlRepository->getErrorTxt();
        } elseif (ConfigGlobal::is_app_installed('procesos')) {
            // parametro sfsv = '' para que regenere los dos procesos (sv y sf)
            $this->actividadProcesoTareaRepository->generarProceso((string) $Qid_activ, null, true);
        }

        return ['error_txt' => $error_txt];
    }
}
