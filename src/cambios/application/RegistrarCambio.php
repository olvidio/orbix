<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use function core\is_true;

/**
 * Caso de uso: registra un cambio en `av_cambios` / `av_cambios_dl`.
 *
 * Se invoca desde `RegistrarCambioListener` como reaccion al evento
 * `EntidadModificada`. Genera una fila (o varias, para UPDATE) con la
 * diferencia entre `$aDadesActuals` y `$aDadesNew`, anotando el tipo de
 * actividad, fases completadas y dl origen para que mas tarde
 * `AvisosGenerarTabla` pueda cruzarlo con las preferencias de aviso.
 *
 * Sucesor del metodo `addCanvi` de la legacy
 * `cambios\model\GestorAvisoCambios`.
 */
class RegistrarCambio
{
    /**
     * @param string $sObjeto      nombre corto del objeto ('Actividad', 'Asistente', 'CentroEncargado', …).
     * @param string $sTipoCambio  'INSERT' | 'UPDATE' | 'DELETE' | 'FASE'.
     * @param int|null $id_activ   actividad asociada (puede ser `null` en algunos casos edge).
     * @param array  $aDadesNew    datos resultantes del cambio (para INSERT / UPDATE / FASE).
     * @param array  $aDadesActuals datos previos (para UPDATE / DELETE).
     */
    public function execute(
        string $sObjeto,
        string $sTipoCambio,
        ?int $id_activ,
        array $aDadesNew,
        array $aDadesActuals
    ): void {
        $id_user = ConfigGlobal::mi_id_usuario();
        $sfsv = ConfigGlobal::mi_sfsv();
        $oAhora = new DateTimeLocal();

        // --- Resolver tipo_activ / dl_org / status segun objeto ---
        switch ($sObjeto) {
            case 'Actividad':
            case 'ActividadDl':
            case 'ActividadEx':
                $Id_tipo_activ = empty($aDadesNew['id_tipo_activ']) ? $aDadesActuals['id_tipo_activ'] : $aDadesNew['id_tipo_activ'];
                $dl_org = empty($aDadesActuals['dl_org']) ? $aDadesNew['dl_org'] : $aDadesActuals['dl_org'];
                $id_status = $aDadesNew['status'] ?? $aDadesActuals['status'];
                break;
            default:
                // Si no hay id_activ, usar valores por defecto (caso edge del
                // flujo ActividadEx que aun no se ha importado).
                if ($id_activ === null) {
                    $Id_tipo_activ = 111111;
                    $dl_org = 'test1';
                    $id_status = 4;
                    break;
                }
                $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
                $oActividad = $ActividadAllRepository->findById($id_activ);
                if ($oActividad === null) {
                    $Id_tipo_activ = 111111;
                    $dl_org = 'test2';
                    $id_status = 4;
                } else {
                    $Id_tipo_activ = $oActividad->getId_tipo_activ();
                    $dl_org = $oActividad->getDl_org();
                    $id_status = $oActividad->getStatus();
                }
        }

        // --- Resolver repositorio y fases segun modulos instalados ---
        if (ConfigGlobal::is_app_installed('cambios')) {
            $CambioRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
            if (ConfigGlobal::is_app_installed('procesos')) {
                $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
                $ActividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sv');
                $aFases_sv = $ActividadProcesoTareaRepository->getFasesCompletadas($id_activ);
                $ActividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sf');
                $aFases_sf = $ActividadProcesoTareaRepository->getFasesCompletadas($id_activ);
            } else {
                // Sin modulo `procesos`, la fase es el status.
                $aFases_sv = [$id_status];
                $aFases_sf = [$id_status];
            }
        } else {
            // Si no tengo instalado el modulo de `cambios`, no tengo la tabla en
            // mi esquema. Lo anoto en `public.av_cambios`. Como fase anoto el
            // estado de la actividad.
            $CambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
            $aFases_sv = [$id_status];
            $aFases_sf = [$id_status];
        }

        switch ($sTipoCambio) {
            case 'INSERT':
                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_INSERT,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora
                );
                $oActividadCambio->setValor_old();
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $oActividadCambio->setPropiedad('nom_activ');
                        $oActividadCambio->setValor_new($aDadesNew['nom_activ']);
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteEx':
                    case 'AsistenteOut':
                    case 'ActividadCargoNoSacd':
                    case 'ActividadCargoSacd':
                        $oActividadCambio->setPropiedad('id_nom');
                        $oActividadCambio->setValor_new($aDadesNew['id_nom']);
                        break;
                    case 'CentroEncargado':
                        $oActividadCambio->setPropiedad('id_ubi');
                        $oActividadCambio->setValor_new($aDadesNew['id_ubi']);
                        break;
                }
                $CambioRepository->Guardar($oActividadCambio);
                break;

