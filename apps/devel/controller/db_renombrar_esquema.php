<?php

use devel\model\DBAlterSchema;
use devel\model\entity\GestorDbSchema;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$QEsquemaRef = (string)filter_input(INPUT_POST, 'esquema');
$Qregion = (string)filter_input(INPUT_POST, 'region');
$Qdl = (string)filter_input(INPUT_POST, 'dl');
$Qcomun = (integer)filter_input(INPUT_POST, 'comun');
$Qsv = (integer)filter_input(INPUT_POST, 'sv');
//$Qsf = (integer) filter_input(INPUT_POST, 'sf');

$esquema_old = substr($QEsquemaRef, 0, -1); // quito la v o la f.

$esquema_oldv = $esquema_old . 'v';
//$esquema_oldf = $esquema_old.'f';

$esquema = "$Qregion-$Qdl";
$esquemav = $esquema . 'v';
//$esquemaf = $esquema.'f';

$oDBRol = new core\DBRol();

//  USUARIOS Y CAMBIO NOMBRE ESQUEMA  ///////////////////////////////////

// Hay que pasar como parámetro el nombre de la database, que corresponde al archivo database.inc
// donde están los passwords. En este caso en importar.inc, tenemos al superadmin.
$oConfigDB = new core\ConfigDB('importar');
//coge los valores de public: 1.la database comun; 2.nombre superusuario; 3.pasword superusuario;


$oDBRol = new core\DBRol();

// comun
$configComunP = $oConfigDB->getEsquema('public');
$oConexion = new core\DBConnection($configComunP);
$oConComun = $oConexion->getPDO();
$oDBRol->setDbConexion($oConComun);
// mantener el password:
$oConfigDBComun = new core\ConfigDB('comun');
$configComun = $oConfigDBComun->getEsquema($esquema_old);
$esquema_pwd = $configComun['password'];

$oDBRol->setUser($esquema);
$oDBRol->setPwd($esquema_pwd);
$oDBRol->renombrarSchema($esquema_old); // Cambia el nombre del esquema
$oDBRol->renombrarUsuario($esquema_old); // reescribe el password que ya tenia.
$oConfigDBComun->renombrarListaEsquema('comun', $esquema_old, $esquema);

// Cambiar la tabla db_idschema. (pone el nombre de los tres esquemas, pero sólo en una base de datos)
$oGesDbSchema = new GestorDbSchema();
$oGesDbSchema->cambiarNombre($esquema_old, $esquema, 'comun');

// sv
$configSvP = $oConfigDB->getEsquema('publicv');
$oConexion = new core\DBConnection($configSvP);
$oConSv = $oConexion->getPDO();
$oDBRol->setDbConexion($oConSv);
// mantener el password:
$oConfigDBSv = new core\ConfigDB('sv');
$configSv = $oConfigDBSv->getEsquema($esquema_oldv);
$esquema_pwdv = $configSv['password'];

$oDBRol->setUser($esquemav);
$oDBRol->setPwd($esquema_pwdv);
$oDBRol->renombrarSchema($esquema_oldv); // Cambia el nombre del esquema 
$oDBRol->renombrarUsuario($esquema_oldv); // reescribe el password que ya tenia.
$oConfigDBSv->renombrarListaEsquema('sv', $esquema_oldv, $esquemav);

// Cambiar la tabla db_idschema. (pone el nombre de los tres esquemas, pero sólo en una base de datos)
$oGesDbSchema = new GestorDbSchema();
$oGesDbSchema->cambiarNombre($esquema_old, $esquema, 'sv');

//sv-e
$configSveP = $oConfigDB->getEsquema('publicv-e');
$oConexion = new core\DBConnection($configSveP);
$oConSve = $oConexion->getPDO();
$oDBRol->setDbConexion($oConSve);
// mantener el password:
$oConfigDBSve = new core\ConfigDB('sv-e');
$configSve = $oConfigDBSve->getEsquema($esquema_oldv);
$esquema_pwdve = $configSve['password'];

