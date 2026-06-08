<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\permisos\domain\PermisosActividades;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\value_objects\IdLocale;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

class ActividadNueva
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private ActividadExRepositoryInterface $actividadExRepository,
        private ImportadaRepositoryInterface $importadaRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadPlazasDlRepositoryInterface $actividadPlazasDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $datosActividad
     */
    public function actividadNueva(array $datosActividad): string
    {
        $Qdl_org = input_string($datosActividad, 'dl_org');
        $Qpublicado = filter_var($datosActividad['publicado'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $Qid_tipo_activ = input_string($datosActividad, 'id_tipo_activ');
        $Qnom_activ = input_string($datosActividad, 'nom_activ');
        $Qf_ini = input_string($datosActividad, 'f_ini');
        $Qf_fin = input_string($datosActividad, 'f_fin');
        $Qstatus = input_int($datosActividad, 'status');
        $Qid_ubi = input_int($datosActividad, 'id_ubi');
        $Qlugar_esp = input_string($datosActividad, 'lugar_esp');
        $Qdesc_activ = input_string($datosActividad, 'desc_activ');
        $Qprecio = $datosActividad['precio'] ?? null;
        $Qnum_asistentes = input_int($datosActividad, 'num_asistentes');
        $Qobserv = input_string($datosActividad, 'observ');
        $Qnivel_stgr = input_int($datosActividad, 'nivel_stgr');
        $Qid_repeticion = input_int($datosActividad, 'id_repeticion');
        $Qobserv_material = input_string($datosActividad, 'observ_material');
        $Qtarifa = input_int($datosActividad, 'tarifa');
        $Qh_ini = input_string($datosActividad, 'h_ini');
        $Qh_fin = input_string($datosActividad, 'h_fin');
        $Qplazas = input_int($datosActividad, 'plazas');
        $Qidioma = input_string($datosActividad, 'idioma');

        if ($Qdl_org !== ConfigGlobal::mi_delef()) {
            $Qpublicado = true;
            $oDBPropiedades = new DBPropiedades();
            $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
            if (!is_array($a_posibles_esquemas)) {
                $a_posibles_esquemas = [];
            }
            $is_dl_in_orbix = FALSE;
            foreach ($a_posibles_esquemas as $esquema) {
                $row = explode('-', $esquema);
                if (($row[1] ?? '') === $Qdl_org) {
                    $is_dl_in_orbix = TRUE;
                    break;
                }
            }
            if ($is_dl_in_orbix) {
                throw new \RuntimeException(_("No puede crear una actividad que organiza una dl/r que ya usa aquinate"));
            }
        }

        $dl_org_no_f = (string) preg_replace('/(\.*)f$/', '\1', $Qdl_org);
        $dl_propia = ConfigGlobal::mi_dele() === $dl_org_no_f;
        if (ConfigGlobal::is_app_installed('procesos')) {
            $oPermActividades = $_SESSION['oPermActividades'] ?? null;
            if (!($oPermActividades instanceof PermisosActividades)) {
                throw new \RuntimeException(_('sesión de permisos no disponible'));
            }
            $oPermActividades->setId_tipo_activ($Qid_tipo_activ);
            if ($oPermActividades->getPermisoCrear($dl_propia) === false) {
                throw new \RuntimeException(_("No tiene permiso para crear una actividad de este tipo"));
            }
        }

        if ($Qnom_activ === '' || $Qf_ini === '' || $Qf_fin === '' || $Qstatus === 0 || $Qdl_org === '') {
            throw new \RuntimeException(_("debe llenar todos los campos que tengan un (*)"));
        }

        $isfsv = substr($Qid_tipo_activ, 0, 1);
        $mi_dele = ConfigGlobal::mi_delef($isfsv);
        if ($Qdl_org === $mi_dele) {
            $ActividadRepository = $this->actividadDlRepository;
            $newId = $ActividadRepository->getNewId();
            $newIdActividad = $ActividadRepository->getNewIdActividad($newId);
            $oActividad = new ActividadAll();
            $oActividad->setId_activ($newIdActividad);
            $oActividad->setIdTablaVo(new IdTablaCode('dl'));
        } else {
            $ActividadRepository = $this->actividadExRepository;
            $newId = $ActividadRepository->getNewId();
            $newIdActividad = $ActividadRepository->getNewIdActividad($newId);
            $oActividad = new ActividadAll();
            $oActividad->setId_activ($newIdActividad);
            $oActividad->setPublicado(true);
            $oActividad->setIdTablaVo(new IdTablaCode('ex'));
            $Qstatus = StatusId::ACTUAL;
        }
        $oActividad->setDl_org($Qdl_org);
        if ($Qid_tipo_activ !== '') {
            try {
                $oActividad->setId_tipo_activ((int) $Qid_tipo_activ);
            } catch (\InvalidArgumentException) {
                throw new \RuntimeException(_("tipo de actividad incorrecto"));
            }
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
        $oF_ini = DateTimeLocal::createFromLocal($Qf_ini);
        $oActividad->setF_ini($oF_ini instanceof DateTimeLocal ? $oF_ini : null);
        $oF_fin = DateTimeLocal::createFromLocal($Qf_fin);
        $oActividad->setF_fin($oF_fin instanceof DateTimeLocal ? $oF_fin : null);
        $oActividad->setPrecioVo(Dinero::fromInput($Qprecio));
        $oActividad->setNum_asistentes($Qnum_asistentes);
        $oActividad->setStatus($Qstatus);
        $oActividad->setObserv($Qobserv);
        if ($Qnivel_stgr === 0) {
            $Qnivel_stgr = NivelStgrId::generarNivelStgr($Qid_tipo_activ);
        }
        $oActividad->setNivel_stgr($Qnivel_stgr);
        $oActividad->setId_repeticion($Qid_repeticion);
        $oActividad->setObserv_material($Qobserv_material);
        $oActividad->setTarifa($Qtarifa);
        $oActividad->setH_ini($Qh_ini === '' ? null : TimeLocal::fromString($Qh_ini));
        $oActividad->setH_fin($Qh_fin === '' ? null : TimeLocal::fromString($Qh_fin));
        $oActividad->setPublicado($Qpublicado);
        $oActividad->setPlazas($Qplazas);
        $oActividad->setIdiomaVo($Qidioma === '' ? null : new IdLocale($Qidioma));
        if ($ActividadRepository->Guardar($oActividad) === false) {
            throw new \RuntimeException(_("hay un error, no se ha guardado") . ": " . $ActividadRepository->getErrorTxt());
        }
        if ($Qdl_org !== $mi_dele) {
            $id_activ = $oActividad->getId_activ();
            $oImportada = new Importada();
            $oImportada->setId_activ($id_activ);
            if ($this->importadaRepository->Guardar($oImportada) === false) {
                throw new \RuntimeException(_("hay un error, no se ha importado") . ": " . $this->importadaRepository->getErrorTxt());
            }
        }
        if (ConfigGlobal::is_app_installed('actividadplazas') && $Qplazas > 0 && $Qdl_org === $mi_dele) {
            $id_activ = $oActividad->getId_activ();
            $id_dl = 0;
            $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $mi_dele]);
            if ($cDelegaciones !== []) {
                $id_dl = $cDelegaciones[0]->getIdDlVo()->value();
            }
            $cActividadPlazasDl = $this->actividadPlazasDlRepository->getActividadesPlazas([
                'id_activ' => $id_activ,
                'id_dl' => $id_dl,
                'dl_tabla' => $mi_dele,
            ]);
            $oActividadPlazasDl = $cActividadPlazasDl[0] ?? null;
            if (!($oActividadPlazasDl instanceof ActividadPlazas)) {
                $oActividadPlazasDl = new ActividadPlazas();
                $oActividadPlazasDl->setId_activ($id_activ);
                $oActividadPlazasDl->setId_dl($id_dl);
                $oActividadPlazasDl->setDlTablaVo($mi_dele);
            }
            $oActividadPlazasDl->setPlazas($Qplazas);
            if ($this->actividadPlazasDlRepository->Guardar($oActividadPlazasDl) === false) {
                throw new \RuntimeException(_("hay un error, no se ha guardado") . ": " . $this->actividadPlazasDlRepository->getErrorTxt());
            }
        }
        return (string) $oActividad->getId_activ();
    }
}
