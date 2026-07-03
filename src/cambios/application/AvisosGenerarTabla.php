<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\cambios\application\legacy\Avisos;
use src\permisos\domain\PermisosActividades;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use stdClass;

/**
 * Caso de uso para generar la tabla de avisos de cambios.
 *
 * Llamado desde:
 *   - el driver CLI `src/cambios/infrastructure/cli/avisos_generar_tabla.php`
 *     (crontab y `exec()` disparado por Cambio::generarTabla()).
 *   - el menu web `frontend/cambios/controller/avisos_generar.php` (boton
 *     "generar tabla" — via require del driver CLI con argv vacio).
 *
 * El use case NO hace `echo`/`exit`; devuelve un array con:
 *   - `err_fila` => HTML de filas de error (<tr>…</tr>) para inyectar en una tabla.
 *   - `bucle_infinito` => true si el bucle detecta que no progresa (antes `exit`).
 *
 * La clase auxiliar `src\cambios\application\legacy\Avisos` sigue siendo
 * legacy (emite `echo`/log desde dentro). Vive en `application/legacy/`
 * porque solo la consume este use case (patron `Resumen` de `notas`).
 */
class AvisosGenerarTabla
{
    public function __construct(
        private Avisos $avisos,
        private CambioRepositoryInterface $cambioRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ImportadaRepositoryInterface $importadaRepository,
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private CambioUsuarioObjetoPrefRepositoryInterface $cambioUsuarioObjetoPrefRepository,
        private CambioUsuarioPropiedadPrefRepositoryInterface $cambioUsuarioPropiedadPrefRepository,
    ) {
    }

