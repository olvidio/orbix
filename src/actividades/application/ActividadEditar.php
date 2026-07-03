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
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_tipo_activ = FuncTablasSupport::inputInt($input, 'id_tipo_activ');
        $Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        $Qnum_asistentes = FuncTablasSupport::inputInt($input, 'num_asistentes');
        $Qstatus = FuncTablasSupport::inputInt($input, 'status');
        $Qid_repeticion = FuncTablasSupport::inputInt($input, 'id_repeticion');
        $Qplazas = FuncTablasSupport::inputInt($input, 'plazas');
        $Qtarifa = FuncTablasSupport::inputInt($input, 'id_tarifa');
        $Qdl_org = FuncTablasSupport::inputString($input, 'dl_org');
        $Qnom_activ = FuncTablasSupport::inputString($input, 'nom_activ');
        $Qlugar_esp = FuncTablasSupport::inputString($input, 'lugar_esp');
        $Qdesc_activ = FuncTablasSupport::inputString($input, 'desc_activ');
        $Qf_ini = FuncTablasSupport::inputString($input, 'f_ini');
        $Qf_fin = FuncTablasSupport::inputString($input, 'f_fin');
        $Qobserv = FuncTablasSupport::inputString($input, 'observ');
        $Qnivel_stgr = FuncTablasSupport::inputInt($input, 'nivel_stgr');
        $Qobserv_material = FuncTablasSupport::inputString($input, 'observ_material');
        $Qh_ini = FuncTablasSupport::inputString($input, 'h_ini');
        $Qh_fin = FuncTablasSupport::inputString($input, 'h_fin');
        $Qpublicado = FuncTablasSupport::inputString($input, 'publicado');
        $Qidioma = FuncTablasSupport::inputString($input, 'idioma');

        $oPermSesion = $_SESSION['oPermActividades'] ?? null;
        if (!($oPermSesion instanceof PermisosActividades)) {
            return ['error_txt' => _('sesión de permisos no disponible')];
        }

        $oPermSesion->setActividad($Qid_activ, $Qid_tipo_activ !== 0 ? (string) $Qid_tipo_activ : null, $Qdl_org !== '' ? $Qdl_org : null);
        $oPermActiv = $oPermSesion->getPermisoActual('datos');

        if (ConfigGlobal::is_app_installed('procesos') && $oPermActiv->have_perm_activ('crear') === true) {
            $Qisfsv_val = FuncTablasSupport::inputInt($input, 'isfsv_val');
            $Qiasistentes_val = FuncTablasSupport::inputInt($input, 'iasistentes_val');
            $Qiactividad_val = FuncTablasSupport::inputInt($input, 'iactividad_val');
            $Qinom_tipo_val = FuncTablasSupport::inputString($input, 'inom_tipo_val');
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
        $oActividad->setPublicado(FuncTablasSupport::isTrue($Qpublicado));
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