$oDBRol->setUser($esquemav);
$oDBRol->setPwd($esquema_pwdve);
$oDBRol->renombrarSchema($esquema_oldv); // Cambia el nombre del esquema
// Ya se ha cambiado el usuario para sv.
//$oDBRol->renombrarUsuario($esquema_oldv); // reescribe el password que ya tenia.
$oConfigDBSve->renombrarListaEsquema('sv-e', $esquema_oldv, $esquemav);

// Cambiar la tabla db_idschema. (pone el nombre de los tres esquemas, pero sólo en una base de datos)
$oGesDbSchema = new GestorDbSchema();
$oGesDbSchema->cambiarNombre($esquema_old, $esquema, 'sv-e');

// sf
/*
$configSf = $oConfigDB->getEsquema('publicf');
$oConexion = new core\dbConnection($configSf);
$oConSf = $oConexion->getPDO();
$oDBRol->setDbConexion($oConSf);
// mantener el password:
$esquema_pwdf = $configSf['password'];

$oDBRol->setUser($esquemaf);
$oDBRol->setPwd($esquema_pwdf);
$oDBRol->renombrarSchema($esquema_oldf); // Cambia el nombre del esquema
$oDBRol->renombrarUsuario($esquema_oldf); // reescribe el password que ya tenia.
$oConfigDBSf = new core\ConfigDB('sf');
$oConfigDBSf->renombrarListaEsquema('sf', $esquema_oldf, $esquemaf);

// Cambiar la tabla db_idschema. (pone el nombre de los tres esquemas, pero sólo en una base de datos)
$oGesDbSchema = new GestorDbSchema();
$oGesDbSchema->cambiarNombre($esquema_old,$esquema,'sf');
*/

// ESQUEMAS: CAMBIOS EN TABLAS ////////////////////////////////////////

$RegionNew = $Qregion;
$DlNew = $Qdl;

