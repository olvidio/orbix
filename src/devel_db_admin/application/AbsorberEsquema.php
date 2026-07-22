<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\devel_db_admin\infrastructure\DBAlterSchema;
use src\shared\config\ReplicaSelectPolicy;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBRol;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Absorción de un esquema DL en otro (comun + sv + sv-e): copias, actualizaciones y renombre del esquema origen.
 *
 * Notas históricas (tablas / exclusiones en absorciones, referencia):
 *
 * comun — copias: cd_cargos_activ_dl, cp_sacd, cu_centros_dl, cu_centros_dlf, a_tipos_actividad;
 * no se guarda nada de procesos: a_actividad_proceso_sf/sv, a_fases, a_tareas, a_tareas_proceso, a_tipos_proceso,
 * av_cambios_anotados_dl, av_cambios_anotados_dl_sf, av_cambios_dl, av_cambios_usuario, x_config_schema;
 * revisar du_tarifas / xa_tipo_activ_tarifa / xa_tipo_tarifa si aplica.
 *
 * sv — absorciones: d_matriculas_activ_dl (acta actual; posibles problemas en CA en ejecución => borrar y nuevo);
 * d_asignaturas_activ_dl (sobrescritura si el profesor de la dl matriz es preceptor y el de la dl_del no);
 * d_cargos_activ_dl (duplicados se borran).
 */