            case 'UPDATE':
                $result = array_diff_assoc($aDadesNew, $aDadesActuals);
                foreach ($result as $key => $value) {
                    // Con booleans no se aclara: 0, 1, false, true, f, t...
                    if (!is_null(is_true($value))
                        && is_true($aDadesActuals[$key]) === is_true($value)
                    ) {
                        continue;
                    }
                    $oActividadCambio = $this->construirBase(
                        $CambioRepository,
                        Cambio::TIPO_CMB_UPDATE,
                        $id_activ,
                        $Id_tipo_activ,
                        $aFases_sv,
                        $aFases_sf,
                        $id_status,
                        $dl_org,
                        $sObjeto,
                        $id_user,
                        $sfsv,
                        $oAhora
                    );
                    $oActividadCambio->setPropiedad($key);
                    $oActividadCambio->setValor_old($aDadesActuals[$key]);
                    $oActividadCambio->setValor_new($value);
                    $CambioRepository->Guardar($oActividadCambio);
                }
                break;

            case 'DELETE':
                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_DELETE,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora
                );
                $oActividadCambio->setValor_new();
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        // pongo id_activ = 0, pues al eliminar la actividad se eliminan todas las filas relacionadas.
                        // Asi mantengo el dato que se ha eliminado.
                        $oActividadCambio->setId_activ(0);
                        $oActividadCambio->setPropiedad('nom_activ');
                        $oActividadCambio->setValor_old($aDadesActuals['nom_activ']);
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteEx':
                    case 'AsistenteOut':
                    case 'ActividadCargoNoSacd':
                    case 'ActividadCargoSacd':
                        if (!empty($aDadesActuals['id_nom'])) {
                            $oActividadCambio->setPropiedad('id_nom');
                            $oActividadCambio->setValor_old($aDadesActuals['id_nom']);
                        }
                        break;
                    case 'CentroEncargado':
                        $oActividadCambio->setPropiedad('id_ubi');
                        $oActividadCambio->setValor_old($aDadesActuals['id_ubi']);
                        break;
                }
                $CambioRepository->Guardar($oActividadCambio);
                break;

            case 'FASE':
                // Solo me fijo en el `completado`. Con booleans no se aclara:
                // 0, 1, false, true, f, t...
                $boolCompletadoNew = !empty($aDadesNew['completado']) && is_true($aDadesNew['completado']);
                $boolCompletadoActual = !empty($aDadesActuals['completado']) && is_true($aDadesActuals['completado']);

                if ($boolCompletadoNew === $boolCompletadoActual) {
                    break;
                }

                // En vez del nombre del valor_old, pongo el id de la fase que se marca.
                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_FASE,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora,
                    useStatusVo: false
                );
                $oActividadCambio->setPropiedad('completado');
                $oActividadCambio->setValor_old($aDadesActuals['id_fase']);
                $oActividadCambio->setValor_new($boolCompletadoNew);
                $CambioRepository->Guardar($oActividadCambio);
                break;
        }
    }

    /**
     * Construye un `Cambio` con los campos comunes a todos los tipos. Los
     * especificos (`propiedad`, `valor_old`, `valor_new`) los rellena el
     * caller. `useStatusVo` diferencia la rama `FASE` (usa `setId_status`
     * directo con el id numerico) del resto (usan `setIdStatusVo` con el
     * value object).
     */
    private function construirBase(
        CambioRepositoryInterface|CambioDlRepositoryInterface $CambioRepository,
        int $tipoCambio,
        ?int $id_activ,
        $Id_tipo_activ,
        array $aFases_sv,
        array $aFases_sf,
        $id_status,
        string $dl_org,
        string $sObjeto,
        int $id_user,
        int $sfsv,
        DateTimeLocal $oAhora,
        bool $useStatusVo = true
    ): Cambio {
        $newIdItem = $CambioRepository->getNewId();
        $oCambio = new Cambio();
        $oCambio->setId_item_cambio($newIdItem);
        $oCambio->setId_tipo_cambio($tipoCambio);
        $oCambio->setId_activ($id_activ);
        $oCambio->setId_tipo_activ($Id_tipo_activ);
        $oCambio->setJson_fases_sv($aFases_sv);
        $oCambio->setJson_fases_sf($aFases_sf);
        if ($useStatusVo) {
            $oCambio->setIdStatusVo($id_status);
        } else {
            $oCambio->setId_status($id_status);
        }
        $oCambio->setDl_org($dl_org);
        $oCambio->setObjeto($sObjeto);
        $oCambio->setQuien_cambia($id_user);
        $oCambio->setSfsv_quien_cambia($sfsv);
        $oCambio->setTimestamp_cambio($oAhora);
        return $oCambio;
    }
}