// comun
if (!empty($Qcomun)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database comun
    $config = $oConfigDB->getEsquema('public'); //de la database comun

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oAlterSchema = new DBAlterSchema();
    $oAlterSchema->setDbConexion($oDevelPC);
    $oAlterSchema->setSchema($esquema);

    // Valores Default:
    $aDefaults = [
        ['tabla' => 'a_actividad_proceso_sf', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_actividad_proceso_sv', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_actividades_dl', 'campo' => 'id_activ', 'valor' => "bigglobal('$esquema'::text, 'a_actividades_dl'::text)"],
        ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'a_fases', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_tareas', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_tareas_proceso', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_tipos_actividad', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'a_tipos_proceso', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'av_cambios_anotados_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'av_cambios_anotados_dl_sf', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'av_cambios_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],

        ['tabla' => 'av_cambios_usuario', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'cd_cargos_activ_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'cp_sacd', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'cu_centros_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'cu_centros_dlf', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'da_ctr_encargados', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'da_ingresos_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'du_gastos_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'du_grupos_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'du_grupos_dl', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'du_periodos', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'du_tarifas', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],

        ['tabla' => 'u_cdc_dl', 'campo' => 'id_ubi', 'valor' => "idglobal('$esquema'::text, 'u_cdc_dl'::text)"],
        ['tabla' => 'u_cdc_dl', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'u_cdc_dl', 'campo' => 'region', 'valor' => "'$RegionNew'::character varying"],
        ['tabla' => 'u_dir_cdc_dl', 'campo' => 'id_direccion', 'valor' => "idglobal('$esquema'::text, 'u_dir_cdc_dl'::text)"],

        ['tabla' => 'x_config_schema', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'xa_tipo_activ_tarifa', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
        ['tabla' => 'xa_tipo_tarifa', 'campo' => 'id_schema', 'valor' => "idschema('$esquema'::text)"],
    ];
    $oAlterSchema->setDefaults($aDefaults);

    // datos
    // REGEXP_REPLACE(source, pattern, replacement_string,[, flags])

    $region_old = strtok($esquema_old, '-');
    $dl_old = strtok('-');

    $aDatos = [
        ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
        ['tabla' => 'av_cambios_dl', 'campo' => 'dl_org', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
        ['tabla' => 'cp_sacd', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'cu_centros_dl', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'cu_centros_dl', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
        ['tabla' => 'cu_centros_dlf', 'campo' => 'dl', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
        ['tabla' => 'cu_centros_dlf', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
    ];

    $oAlterSchema->updateDatosRegexp($aDatos);

}

// sv
if (!empty($Qsv)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sv
    $config = $oConfigDB->getEsquema('publicv');

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oAlterSchema = new DBAlterSchema();
    $oAlterSchema->setDbConexion($oDevelPC);
    $oAlterSchema->setSchema($esquemav);

    // Valores Default:
    $aDefaults = [
        ['tabla' => 'd_asignaturas_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_congresos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_docencia_stgr', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_dossiers_abiertos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_matriculas_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_profesor_ampliacion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_profesor_director', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],

        ['tabla' => 'd_profesor_juramento', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_profesor_latin', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_profesor_stgr', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],

        ['tabla' => 'd_publicaciones', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_teleco_ctr_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_teleco_personas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_titulo_est', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_traslados', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_ultima_asistencia', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'da_plazas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'dap_plazas_peticion_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'du_presentacion_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'e_actas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'e_actas_tribunal_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'e_notas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_agregados', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_agregados', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_agregados'::text)"],
        ['tabla' => 'p_agregados', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'p_de_paso_out', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_de_paso_out', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'p_numerarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_numerarios', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_numerarios'::text)"],
        ['tabla' => 'p_numerarios', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'p_sssc', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_sssc', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_sssc'::text)"],
        ['tabla' => 'p_sssc', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'p_supernumerarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'p_supernumerarios', 'campo' => 'id_nom', 'valor' => "public.idglobal('$esquemav'::text, 'p_supernumerarios'::text)"],
        ['tabla' => 'p_supernumerarios', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'personas_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],

        ['tabla' => 'u_centros_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'u_centros_dl', 'campo' => 'id_ubi', 'valor' => "public.idglobal('$esquemav'::text, 'u_centros_dl'::text)"],
        ['tabla' => 'u_centros_dl', 'campo' => 'dl', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'u_centros_dl', 'campo' => 'region', 'valor' => "'$RegionNew'::character varying"],
        ['tabla' => 'u_cross_ctr_dl_dir', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'u_dir_ctr_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'u_dir_ctr_dl', 'campo' => 'id_direccion', 'valor' => "public.idglobal('$esquemav'::text, 'u_dir_ctr_dl'::text)"],
    ];
    $oAlterSchema->setDefaults($aDefaults);

    // datos
    // REGEXP_REPLACE(source, pattern, replacement_string,[, flags])

    $region_old = strtok($esquema_old, '-');
    $dl_old = strtok('-');

    $aDatos = [
        ['tabla' => 'p_agregados', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'p_de_paso_out', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'p_numerarios', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'p_sssc', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'p_supernumerarios', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'u_centros_dl', 'campo' => 'dl', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'u_centros_dl', 'campo' => 'region', 'pattern' => "\m$region_old\M", 'replacement' => "$RegionNew"],
        ['tabla' => 'd_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'd_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'da_plazas_dl', 'campo' => 'dl_tabla', 'pattern' => "\m$dl_old(f?)\M", 'replacement' => "$DlNew\\1"],
    ];

    $oAlterSchema->updateDatosRegexp($aDatos);

    // borrar
    $aDatos = [
        ['tabla' => 'u_centros_dl', 'campo' => 'id_zona'],
    ];
    $oAlterSchema->setNullDatos($aDatos);

    // Todos los esquemas:
    $aDatos = [
        ['tabla' => 'global.d_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'global.d_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
    ];
    $oAlterSchema->updateDatosRegexpTodos($aDatos);

    /*
    * da_plazas_dl       
    cedidas {"dlb": 2, "dlp": 3, "dlv": 1, "dlmE": 1, "dlmO": 1, "dlst": 1}
    */
    $oAlterSchema->updateCedidasAll($dl_old, $DlNew);

    // Esquema sv-e
    $oConfigDB = new core\ConfigDB('importar'); //de la database sv
    $config = $oConfigDB->getEsquema('publicv-e');

    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

    $oAlterSchema = new DBAlterSchema();
    $oAlterSchema->setDbConexion($oDevelPC);
    $oAlterSchema->setSchema($esquemav);

    // Valores Default:
    $aDefaults = [
        ['tabla' => 'a_sacd_textos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_cross_usuarios_grupos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_grupmenu', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_grupmenu_rol', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_grupo_permmenu', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_grupos_y_usuarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_menus', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_usuarios', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_usuarios_ctr_perm', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'aux_usuarios_perm', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'av_cambios_usuario_objeto_pref', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'av_cambios_usuario_objeto_pref', 'campo' => 'dl_org', 'valor' => "'$DlNew'::character varying"],
        ['tabla' => 'av_cambios_usuario_propiedades_pref', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_asistentes_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_asistentes_out', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'd_cargos_activ_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_datos_cgi', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_horario_excepcion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_sacd_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_sacd_horario_excepcion', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_sacd_observ', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_textos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargo_tipo', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'encargos_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'm0_mods_installed_dl', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'propuesta_encargo_sacd_horario', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'propuesta_encargos_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'web_preferencias', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'zonas', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'zonas_grupos', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
        ['tabla' => 'zonas_sacd', 'campo' => 'id_schema', 'valor' => "public.idschema('$esquemav'::text)"],
    ];
    $oAlterSchema->setDefaults($aDefaults);

    // datos
    // Todos los esquemas:
    $aDatos = [
        ['tabla' => 'global.d_asistentes_dl', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
        ['tabla' => 'publicv.d_asistentes_de_paso', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$DlNew"],
    ];
    $oAlterSchema->updateDatosRegexpTodos($aDatos);

    $oAlterSchema->updatePropietarioAll($dl_old, $DlNew);
}

/*
// sf
if (!empty($Qsf)) {
    $oConfigDB = new core\ConfigDB('importar'); //de la database sf
    $config = $oConfigDB->getEsquema('publicf');
    $oConexion = new core\dbConnection($config);
    $oDevelPC = $oConexion->getPDO();

	// CREAR Esquema sf
    $oDBRol->setDbConexion($oDevelPC);
    $oDBRol->setUser($esquemaf);
    // Necesito tener los permisos del usuario que tiene las tablas padre para poder crear las heredadas.
    // Despues hay que quitarlo para que no tenga permisos para la tabla padre.
    $oDBRol->addGrupo('orbixf');
	$oDBRol->crearSchema();
	// Copiar esquema
    $oDBEsquema = new core\DBEsquema();
    $oDBEsquema->setConfig($config);
    $oDBEsquema->setRegionRef($RegionRef);
    $oDBEsquema->setDlRef($DlRef);
    $oDBEsquema->setRegionNew($RegionNew);
    $oDBEsquema->setDlNew($DlNew);
    $oDBEsquema->crear();

    // Hay que quitar a los usuarios del grupo para que no tenga permisos para la tabla padre.
	$oDBRol->delGrupo('orbixf');

    // Llenar la tabla db_idschema (todos, aunque de momento no exista sv o sf).
    $schema = $RegionNew.'-'.$DlNew;
    $oGesDbSchema = new GestorDbSchema();
    $oGesDbSchema->llenarNuevo($schema,'sf');

}
*/

echo "<br>";
echo sprintf(_("se ha cambiado el nombre del esquema"));