final class AbsorberEsquema
{
    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly CargoRepositoryInterface $cargoRepository,
    ) {
    }

    public function execute(string $esquemaMatriz, string $esquemaDel): AbsorberEsquemaResult
    {
        $errores = [];

        $esquemaMatrizv = $esquemaMatriz . 'v';
        $esquemaDelv = $esquemaDel . 'v';

        [$region_new, $dl_new] = $this->parseRegionDl($esquemaMatriz);

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema($this->cargoRepository);
        $oAlterSchema->setContinuarEnError(true);
        $oAlterSchema->setDbConexion($oDevelPC);
        $oAlterSchema->setSchema($esquemaMatriz);
        $oAlterSchema->setSchemaDel($esquemaDel);

        $aInserts = [];

        $tabla = 'a_actividades_dl';
        $campos = 'id_activ, id_tipo_activ, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, id_repeticion, publicado, id_tabla, plazas';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'da_ingresos_dl';
        $campos = 'id_activ, ingresos, num_asistentes, ingresos_previstos, num_asistentes_previstos, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'a_importadas';
        $campos = 'id_activ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $tabla = 'u_cdc_dl';
        $campos = 'tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'u_dir_cdc_dl';
        $campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'u_cross_cdc_dl_dir';
        $campos = 'id_ubi, id_direccion, propietario, principal';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_gastos_dl';
        $campos = 'id_ubi, f_gasto, tipo, cantidad';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_grupos_dl';
        $campos = 'id_ubi_padre, id_ubi_hijo';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_periodos';
        $campos = 'id_ubi, f_ini, f_fin, sfsv';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_teleco_cdc_dl';
        $campos = 'id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'da_ctr_encargados';
        $campos = 'id_activ, id_ubi, num_orden, encargo';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $oAlterSchema->setInserts($aInserts);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $config = $oConfigDB->getEsquema('publicv');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema($this->cargoRepository);
        $oAlterSchema->setContinuarEnError(true);
        $oAlterSchema->setDbConexion($oDevelPC);
        $oAlterSchema->setSchema($esquemaMatrizv);
        $oAlterSchema->setSchemaDel($esquemaDelv);

        $aInserts = [];

        $tabla = 'd_asignaturas_activ_dl';
        $campos = 'id_activ, id_asignatura, id_profesor, avis_profesor, tipo, f_ini, f_fin';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_congresos';
        $campos = 'id_nom, congreso, lugar, f_ini, f_fin, organiza, tipo';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_docencia_stgr';
        $campos = 'id_nom, id_asignatura, id_activ, tipo, curso_inicio, acta';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_dossiers_abiertos';
        $campos = 'tabla, id_pau, id_tipo_dossier, f_ini, f_camb_dossier, active, f_active';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_matriculas_activ_dl';
        $campos = 'id_activ, id_asignatura, id_nom, id_situacion, preceptor, id_nivel, nota_num, nota_max, id_preceptor, acta';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_profesor_ampliacion';
        $campos = 'id_nom, id_asignatura, escrito_nombramiento, f_nombramiento, escrito_cese, f_cese';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_profesor_director';
        $campos = 'id_nom, id_departamento, escrito_nombramiento, f_nombramiento, escrito_cese, f_cese';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_profesor_juramento';
        $campos = 'id_nom, f_juramento';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_profesor_latin';
        $campos = 'id_nom, latin';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_profesor_stgr';
        $campos = 'id_nom, id_departamento, escrito_nombramiento, f_nombramiento, id_tipo_profesor, escrito_cese, f_cese';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_publicaciones';
        $campos = 'id_nom, tipo_publicacion, titulo, editorial, coleccion, f_publicacion, pendiente, referencia, lugar, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_teleco_ctr_dl';
        $campos = 'id_ubi, id_tipo_teleco, id_desc_teleco, num_teleco, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_teleco_personas_dl';
        $campos = 'id_nom, id_tipo_teleco, num_teleco, observ, id_desc_teleco';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_titulo_est';
        $campos = 'id_nom, titulo, centro_dnt, eclesiastico, year';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_traslados';
        $campos = 'id_nom, f_traslado, tipo_cmb, id_ctr_origen, ctr_origen, id_ctr_destino, ctr_destino, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_ultima_asistencia';
        $campos = 'id_nom, id_tipo_activ, f_ini, descripcion, cdr';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $tabla = 'da_plazas_dl';
        $campos = 'id_activ, id_dl, plazas, cl, dl_tabla, cedidas';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'dap_plazas_peticion_dl';
        $campos = 'id_nom, id_activ, orden, tipo';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_presentacion_dl';
        $campos = 'id_direccion, id_ubi, pres_nom, pres_telf, pres_mail, zona, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'e_actas_dl';
        $campos = 'acta, id_asignatura, id_activ, f_acta, libro, pagina, linea, lugar, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'e_actas_tribunal_dl';
        $campos = 'acta, examinador, orden';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'e_notas_dl';
        $campos = 'id_nom, id_nivel, id_asignatura, id_situacion, acta, f_acta, detalle, preceptor, id_preceptor, epoca, id_activ, nota_num, nota_max, tipo_acta';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'p_agregados';
        $campos = 'id_nom, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, idioma_preferido, situacion, f_situacion, apel_fam, inc, f_inc, nivel_stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'p_de_paso_out';
        $campos = 'id_nom, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, idioma_preferido, situacion, f_situacion, apel_fam, inc, f_inc, nivel_stgr, edad, profesion, eap, observ, profesor_stgr, lugar_nacimiento';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'p_numerarios';
        $campos = 'id_nom, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, idioma_preferido, situacion, f_situacion, apel_fam, inc, f_inc, nivel_stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'p_supernumerarios';
        $campos = 'id_nom, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, idioma_preferido, situacion, f_situacion, apel_fam, inc, f_inc, nivel_stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'p_sssc';
        $campos = 'id_nom, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, idioma_preferido, situacion, f_situacion, apel_fam, inc, f_inc, nivel_stgr, profesion, eap, observ, id_ctr, lugar_nacimiento';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'u_centros_dl';
        $campos = 'tipo_ubi, id_ubi, nombre_ubi, pais, active, f_active, sv, sf, tipo_ctr, tipo_labor, cdc, id_ctr_padre, n_buzon, num_pi, num_cartas, observ, num_habit_indiv, plazas, sede, num_cartas_mensuales';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'u_dir_ctr_dl';
        $campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'u_cross_ctr_dl_dir';
        $campos = 'id_ubi, id_direccion, propietario, principal';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $oAlterSchema->setInserts($aInserts);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        [$region_old, $dl_old] = $this->parseRegionDl($esquemaDel);

        $this->actualizarMapaPrefijoActa($oDevelPC, $esquemaDel, $esquemaMatriz, $dl_old, $dl_new, $errores);

        $gesDelegaciones = $this->delegacionRepository;
        $cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_old, 'region' => $region_old]);
        $id_dl_old = $cDelegaciones[0]->getIdDlVo()->value();
        $cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_new, 'region' => $region_new]);
        $id_dl_new = $cDelegaciones[0]->getIdDlVo()->value();

        $aDatos = [
            ['tabla' => 'da_plazas_dl', 'campo' => 'id_dl', 'old' => "$id_dl_old", 'new' => "$id_dl_new"],
        ];

        $oAlterSchema->updateDatos($aDatos);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $aDatos = [
            ['tabla' => 'da_plazas_dl', 'campo' => 'dl_tabla', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];

        $oAlterSchema->updateDatosRegexp($aDatos);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $aDatos = [
            ['tabla' => 'global.d_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
            ['tabla' => 'global.d_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];
        $oAlterSchema->updateDatosRegexpTodos($aDatos);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $oAlterSchema->updateCedidasAll($dl_old, $dl_new);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $config = $oConfigDB->getEsquema('publicv-e');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema($this->cargoRepository);
        $oAlterSchema->setContinuarEnError(true);
        $oAlterSchema->setDbConexion($oDevelPC);

        $oAlterSchema->setSchema($esquemaMatrizv);
        $oAlterSchema->setSchemaDel($esquemaDelv);

        $oAlterSchema->asistentesOut2Dl($dl_new);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $oAlterSchema->asistentesOut2DlPropia();
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $aInserts = [];

        $tabla = 'd_asistentes_dl';
        $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_asistentes_out';
        $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $oAlterSchema->setInserts($aInserts);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $oAlterSchema->updatePropietarioAll($dl_old, $dl_new);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());
        $aDatos = [
            ['tabla' => 'global.d_asistentes_dl', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
            ['tabla' => 'publicv.d_asistentes_de_paso', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];
        $oAlterSchema->updateDatosRegexpTodos($aDatos);
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $oAlterSchema->comprobarImportadas();
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $oAlterSchema->insertarCargos();
        $errores = array_merge($errores, $oAlterSchema->consumirErrores());

        $DelegacionRepository = $this->delegacionRepository;
        $oDelegacion = $DelegacionRepository->findById($id_dl_old);
        if ($oDelegacion === null) {
            $errores[] = _('Delegación origen no encontrada.');
        } else {
            $oDelegacion->setActive(false);
            if ($DelegacionRepository->Guardar($oDelegacion) === false) {
                $errores[] = _('hay un error, no se ha guardado') . ': ' . $DelegacionRepository->getErrorTxt();
            }
        }

        $oDBRol = new DBRol();
        $esquema_zz = 'zz' . $esquemaDel;
        $esquemav_zz = 'zz' . $esquemaDelv;
        $incluirSelect = ReplicaSelectPolicy::incluirSelect();

        $clavesComun = ['public'];
        if ($incluirSelect) {
            $clavesComun[] = 'public_select';
        }
        foreach ($clavesComun as $clave) {
            $this->renombrarEsquemaDisuelto(
                $oConfigDB,
                $oDBRol,
                $oAlterSchema,
                $clave,
                $esquemaDel,
                $esquema_zz,
                $errores,
            );
        }

        foreach (['publicv'] as $clave) {
            $this->renombrarEsquemaDisuelto(
                $oConfigDB,
                $oDBRol,
                $oAlterSchema,
                $clave,
                $esquemaDelv,
                $esquemav_zz,
                $errores,
            );
        }

        $clavesSve = ['publicv-e'];
        if ($incluirSelect) {
            $clavesSve[] = 'publicv-e_select';
        }
        foreach ($clavesSve as $clave) {
            $this->renombrarEsquemaDisuelto(
                $oConfigDB,
                $oDBRol,
                $oAlterSchema,
                $clave,
                $esquemaDelv,
                $esquemav_zz,
                $errores,
            );
        }

        $lines = [
            sprintf(_("se ha pasado la %s a %s"), $dl_old, $dl_new),
            'Hay que borrar el esquema viejo. Se le ha cambiado el nombre',
        ];

        return new AbsorberEsquemaResult($lines, $errores);
    }

    /**
     * Fuente única: `public.mapa_prefijo_acta_esquema` (sv/sf).
     * Registra el prefijo absorbido → matriz y reasigna filas que apuntaban al esquema disuelto.
     *
     * @param list<string> $errores
     */
    private function actualizarMapaPrefijoActa(
        \PDO $pdo,
        string $esquemaDel,
        string $esquemaMatriz,
        string $dlOld,
        string $dlNew,
        array &$errores,
    ): void {
        try {
            $regclassStmt = $pdo->query("SELECT to_regclass('public.mapa_prefijo_acta_esquema')");
            $exists = $regclassStmt !== false ? $regclassStmt->fetchColumn() : false;
            if ($exists === false || $exists === null || $exists === '') {
                $errores[] = 'mapa_prefijo_acta_esquema no existe; aplicar migración 202607221200 antes de absorber';
                return;
            }

            $stmt = $pdo->prepare(
                'INSERT INTO public.mapa_prefijo_acta_esquema (pref, esquema_base, notas)
                 VALUES (:pref, :base, :notas)
                 ON CONFLICT (pref) DO UPDATE
                 SET esquema_base = EXCLUDED.esquema_base,
                     notas = COALESCE(EXCLUDED.notas, public.mapa_prefijo_acta_esquema.notas)'
            );
            $stmt->execute([
                'pref' => strtolower($dlOld),
                'base' => $esquemaMatriz,
                'notas' => "fusionada en $dlNew",
            ]);
            $stmt->execute([
                'pref' => strtolower($dlNew),
                'base' => $esquemaMatriz,
                'notas' => null,
            ]);

            $upd = $pdo->prepare(
                'UPDATE public.mapa_prefijo_acta_esquema
                 SET esquema_base = :nuevo
                 WHERE esquema_base = :viejo'
            );
            $upd->execute(['nuevo' => $esquemaMatriz, 'viejo' => $esquemaDel]);
        } catch (\Throwable $e) {
            $errores[] = 'mapa_prefijo_acta_esquema: ' . $e->getMessage();
        }
    }

    /**
     * @param list<string> $errores
     */
    private function renombrarEsquemaDisuelto(
        ConfigDB $oConfigDB,
        DBRol $oDBRol,
        DBAlterSchema $oAlterSchema,
        string $claveImportar,
        string $esquemaDel,
        string $esquemaZz,
        array &$errores,
    ): void {
        try {
            $config = $oConfigDB->getEsquema($claveImportar);
            $pdo = (new DBConnection($config))->getPDO();
            $oDBRol->setDbConexion($pdo);
            $oDBRol->setUser($esquemaZz);

            if ($this->existeEsquema($pdo, $esquemaDel) && !$this->existeEsquema($pdo, $esquemaZz)) {
                $oDBRol->renombrarSchema($esquemaDel);
            }

            if ($this->existeEsquema($pdo, $esquemaZz)) {
                $oAlterSchema->quitarHerencias($pdo, $esquemaZz);
                $errores = array_merge($errores, $oAlterSchema->consumirErrores());
            }
        } catch (\Throwable $e) {
            $errores[] = sprintf(
                'renombrarSchema %s (%s → %s): %s',
                $claveImportar,
                $esquemaDel,
                $esquemaZz,
                $e->getMessage(),
            );
        }
    }

    private function existeEsquema(\PDO $pdo, string $nombre): bool
    {
        if ($nombre === '') {
            return false;
        }

        try {
            $st = $pdo->prepare('SELECT 1 FROM pg_namespace WHERE nspname = :n LIMIT 1');
            $st->execute(['n' => $nombre]);

            return (bool) $st->fetchColumn();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function parseRegionDl(string $esquema): array
    {
        $partes = explode('-', trim($esquema), 2);
        $region = $partes[0];
        $dl = $partes[1] ?? '';

        return [$region, $dl];
    }
}
