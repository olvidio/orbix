<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\cambios\application\legacy\Avisos;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\PermAccion;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\Cambio;
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
        private ActividadParaAvisoLookup $actividadParaAvisoLookup,
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
        /** @var array<string, true> cambios omitidos (sin anotar) por actividad inexistente u otro error recuperable */
        $cambiosOmitidos = [];

        $ActividadParaAvisoLookup = $this->actividadParaAvisoLookup;
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

                if ($id_activ > 0 && $ActividadParaAvisoLookup->find($id_activ) === null) {
                    $this->registrarCambioNoProcesado(
                        $oCambio,
                        sprintf('actividad %d no encontrada', $id_activ),
                        $err_fila,
                        $cambiosOmitidos,
                    );
                    continue;
                }

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
                    if ($ImportadaRepository->findById($id_activ) === null) {
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
                        $oActividad = $ActividadParaAvisoLookup->find($id_activ);
                        if ($oActividad === null || $oF_cmb === null) {
                            continue;
                        }
                        $oF_fin = $oActividad->getF_fin();
                        if ($oF_fin !== null && $oF_cmb > $oF_fin) {
                            continue;
                        }
                    }

                    /////////////////// COMPARAR STATUS / FASES //////////////////////////////////////////
                    // Otra dl: matching por estado (id_fase_ref → status si hay procesos), sin permiso ocupado.
                    if (!ConfigGlobal::is_app_installed('procesos')
                        || !\src\shared\domain\helpers\FuncTablasSupport::isTrue($dl_propia)
                    ) {
                        $statusActual = $this->statusActividadParaMatching(
                            $id_activ,
                            $id_status_cmb ?? 0,
                        );
                        if ($statusActual === null) {
                            continue;
                        }
                        if ($aFases_cmb !== [] && \src\shared\domain\helpers\FuncTablasSupport::isTrue($dl_propia)) {
                            $oPermActiv = $this->permisoActualActividad(
                                $id_usuario,
                                $id_activ,
                                (string) $id_tipo_activ,
                                (string) ($dl_org ?? ''),
                                $aFases_cmb,
                                $afecta,
                                $oCambio,
                                $err_fila,
                                $cambiosOmitidos,
                            );
                            if ($oPermActiv === null) {
                                continue 2;
                            }
                            if (!$oPermActiv->have_perm_activ('ocupado')) {
                                continue;
                            }
                        }
                        $statusReferencia = $id_fase_ref;
                        if (ConfigGlobal::is_app_installed('procesos')) {
                            $statusReferencia = $this->statusDeFaseReferencia(
                                $id_fase_ref,
                                $id_tipo_activ,
                                $TipoDeActividadRepository,
                                $TareaProcesoRepository,
                            );
                        }
                        $fase_correcta = self::evaluarFaseCorrectaSinProcesos(
                            $statusActual,
                            $statusReferencia,
                            $aviso_on,
                            $aviso_off,
                        );
                    } elseif (empty($aFases_cmb)) {
                        // Si el id_fase es NULL, hay que mirar el id_status (solo con procesos).
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
                        /////////////////// COMPARAR FASES (con procesos) //////////////////////////////////////////
                        // fase on
                        if (in_array($id_fase_ref, $aFases_cmb)) {
                            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_on)) {
                                $oPermActiv = $this->permisoActualActividad(
                                    $id_usuario,
                                    $id_activ,
                                    (string) $id_tipo_activ,
                                    (string) ($dl_org ?? ''),
                                    $aFases_cmb,
                                    $afecta,
                                    $oCambio,
                                    $err_fila,
                                    $cambiosOmitidos,
                                );
                                if ($oPermActiv === null) {
                                    continue 2;
                                }
                                if (!$oPermActiv->have_perm_activ('ocupado')) {
                                    continue;
                                }

                                $fase_correcta = 1;
                            }
                        } else {
                            // fase off
                            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_off)) {
                                $oPermActiv = $this->permisoActualActividad(
                                    $id_usuario,
                                    $id_activ,
                                    (string) $id_tipo_activ,
                                    (string) ($dl_org ?? ''),
                                    $aFases_cmb,
                                    $afecta,
                                    $oCambio,
                                    $err_fila,
                                    $cambiosOmitidos,
                                );
                                if ($oPermActiv === null) {
                                    continue 2;
                                }
                                if (!$oPermActiv->have_perm_activ('ocupado')) {
                                    continue;
                                }

                                $fase_correcta = 1;
                            }
                        }
                    }

                    if ($fase_correcta === 1) {
                        $cListaPropiedades = $CambiosUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadPrefs(['id_item_usuario_objeto' => $id_item_usuario_objeto]);
                        if ($cListaPropiedades === []) {
                            if ($oAvisos->me_afecta($propiedad_cmb ?? '', $id_activ, $valor_old_cmb, $valor_new_cmb, $id_pau, (string) $sObjeto)) {
                                $apuntar = true;
                            }
                        } else {
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
                if ($this->todosCambiosPendientesSonOmitidos($cNuevosCambios, $cambiosOmitidos)) {
                    $this->logAvisosGenerarTabla(sprintf(
                        'finalizado: %d cambio(s) no procesado(s) (permanecen pendientes en cola)',
                        count($cambiosOmitidos),
                    ));
                    break;
                }
                $oAvisos->borrar_pid($username, $esquema);
                $bucle_infinito = true;
                $this->logAvisosGenerarTabla(
                    'bucle infinito: el numero de cambios pendientes no disminuye tras un ciclo completo',
                );
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
     * Con procesos instalados, id_fase_ref de la preferencia es id_fase del tipo de proceso;
     * para comparar con el status de la actividad hay que traducirlo.
     */
    private function statusDeFaseReferencia(
        int $id_fase_ref,
        int $id_tipo_activ,
        TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        TareaProcesoRepositoryInterface $tareaProcesoRepository,
    ): int {
        if ($id_fase_ref <= 0) {
            return 0;
        }

        $cTiposActividad = $tipoDeActividadRepository->getTiposDeActividades(['id_tipo_activ' => $id_tipo_activ]);
        if ($cTiposActividad === []) {
            return $id_fase_ref;
        }

        $id_tipo_proceso = $cTiposActividad[0]->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
        $cTareasProceso = $tareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $id_tipo_proceso,
            'id_fase' => $id_fase_ref,
        ]);
        if ($cTareasProceso === []) {
            return $id_fase_ref;
        }

        return $cTareasProceso[0]->getStatus();
    }

    /**
     * Estado de la actividad para matching sin procesos: actual en BD o anotado en el cambio.
     */
    private function statusActividadParaMatching(
        int $id_activ,
        int $id_status_cmb,
    ): ?int {
        if ($id_activ > 0) {
            $oActividad = $this->actividadParaAvisoLookup->find($id_activ);
            if ($oActividad !== null) {
                return $oActividad->getStatus();
            }
        }

        if ($id_status_cmb > 0) {
            return $id_status_cmb;
        }

        return null;
    }

    /**
     * aviso_on: actividad en el estado id_fase_ref; aviso_off: actividad fuera de ese estado.
     */
    private static function evaluarFaseCorrectaSinProcesos(
        int $statusActual,
        int $id_fase_ref,
        bool $aviso_on,
        bool $aviso_off,
    ): int {
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_on) && $statusActual === $id_fase_ref) {
            return 1;
        }
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($aviso_off) && $statusActual !== $id_fase_ref) {
            return 1;
        }

        return 0;
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

    /**
     * Evalúa permisos sobre la actividad del cambio usando tipo/dl del propio cambio
     * (evita lookup en BD si la actividad ya no existe).
     *
     * @param list<int> $aFases_cmb
     * @param array<string, true> &$cambiosOmitidos
     */
    private function permisoActualActividad(
        int $id_usuario,
        int $id_activ,
        string $id_tipo_activ,
        string $dl_org,
        array $aFases_cmb,
        string $afecta,
        Cambio $oCambio,
        string &$err_fila,
        array &$cambiosOmitidos,
    ): ?PermAccion {
        if ($id_activ > 0 && $this->actividadParaAvisoLookup->find($id_activ) === null) {
            $this->registrarCambioNoProcesado(
                $oCambio,
                sprintf('actividad %d no encontrada', $id_activ),
                $err_fila,
                $cambiosOmitidos,
            );

            return null;
        }

        $oPermActividades = $this->makePermisosActividades($id_usuario);
        try {
            if ($id_tipo_activ !== '' && $dl_org !== '') {
                $oPermActividades->setActividad($id_activ, $id_tipo_activ, $dl_org);
            } else {
                $oPermActividades->setActividad($id_activ);
            }
            $oPermActividades->setFasesCompletadas($aFases_cmb);
        } catch (\RuntimeException $e) {
            $this->registrarCambioNoProcesado($oCambio, $e->getMessage(), $err_fila, $cambiosOmitidos);

            return null;
        }

        return $oPermActividades->getPermisoActual($afecta);
    }

    /**
     * @param list<Cambio> $cambios
     * @param array<string, true> $cambiosOmitidos
     */
    private function todosCambiosPendientesSonOmitidos(
        array $cambios,
        array $cambiosOmitidos,
    ): bool {
        if ($cambios === []) {
            return false;
        }
        foreach ($cambios as $oCambio) {
            if (isset($cambiosOmitidos[self::cambioKey($oCambio)])) {
                continue;
            }
            $id_activ = $oCambio->getId_activ();
            if ($id_activ > 0 && $this->actividadParaAvisoLookup->find($id_activ) === null) {
                continue;
            }

            return false;
        }

        return true;
    }

    private static function cambioKey(Cambio $oCambio): string
    {
        return $oCambio->getId_schema() . '_' . $oCambio->getId_item_cambio();
    }

    /**
     * @param array<string, true> $cambiosOmitidos
     */
    private function registrarCambioNoProcesado(
        Cambio $oCambio,
        string $motivo,
        string &$err_fila,
        array &$cambiosOmitidos,
    ): void
    {
        $id_schema = $oCambio->getId_schema();
        $id_item = $oCambio->getId_item_cambio();
        $id_activ = $oCambio->getId_activ();
        $objeto = (string) ($oCambio->getObjeto() ?? '');
        $propiedad = (string) ($oCambio->getPropiedad() ?? '');

        $cambiosOmitidos[self::cambioKey($oCambio)] = true;

        $this->logAvisosGenerarTabla(sprintf(
            'cambio NO procesado schema=%d item=%d id_activ=%d objeto=%s propiedad=%s motivo=%s',
            $id_schema,
            $id_item,
            $id_activ,
            $objeto,
            $propiedad,
            $motivo,
        ));

        $err_fila .= '<tr>';
        $err_fila .= '<td>' . htmlspecialchars((string) $id_schema, ENT_QUOTES, 'UTF-8') . '</td>';
        $err_fila .= '<td>' . htmlspecialchars((string) $id_item, ENT_QUOTES, 'UTF-8') . '</td>';
        $err_fila .= '<td>' . htmlspecialchars((string) $id_activ, ENT_QUOTES, 'UTF-8') . '</td>';
        $err_fila .= '<td colspan="1">' . htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8') . '</td>';
        $err_fila .= '</tr>';
    }

    private function logAvisosGenerarTabla(string $mensaje): void
    {
        $line = sprintf("[%s] AvisosGenerarTabla: %s\n", date('c'), $mensaje);
        error_log($line, 3, ConfigGlobal::$directorio . '/log/avisos.err');
    }
}
