<?php

namespace src\cambios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Ubi;

/**
 * Construye el texto legible de un aviso a partir de un `Cambio`.
 * Extraído de `Cambio::getAvisoTxt()` para evitar DI en la entidad de dominio.
 */
final class CambioAvisoTxtBuilder
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private CambioRepositoryInterface $cambioRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private RepeticionRepositoryInterface $repeticionRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    public function build(Cambio $cambio): string|false
    {
        $bEliminada = false;
        $iTipo_cambio = $cambio->getId_tipo_cambio();
        $sObjeto = (string) $cambio->getObjeto();
        $iId = $cambio->getId_activ();
        $sPropiedad = (string) ($cambio->getPropiedad() ?? '');
        $sValor_old = (string) ($cambio->getValor_old() ?? '');
        $sValor_new = (string) ($cambio->getValor_new() ?? '');

        $oActividad = $this->actividadAllRepository->findById($iId);
        if ($oActividad === null) {
            return false;
        }
        $aStatus = StatusId::getArrayStatus();

        $sNomActiv = $oActividad->getNom_activ();
        if (empty($sNomActiv)) {
            $bEliminada = true;
            $sNomActiv = $this->cambioRepository->getNomActivEliminada($iId);
        }

        $sPropiedad = $sPropiedad === '' ? '-' : $sPropiedad;

        if ($sPropiedad === 'id_nom') {
            if ($sValor_old !== '' && is_numeric($sValor_old)) {
                $oPersona = $this->personaSacdRepository->findById((int) $sValor_old);
                if ($oPersona !== null) {
                    $sValor_old = $oPersona->getPrefApellidosNombre();
                }
            }
            if ($sValor_new !== '' && is_numeric($sValor_new)) {
                $oPersona = $this->personaSacdRepository->findById((int) $sValor_new);
                if ($oPersona !== null) {
                    $sValor_new = $oPersona->getPrefApellidosNombre();
                }
            }
        }
        if ($sPropiedad === 'id_ubi') {
            if ($sValor_old !== '') {
                $oUbi = Ubi::NewUbi($sValor_old);
                if ($oUbi) {
                    $sValor_old = $oUbi->getNombre_ubi();
                }
            }
            if ($sValor_new !== '') {
                $oUbi = Ubi::NewUbi($sValor_new);
                if ($oUbi) {
                    $sValor_new = $oUbi->getNombre_ubi();
                }
            }
        }
        if ($sObjeto === 'Actividad' || $sObjeto === 'ActividadDl' || $sObjeto === 'ActividadEx') {
            if ($sPropiedad === 'status') {
                $sValor_old = (string) ($aStatus[$sValor_old] ?? $sValor_old);
                $sValor_new = (string) ($aStatus[$sValor_new] ?? $sValor_new);
            }
            if ($sPropiedad === 'f_ini' || $sPropiedad === 'f_fin') {
                $oFOld = new DateTimeLocal($sValor_old);
                $sValor_old = $oFOld->getFromLocal();
                $oFNew = new DateTimeLocal($sValor_new);
                $sValor_new = $oFNew->getFromLocal();
            }
            if ($sPropiedad === 'id_tarifa') {
                $aTarifas = $this->tipoTarifaRepository->getArrayTipoTarifas();
                $sValor_old = $sValor_old === '' ? $sValor_old : (string) ($aTarifas[$sValor_old] ?? $sValor_old);
                $sValor_new = $sValor_new === '' ? $sValor_new : (string) ($aTarifas[$sValor_new] ?? $sValor_new);
            }
            if ($sPropiedad === 'id_repeticion') {
                $aRepeticion = $this->repeticionRepository->getArrayRepeticion();
                $sValor_old = $sValor_old === '' ? $sValor_old : (string) ($aRepeticion[$sValor_old] ?? $sValor_old);
                $sValor_new = $sValor_new === '' ? $sValor_new : (string) ($aRepeticion[$sValor_new] ?? $sValor_new);
            }
            if ($sPropiedad === 'nivel_stgr') {
                $aNivelStgr = NivelStgrId::getArrayNivelStgr();
                $sValor_old = $sValor_old === '' ? $sValor_old : (string) ($aNivelStgr[$sValor_old] ?? $sValor_old);
                $sValor_new = $sValor_new === '' ? $sValor_new : (string) ($aNivelStgr[$sValor_new] ?? $sValor_new);
            }
        }

        $etiqueta = $sPropiedad;

        if ($sObjeto === 'Asistente'
            || $sObjeto === 'AsistenteDl'
            || $sObjeto === 'AsistenteOut'
            || $sObjeto === 'AsistenteEx'
        ) {
            if ($sValor_new === '' && $sValor_old === '') {
                return false;
            }
        }
        $sValor_old = $sValor_old === '' ? '-' : $sValor_old;
        $sValor_new = $sValor_new === '' ? '-' : $sValor_new;

        $sformat = '';
        switch ($iTipo_cambio) {
            case Cambio::TIPO_CMB_INSERT:
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: se ha creado la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: se ha asignado un cargo a \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: se ha asignado el sacd \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                        $sformat = _("Asistencia: \"%4\$s\" se ha incorporado a la actividad \"%1\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: se ha asignado el ctr \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_UPDATE:
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: la actividad \"%1\$s\" ha cambiado el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: ha cambiado el cargo en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: ha cambiado el cargo en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                        $sformat = _("Asistente: ha cambiado la asistencia en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: ctr \"%2\$s\" Ha cambiado a la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_DELETE:
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: se ha eliminado la actividad \"%3\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: se ha quitado el cargo a \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: se ha quitado al sacd \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                        $sformat = _("Asistencia: \"%3\$s\" se ha borrado de la actividad \"%1\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: se ha quitado al ctr \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_FASE:
                $id_fase = $sValor_old;
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $aFases = $cambio->getJson_fases_sv();
                } else {
                    $aFases = $cambio->getJson_fases_sf();
                }
                $idStatus = $cambio->getId_status();
                $sFase = '';

                if (!$bEliminada) {
                    if ($sPropiedad !== '-') {
                        $cFases = $this->actividadFaseRepository->getActividadFases(['id_fase' => $id_fase]);
                        $sFase = $cFases[0]->getDesc_fase();

                        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($sValor_new)) {
                            $sformat = _("Fase \"%2\$s\" marcada en la actividad \"%1\$s\"");
                        } else {
                            $sformat = _("Fase \"%2\$s\" desmarcada en la actividad \"%1\$s\"");
                        }
                    } elseif ($idStatus !== null) {
                        $sFase = (string) ($aStatus[$idStatus] ?? '');

                        $sformat = _("Fase cambiada en la actividad \"%1\$s\". Status \"%3\$s\"");
                        if ($sValor_old === '-' && $sValor_new == 1) {
                            $sformat = _("Status \"%2\$s\" completado en la actividad \"%1\$s\". Status actual \"%3\$s\"");
                        }
                        if ($sValor_old == 1 && $sValor_new === '-') {
                            $sformat = _("Status \"%2\$s\" eliminada en la actividad \"%1\$s\". Status actual \"%3\$s\"");
                        }
                    }
                } else {
                    $sformat = _("Fase cambiada en la actividad \"%1\$s\"");
                }
                return sprintf((string) $sformat, $sNomActiv, $sFase);
        }

        if ($sformat === '') {
            return "$sNomActiv; $etiqueta; $sValor_old; $sValor_new";
        }

        return sprintf($sformat, $sNomActiv, $etiqueta, $sValor_old, $sValor_new);
    }
}
