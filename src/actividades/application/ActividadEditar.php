<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\value_objects\IdLocale;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Guarda la edición de una actividad existente.
 * Sustituye la lógica del antiguo case `editar` de actividad_update.php.
 */
final class ActividadEditar
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
        private ActividadPlazasDlRepositoryInterface $actividadPlazasDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error_txt: string, tipo_error?: string}
     */
    public function execute(array $input): array
    {
        $Qid_activ = input_int($input, 'id_activ');
        $Qid_tipo_activ = input_int($input, 'id_tipo_activ');
        $Qid_ubi = input_int($input, 'id_ubi');
        $Qnum_asistentes = input_int($input, 'num_asistentes');
        $Qstatus = input_int($input, 'status');
        $Qid_repeticion = input_int($input, 'id_repeticion');
        $Qplazas = input_int($input, 'plazas');
        $Qtarifa = input_int($input, 'id_tarifa');
        $Qdl_org = input_string($input, 'dl_org');
        $Qnom_activ = input_string($input, 'nom_activ');
        $Qlugar_esp = input_string($input, 'lugar_esp');
        $Qdesc_activ = input_string($input, 'desc_activ');
        $Qf_ini = input_string($input, 'f_ini');
        $Qf_fin = input_string($input, 'f_fin');
        $Qobserv = input_string($input, 'observ');
        $Qnivel_stgr = input_int($input, 'nivel_stgr');
        $Qobserv_material = input_string($input, 'observ_material');
        $Qh_ini = input_string($input, 'h_ini');
        $Qh_fin = input_string($input, 'h_fin');
        $Qpublicado = input_string($input, 'publicado');
        $Qidioma = input_string($input, 'idioma');

        $oPermSesion = $_SESSION['oPermActividades'] ?? null;
        if (!($oPermSesion instanceof PermisosActividades)) {
            return ['error_txt' => _('sesión de permisos no disponible')];
        }

        $oPermSesion->setActividad($Qid_activ, $Qid_tipo_activ !== 0 ? (string) $Qid_tipo_activ : null, $Qdl_org !== '' ? $Qdl_org : null);
        $oPermActiv = $oPermSesion->getPermisoActual('datos');

        if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('crear') === true) {
            $Qisfsv_val = input_int($input, 'isfsv_val');
            $Qiasistentes_val = input_int($input, 'iasistentes_val');
            $Qiactividad_val = input_int($input, 'iactividad_val');
            $Qinom_tipo_val = input_string($input, 'inom_tipo_val');
            $condta = $Qisfsv_val . $Qiasistentes_val . $Qiactividad_val . $Qinom_tipo_val;
            if (strpos($condta, '.') === false) {
                $valor_id_tipo_activ = (int) $condta;
            } else {
                return ['error_txt' => _("debe seleccionar un tipo de actividad"), 'tipo_error' => 'tipo'];
            }
        } else {
            $valor_id_tipo_activ = $Qid_tipo_activ;
        }

        $oActividad = $this->actividadAllRepository->findById($Qid_activ);
        if ($oActividad === null) {
            return ['error_txt' => _('actividad no encontrada')];
        }

        $plazas_old = $oActividad->getPlazas();
        $dl_orig = $oActividad->getDl_org();

        // Comprueba que el id tiene al menos 6 dígitos (legacy actividad_update.php).
        if ($valor_id_tipo_activ !== 0 && ($valor_id_tipo_activ / 100000) >= 1) {
            $oActividad->setId_tipo_activ($valor_id_tipo_activ);
        }
        if (isset($input['dl_org'])) {
            $dl_org = strtok($Qdl_org, '#');
            $oActividad->setDl_org(is_string($dl_org) ? $dl_org : '');
        } else {
            $dl_org = '';
            $oActividad->setDl_org('');
        }
        $oActividad->setNom_activ($Qnom_activ);

        if ($Qid_ubi !== 0 && $Qid_ubi !== 1) {
            $oActividad->setId_ubi($Qid_ubi);
            $oActividad->setLugar_esp('');
        } else {
            $oActividad->setId_ubi($Qid_ubi);
            $oActividad->setLugar_esp($Qlugar_esp);
        }
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
        $oActividad->setTarifa($Qtarifa);
        $oH_ini = $Qh_ini === '' ? null : TimeLocal::fromString($Qh_ini);
        $oActividad->setH_ini($oH_ini);
        $oH_fin = $Qh_fin === '' ? null : TimeLocal::fromString($Qh_fin);
        $oActividad->setH_fin($oH_fin);
        $oActividad->setPublicado(is_true($Qpublicado));
        $oActividad->setPlazas($Qplazas);
        $idiomaVo = $Qidioma === '' ? null : new IdLocale($Qidioma);
        $oActividad->setIdiomaVo($idiomaVo);

        $error_txt = '';
        if ($this->actividadAllRepository->Guardar($oActividad) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $this->actividadAllRepository->getErrorTxt();
            return ['error_txt' => $error_txt];
        }

        if (ConfigGlobal::is_app_installed('procesos')) {
            if (($dl_orig != $dl_org) && ($dl_org == ConfigGlobal::mi_delef() || $dl_orig == ConfigGlobal::mi_delef())) {
                $this->actividadProcesoTareaRepository->generarProceso(
                    (string) $oActividad->getId_activ(),
                    null,
                    false,
                    $oActividad,
                );
            }
        }

        if (ConfigGlobal::is_app_installed('actividadplazas')) {
            $mi_dele = ConfigGlobal::mi_delef();
            if ($Qplazas !== 0 && ($plazas_old != $Qplazas) && $Qdl_org == $mi_dele) {
                $id_dl = 0;
                $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $mi_dele]);
                if (count($cDelegaciones) > 0) {
                    $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
                }
                $aWhere = [
                    'id_activ' => $Qid_activ,
                    'id_dl' => $id_dl,
                    'dl_tabla' => $mi_dele,
                ];
                $cActividadPlazas = $this->actividadPlazasRepository->getactividadesPlazas($aWhere);
                $salta = 0;
                if (count($cActividadPlazas) === 1) {
                    $oActividadPlazasDl = $cActividadPlazas[0];
                    $plazas_dl = $oActividadPlazasDl->getPlazas();
                    if ($plazas_dl !== $plazas_old) {
                        $salta = 1;
                    }
                }
                if ($salta !== 1) {
                    $oActividadPlazasDl = $this->actividadPlazasRepository->findById($Qid_activ);
                    if ($oActividadPlazasDl instanceof ActividadPlazas) {
                        $oActividadPlazasDl->setPlazas($Qplazas);

                        if ($this->actividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
                            $error_txt .= _("hay un error, no se ha guardado");
                            $error_txt .= "\n" . $this->actividadPlazasDlRepository->getErrorTxt();
                        }
                    }
                }
            }
        }

        return ['error_txt' => $error_txt];
    }
}
