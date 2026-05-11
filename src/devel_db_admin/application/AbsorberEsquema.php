<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\devel_db_admin\infrastructure\DBAlterSchema;
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
        private readonly object $container,
    ) {
    }

    public function execute(string $esquemaMatriz, string $esquemaDel): AbsorberEsquemaResult
    {
        $esquemaMatrizv = $esquemaMatriz . 'v';
        $esquemaDelv = $esquemaDel . 'v';

        $region_new = strtok($esquemaMatriz, '-');
        $dl_new = strtok('-');

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema();
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

        $config = $oConfigDB->getEsquema('publicv');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema();
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

        $region_old = strtok($esquemaDel, '-');
        $dl_old = strtok('-');

        $gesDelegaciones = $this->container->get(DelegacionRepositoryInterface::class);
        $cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_old, 'region' => $region_old]);
        $id_dl_old = $cDelegaciones[0]->getIdDlVo()?->value() ?? 0;
        $cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_new, 'region' => $region_new]);
        $id_dl_new = $cDelegaciones[0]->getIdDlVo()?->value() ?? 0;

        $aDatos = [
            ['tabla' => 'da_plazas_dl', 'campo' => 'id_dl', 'old' => "$id_dl_old", 'new' => "$id_dl_new"],
        ];

        $oAlterSchema->updateDatos($aDatos);

        $aDatos = [
            ['tabla' => 'da_plazas_dl', 'campo' => 'dl_tabla', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];

        $oAlterSchema->updateDatosRegexp($aDatos);

        $aDatos = [
            ['tabla' => 'global.d_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
            ['tabla' => 'global.d_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];
        $oAlterSchema->updateDatosRegexpTodos($aDatos);

        $oAlterSchema->updateCedidasAll($dl_old, $dl_new);

        $config = $oConfigDB->getEsquema('publicv-e');

        $oConexion = new DBConnection($config);
        $oDevelPC = $oConexion->getPDO();

        $oAlterSchema = new DBAlterSchema();
        $oAlterSchema->setDbConexion($oDevelPC);

        $oAlterSchema->setSchema($esquemaMatrizv);
        $oAlterSchema->setSchemaDel($esquemaDelv);

        $oAlterSchema->asistentesOut2Dl($dl_new);

        $oAlterSchema->asistentesOut2DlPropia();

        $aInserts = [];

        $tabla = 'd_asistentes_dl';
        $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_asistentes_out';
        $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        $oAlterSchema->setInserts($aInserts);

        $oAlterSchema->updatePropietarioAll($dl_old, $dl_new);
        $aDatos = [
            ['tabla' => 'global.d_asistentes_dl', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
            ['tabla' => 'publicv.d_asistentes_de_paso', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
        ];
        $oAlterSchema->updateDatosRegexpTodos($aDatos);

        $oAlterSchema->comprobarImportadas();

        $oAlterSchema->insertarCargos();

        $DelegacionRepository = $this->container->get(DelegacionRepositoryInterface::class);
        $oDelegacion = $DelegacionRepository->findById($id_dl_old);
        $oDelegacion->setActive(false);
        if ($DelegacionRepository->Guardar($oDelegacion) === false) {
            $error_txt = _("hay un error, no se ha guardado") . "\n" . $DelegacionRepository->getErrorTxt();
        }

        $oDBRol = new DBRol();
        $esquema_zz = 'zz' . $esquemaDel;
        $esquemav_zz = 'zz' . $esquemaDelv;

        $configComunP = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($configComunP);
        $oConComun = $oConexion->getPDO();
        $oDBRol->setDbConexion($oConComun);
        $oDBRol->setUser($esquema_zz);
        $oDBRol->renombrarSchema($esquemaDel);
        $oAlterSchema->quitarHerencias($oConComun, $esquema_zz);

        $configSv = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($configSv);
        $oConSv = $oConexion->getPDO();
        $oDBRol->setDbConexion($oConSv);
        $oDBRol->setUser($esquemav_zz);
        $oDBRol->renombrarSchema($esquemaDelv);
        $oAlterSchema->quitarHerencias($oConSv, $esquemav_zz);

        $configSve = $oConfigDB->getEsquema('publicv-e');
        $oConexion = new DBConnection($configSve);
        $oConSve = $oConexion->getPDO();
        $oDBRol->setDbConexion($oConSve);
        $oDBRol->setUser($esquemav_zz);
        $oDBRol->renombrarSchema($esquemaDelv);
        $oAlterSchema->quitarHerencias($oConSve, $esquemav_zz);

        $lines = [
            sprintf(_("se ha pasado la %s a %s"), $dl_old, $dl_new),
            'Hay que borrar el esquema viejo. Se le ha cambiado el nombre',
        ];

        return new AbsorberEsquemaResult($lines);
    }
}