    /**
     * @param string $username usuario que dispara el proceso (para `pid`).
     * @param string $esquema  esquema/region de la delegacion (para `pid`).
     * @return array{err_fila: string, bucle_infinito: bool}
     */
    public function execute(string $username, string $esquema): array
    {
        if (!ConfigGlobal::is_app_installed('cambios')) {
            return ['err_fila' => '', 'bucle_infinito' => false];
        }

        $oAvisos = $this->avisos;
        $oAvisos->crear_pid($username, $esquema);

        $CambioRepository = $this->cambioRepository;
        $CambioRepository->borrarCambios('P1Y');

        $aObjPerm = [
            'Actividad' => 'datos',
            'ActividadProcesoTarea' => 'datos',
            'ActividadCargoSacd' => 'sacd',
            'ActividadCargoNoSacd' => 'cargos',
            'Asistente' => 'asistentes',
            'CentroEncargado' => 'ctr',
        ];

        $cNuevosCambios = $CambioRepository->getCambiosNuevos();
        $num_cambios = count($cNuevosCambios);
        $err_fila = '';
        $bucle_infinito = false;

        $ActividadAllRepository = $this->actividadAllRepository;
        $ImportadaRepository = $this->importadaRepository;
        $TipoDeActividadRepository = $this->tipoDeActividadRepository;
        $PersonaSacdRepository = $this->personaSacdRepository;
        $TareaProcesoRepository = $this->tareaProcesoRepository;
        $CambioUsuarioObjetoPrefRepository = $this->cambioUsuarioObjetoPrefRepository;
        $CambiosUsuarioPropiedadPrefRepository = $this->cambioUsuarioPropiedadPrefRepository;

        while ($num_cambios) {
            $num_cambios_inicial = $num_cambios;
            foreach ($cNuevosCambios as $oCambio) {
                $afecta = '';
                $id_item_cmb = $oCambio->getId_item_cambio();
                $id_schema_cmb = $oCambio->getId_schema();
                $sObjeto = $oCambio->getObjeto();
                $dl_org = $oCambio->getDl_org();
                $id_tipo_activ = $oCambio->getId_tipo_activ();
                $aFases_cmb_sv = self::jsonFasesToList($oCambio->getJson_fases_sv());
                $aFases_cmb_sf = self::jsonFasesToList($oCambio->getJson_fases_sf());
                $id_status_cmb = $oCambio->getId_status();
                $propiedad_cmb = $oCambio->getPropiedad();
                $valor_old_cmb = $oCambio->getValor_old();
                $valor_new_cmb = $oCambio->getValor_new();
                $id_activ = $oCambio->getId_activ();
                $oF_cmb = $oCambio->getTimestamp_cambio();

                // Para las actividades, en el cambio se anota: 'ActividadDl' 'ActividadEx'
                // pero en las preferencias, solo 'Actividad'.
                // OJO strpos no sirve, porque me anula ActividadCargo
                if ($sObjeto === 'Actividad' || $sObjeto === 'ActividadDl' || $sObjeto === 'ActividadEx') {
                    $sObjeto = 'Actividad';
                }
                // Para los asistentes, en el cambio se anota: 'Asistente' 'AsistenteDl' 'AsistenteEx' 'AsistenteOut'
                // pero en las preferencias, solo 'Asistente'.
                if ($sObjeto !== null && strpos($sObjeto, 'Asistente') !== false) {
                    $sObjeto = 'Asistente';
                    // Para el caso de los sacd, el permiso es 'asistentessacd'
                    if ($propiedad_cmb === 'id_nom') {
                        $id_nom = empty($valor_new_cmb) ? $valor_old_cmb : $valor_new_cmb;
                        if (is_numeric($id_nom)) {
                            $oPersonaSacd = $PersonaSacdRepository->findById((int) $id_nom);
                            if ($oPersonaSacd !== null && $oPersonaSacd->isSacd()) {
                                $afecta = 'asistentesSacd';
                            }
                        }
                    }
                }

                $afecta = empty($afecta) ? ($aObjPerm[$sObjeto] ?? '') : $afecta;

                if (ConfigGlobal::mi_sfsv() === 1) {
                    $aFases_cmb = $aFases_cmb_sv;
                } else {
                    $aFases_cmb = $aFases_cmb_sf;
                }

                $oAvisos->setId_schema_cmb($id_schema_cmb);
                $oAvisos->setId_item_cmb($id_item_cmb);
                $oAvisos->setObjeto((string) $sObjeto);
                $oAvisos->setFasesCmb($aFases_cmb);

                // para dl y dlf:
                $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', (string) ($dl_org ?? ''));
                $dl_propia = (ConfigGlobal::mi_dele() === $dl_org_no_f);
                // Si es de otra dl, compruebo que sea una actividad importada, sino no tiene sentido avisar.
                if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($dl_propia)) {
                    $cImportadas = $ImportadaRepository->findById($id_activ);
                    if (empty($cImportadas)) {
                        $oAvisos->anotado();
                        continue;
                    }
                }

                $aWhere = [];
                $aOperador = [];
                $aWhere['objeto'] = $sObjeto;
                $aWhere['dl_org'] = (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($dl_propia)) ? 'x' : $dl_org;
                $aWhere['id_tipo_activ_txt'] = $id_tipo_activ;
                $aOperador['id_tipo_activ_txt'] = '~INV';
                $aWhere['_ordre'] = 'aviso_tipo,id_usuario,id_tipo_activ_txt DESC';
                $cCambiosUsuarioObjeto = $CambioUsuarioObjetoPrefRepository->getCambioUsuarioObjetoPrefs($aWhere, $aOperador);
                if ($cCambiosUsuarioObjeto === []) {
                    $oAvisos->anotado();
                    continue;
                }
                $id_usuario_anterior = 0;
                $aviso_tipo_anterior = 0;
                $apuntar = false;
                $yaApuntado = false;
                foreach ($cCambiosUsuarioObjeto as $oCambioUsuarioObjetoPref) {
                    $id_item_usuario_objeto = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
                    $id_usuario = $oCambioUsuarioObjetoPref->getId_usuario();
                    $aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
                    $oAvisos->setId_usuario($id_usuario);
                    if ($yaApuntado && ($aviso_tipo === $aviso_tipo_anterior) && ($id_usuario === $id_usuario_anterior)) {
                        continue;
                    }
                    $aviso_tipo_anterior = $aviso_tipo;
                    $id_usuario_anterior = $id_usuario;
                    $id_pau = $oCambioUsuarioObjetoPref->getCsv_id_pau();
                    $id_fase_ref = $oCambioUsuarioObjetoPref->getId_fase_ref();
                    $aviso_off = $oCambioUsuarioObjetoPref->isAviso_off();
                    $aviso_on = $oCambioUsuarioObjetoPref->isAviso_on();
                    $aviso_outdate = $oCambioUsuarioObjetoPref->isAviso_outdate();

                    $fase_correcta = 0;
                    /////////////////// COMPARAR DATE //////////////////////////////////////////
                    if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_outdate)) {
                        $oActividad = $ActividadAllRepository->findById($id_activ);
                        if ($oActividad === null || $oF_cmb === null) {
                            continue;
                        }
                        $oF_fin = $oActividad->getF_fin();
                        if ($oF_fin !== null && $oF_cmb > $oF_fin) {
                            continue;
                        }
                    }

                    /////////////////// COMPARAR STATUS //////////////////////////////////////////
                    // Si el id_fase es NULL, hay que mirar el id_status
                    // Si el id_status es 1,2,3 corresponde al status de la actividad,
                    //   porque no tiene instalado el módulo de procesos.
                    if (empty($aFases_cmb)) {
                        if (ConfigGlobal::is_app_installed('procesos')) {
                            $status_de_fase = 0;
                            $cTiposActividad = $TipoDeActividadRepository->getTiposDeActividades(['id_tipo_activ' => $id_tipo_activ]);
                            if (!empty($cTiposActividad)) {
                                $id_tipo_proceso = $cTiposActividad[0]->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
                                $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso, 'id_fase' => $id_fase_ref]);
                                if (!empty($cTareasProceso)) {
                                    $status_de_fase = $cTareasProceso[0]->getStatus();
                                }
                            }
                            if ($id_status_cmb === $status_de_fase && \src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_on)) {
                                $fase_correcta = 1;
                            }
                        } else {
                            if ($id_status_cmb === $id_fase_ref && \src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_on)) {
                                $fase_correcta = 1;
                            }
                        }
                    } else {
                        /////////////////// COMPARAR FASES //////////////////////////////////////////
                        // fase on
                        if (in_array($id_fase_ref, $aFases_cmb)) {
                            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_on)) {
                                $oPermActividades = $this->makePermisosActividades($id_usuario);
                                $oPermActividades->setActividad($id_activ);
                                $oPermActividades->setFasesCompletadas($aFases_cmb);
                                $oPermActiv = $oPermActividades->getPermisoActual($afecta);
                                if (!$oPermActiv->have_perm_activ('ocupado')) {
                                    continue;
                                }

                                if (ConfigGlobal::is_app_installed('procesos')) {
                                    $fase_correcta = 1;
                                } else {
                                    $oActividad = $ActividadAllRepository->findById($id_activ);
                                    if ($oActividad !== null) {
                                        $status = $oActividad->getStatus();
                                        foreach ($aFases_cmb as $id_fase) {
                                            if ($status === $id_fase) {
                                                $fase_correcta = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            // fase off
                            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_off)) {
                                $oPermActividades = $this->makePermisosActividades($id_usuario);
                                $oPermActividades->setActividad($id_activ);
                                $oPermActividades->setFasesCompletadas($aFases_cmb);
                                $oPermActiv = $oPermActividades->getPermisoActual($afecta);
                                if (!$oPermActiv->have_perm_activ('ocupado')) {
                                    continue;
                                }

                                $fase_correcta = 1;
                            }
                        }
                    }

                    if ($fase_correcta === 1) {
                        $cListaPropiedades = $CambiosUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(['id_item_usuario_objeto' => $id_item_usuario_objeto]);
                        foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
                            $propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
                            $operador = $oCambioUsuarioPropiedadPref->getOperador();
                            $valor = $oCambioUsuarioPropiedadPref->getValor();
                            $valor_old = $oCambioUsuarioPropiedadPref->isValor_old();
                            $valor_new = $oCambioUsuarioPropiedadPref->isValor_new();

                            if ($propiedad_cmb === $propiedad) {
                                if (!$oAvisos->me_afecta($propiedad, $id_activ, $valor_old_cmb, $valor_new_cmb, $id_pau, (string) $sObjeto)) {
                                    $apuntar = false;
                                    continue;
                                } elseif (!empty($valor)) {
                                    $operador = empty($operador) ? '=' : $operador;
                                    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($valor_old ?? false)) {
                                        $apuntar = $oAvisos->comparar($valor_old_cmb, $operador, $valor);
                                    }
                                    if ($apuntar === false && \src\shared\domain\helpers\FuncTablasSupport::isTrue($valor_new ?? false)) {
                                        $apuntar = $oAvisos->comparar($valor_new_cmb, $operador, $valor);
                                    }
                                } else {
                                    $apuntar = true;
                                }
                            }
                        }
                    }
                    if ($apuntar) {
                        $err_fila .= $oAvisos->fn_apuntar($aviso_tipo);
                        $yaApuntado = true;
                    }
                    $apuntar = false;
                }
                $oAvisos->anotado();
            }
            // Si algo falla, el $num_cambios_inicial es igual al actual y se genera un bucle infinito.
            // Si se han producido nuevos cambios durante el proceso, $num_cambios no sera 0 y se repite el proceso.
            $cNuevosCambios = $CambioRepository->getCambiosNuevos();
            $num_cambios = count($cNuevosCambios);
            if ($num_cambios === $num_cambios_inicial) {
                $oAvisos->borrar_pid($username, $esquema);
                $bucle_infinito = true;
                break;
            }
        }

        $oAvisos->borrar_pid($username, $esquema);

        return [
            'err_fila' => $err_fila,
            'bucle_infinito' => $bucle_infinito,
        ];
    }

    /**
     * @param array<string, mixed>|stdClass|null $json
     * @return list<int>
     */
    private static function jsonFasesToList(array|stdClass|null $json): array
    {
        if ($json === null) {
            return [];
        }
        if ($json instanceof stdClass) {
            $json = (array) $json;
        }
        $result = [];
        foreach ($json as $fase) {
            if (is_numeric($fase)) {
                $result[] = (int) $fase;
            }
        }

        return $result;
    }

    private function makePermisosActividades(int $id_usuario): PermisosActividades
    {
        $resolved = DependencyResolver::make(PermisosActividades::class, ['idUsuario' => $id_usuario]);
        if (!$resolved instanceof PermisosActividades) {
            throw new \RuntimeException('PermisosActividades could not be resolved');
        }

        return $resolved;
    }
}
