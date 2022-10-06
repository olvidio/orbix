<?php

use devel\model\DBAlterSchema;
use ubis\model\entity\GestorDelegacion;
use ubis\model\entity\Delegacion;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$QEsquemaMatriz = (string)filter_input(INPUT_POST, 'esquema_matriz');
$QEsquemaDel = (string)filter_input(INPUT_POST, 'esquema_del');

$esquemaMatrizv = $QEsquemaMatriz . 'v';
//$esquemaMatrizf = $QEsquemaMatriz.'f';

$esquemaDelv = $QEsquemaDel . 'v';
//$esquemaDelf = $QEsquemaDel.'f';


// ESQUEMAS: CAMBIOS EN TABLAS ////////////////////////////////////////

$region_new = strtok($QEsquemaMatriz, '-');
$dl_new = strtok('-');

// comun
/*
 * copias
cd_cargos_activ_dl
cp_sacd
cu_centros_dl
cu_centros_dlf
a_tipos_actividad
// OJO No se guarda nada de procesos:
a_actividad_proceso_sf
a_actividad_proceso_sv
a_fases
a_tareas
a_tareas_proceso
a_tipos_proceso
av_cambios_anotados_dl
av_cambios_anotados_dl_sf
av_cambios_dl
av_cambios_usuario
x_config_schema
// OJO revisar si el tipo tarifa es ok
$tabla = 'du_tarifas';
$campos = 'id_ubi, id_tarifa, year, cantidad, observ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
//xa_tipo_activ_tarifa
//xa_tipo_tarifa
*/

$oConfigDB = new core\ConfigDB('importar'); //de la database comun
$config = $oConfigDB->getEsquema('public'); //de la database comun

$oConexion = new core\DBConnection($config);
$oDevelPC = $oConexion->getPDO();

$oAlterSchema = new DBAlterSchema();
$oAlterSchema->setDbConexion($oDevelPC);

$oAlterSchema->setSchema($QEsquemaMatriz);
$oAlterSchema->setSchemaDel($QEsquemaDel);

// Insertar los valores del esquemaDel en las tablas del esquemaMatriz
$aInserts = [];

$tabla = 'a_actividades_dl';
// quito los campos: tarifa y tipo_horario(posible discordancia), dl_org (para que se llenen con el default de la dl destino).
$campos = 'id_activ, id_tipo_activ, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, id_repeticion, publicado, id_tabla, plazas';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'da_ingresos_dl'; // importa el orden: después de a_actividades_dl(id_activ)
$campos = 'id_activ, ingresos, num_asistentes, ingresos_previstos, num_asistentes_previstos, observ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'a_importadas';
$campos = 'id_activ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

$tabla = 'u_cdc_dl';
// quito los campos dl y region para que se llenen con el default de la dl destino.
$campos = 'tipo_ubi, id_ubi, nombre_ubi, pais, status, f_status, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'u_dir_cdc_dl';
$campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'u_cross_cdc_dl_dir'; // importa el orden: después de u_dir_cdc_dl(id_direccion) y u_cdc_dl(id_ubi)
$campos = 'id_ubi, id_direccion, propietario, principal';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'du_gastos_dl'; // importa el orden: después de u_cdc_dl(id_ubi)
$campos = 'id_ubi, f_gasto, tipo, cantidad';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'du_grupos_dl'; // importa el orden: después de u_cdc_dl(id_ubi)
$campos = 'id_ubi_padre, id_ubi_hijo';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'du_periodos';
$campos = 'id_ubi, f_ini, f_fin, sfsv';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'd_teleco_cdc_dl';
$campos = 'id_ubi, tipo_teleco, desc_teleco, num_teleco, observ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'da_ctr_encargados';
$campos = 'id_activ, id_ubi, num_orden, encargo';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

$oAlterSchema->setInserts($aInserts);

// sv
/* ESTO ES PARA ABSORCIONES
 *
 * d_matriculas_activ_dl >>> Se deja el acta actual: posibles problemas en los ca en ejecución => borrar y nuevo.
 * d_asignaturas_activ_dl >>> Se sobreescribe si el profesor de la dl_matriz es preceptor, y el de la dl_del NO.
 * d_cargos_activ_dl  >>> los duplicados se borran
 */
// datos
// REGEXP_REPLACE(source, pattern, replacement_string,[, flags])

$config = $oConfigDB->getEsquema('publicv'); //de la database sv

$oConexion = new core\DBConnection($config);
$oDevelPC = $oConexion->getPDO();

$oAlterSchema = new DBAlterSchema();
$oAlterSchema->setDbConexion($oDevelPC);

$oAlterSchema->setSchema($esquemaMatrizv);
$oAlterSchema->setSchemaDel($esquemaDelv);

// Insertar los valores del esquemaDel en las tablas del esquemaMatriz
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
$campos = 'tabla, id_pau, id_tipo_dossier, f_ini, f_camb_dossier, status_dossier, f_status';
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
$campos = 'id_ubi, tipo_teleco, desc_teleco, num_teleco, observ';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'd_teleco_personas_dl';
$campos = 'id_nom, tipo_teleco, num_teleco, observ, desc_teleco';
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
// quito el campo dl para que se llene con el default de la dl destino.
$campos = 'id_nom, id_cr, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, lengua, situacion, f_situacion, apel_fam, inc, f_inc, stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'p_de_paso_out';
// quito el campo dl para que se llene con el default de la dl destino.
$campos = 'id_nom, id_cr, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, lengua, situacion, f_situacion, apel_fam, inc, f_inc, stgr, edad, profesion, eap, observ, profesor_stgr, lugar_nacimiento';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'p_numerarios';
$campos = 'id_nom, id_cr, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, lengua, situacion, f_situacion, apel_fam, inc, f_inc, stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'p_supernumerarios';
$campos = 'id_nom, id_cr, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, lengua, situacion, f_situacion, apel_fam, inc, f_inc, stgr, profesion, eap, observ, id_ctr, lugar_nacimiento, ce, ce_ini, ce_fin, ce_lugar';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'p_sssc';
$campos = 'id_nom, id_cr, id_tabla, sacd, trato, nom, nx1, apellido1, nx2, apellido2, f_nacimiento, lengua, situacion, f_situacion, apel_fam, inc, f_inc, stgr, profesion, eap, observ, id_ctr, lugar_nacimiento';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'u_centros_dl';
// quito el campo dl, region para que se llene con el default de la dl destino.
// quite el id_zona para dejarlo null.
$campos = 'tipo_ubi, id_ubi, nombre_ubi, pais, status, f_status, sv, sf, tipo_ctr, tipo_labor, cdc, id_ctr_padre, n_buzon, num_pi, num_cartas, observ, num_habit_indiv, plazas, sede, num_cartas_mensuales';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'u_dir_ctr_dl';
$campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'u_cross_ctr_dl_dir'; // importa orden, depende de: u_dir_ctr_dl(id_direccion) y u_centros_dl(id_ubi)
$campos = 'id_ubi, id_direccion, propietario, principal';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

$oAlterSchema->setInserts($aInserts);

$region_old = strtok($QEsquemaDel, '-');
$dl_old = strtok('-');

$gesDelegaciones = new GestorDelegacion();
$cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_old, 'region' => $region_old]);
$id_dl_old = $cDelegaciones[0]->getId_dl();
$cDelegaciones = $gesDelegaciones->getDelegaciones(['dl' => $dl_new, 'region' => $region_new]);
$id_dl_new = $cDelegaciones[0]->getId_dl();

$aDatos = [
    ['tabla' => 'da_plazas_dl', 'campo' => 'id_dl', 'old' => "$id_dl_old", 'new' => "$id_dl_new"],
];

$oAlterSchema->updateDatos($aDatos);

$aDatos = [
    ['tabla' => 'da_plazas_dl', 'campo' => 'dl_tabla', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
];

$oAlterSchema->updateDatosRegexp($aDatos);

// Todos los esquemas:
$aDatos = [
    ['tabla' => 'global.d_traslados', 'campo' => 'ctr_origen', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
    ['tabla' => 'global.d_traslados', 'campo' => 'ctr_destino', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
];
$oAlterSchema->updateDatosRegexpTodos($aDatos);

// da_plazas_dl  cedidas {"dlb": 2, "dlp": 3, "dlv": 1, "dlmE": 1, "dlmO": 1, "dlst": 1}
$oAlterSchema->updateCedidasAll($dl_old, $dl_new);

// sv-e 
$config = $oConfigDB->getEsquema('publicv-e'); //de la database sv

$oConexion = new core\DBConnection($config);
$oDevelPC = $oConexion->getPDO();

$oAlterSchema = new DBAlterSchema();
$oAlterSchema->setDbConexion($oDevelPC);

$oAlterSchema->setSchema($esquemaMatrizv);
$oAlterSchema->setSchemaDel($esquemaDelv);

// Antes de insertar las asistencias, conviene cambiar las asistencias_out a asistencias_dl para el caso de
// asistencias a actividadesque organiza la dl_new.
$oAlterSchema->asistentesOut2Dl($dl_new);

// borrar actividades importadas en dl matriz de dl_old
// pasar a asistentes_dl las asistencias_out de la dl matriz para las actividades de dl_old
// hacerlo antes de incorporar las asistencias de la nueva dl
$oAlterSchema->asistentesOut2DlPropia();

// Insertar los valores del esquemaDel en las tablas del esquemaMatriz
$aInserts = [];

$tabla = 'd_asistentes_dl';
$campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
$tabla = 'd_asistentes_out';
$campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
$aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

$oAlterSchema->setInserts($aInserts);

// cambiar los propietarios de la plaza
$oAlterSchema->updatePropietarioAll($dl_old, $dl_new);
// Todos los esquemas:
$aDatos = [
    ['tabla' => 'global.d_asistentes_dl', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
    ['tabla' => 'publicv.d_asistentes_de_paso', 'campo' => 'dl_responsable', 'pattern' => "\m$dl_old\M", 'replacement' => "$dl_new"],
];
$oAlterSchema->updateDatosRegexpTodos($aDatos);

// Comprobar que toas las asistencias out, tiene su actividad como importada.
$oAlterSchema->comprobarImportadas();

// funcion especial para intentar añadir los repetidos, sumando un cargo (sacd2).
$oAlterSchema->insertarCargos();

// De las tablas de encargos y zonas, no hago nada....

// Poner la dl en incativo:
$oDelegacion = new Delegacion($id_dl_old);
$oDelegacion->setStatus(FALSE);
$oDelegacion->DBGuardar();

// cambiar nombre del esquema (para no borrar):
$oDBRol = new core\DBRol();
$esquema_zz = 'zz' . $QEsquemaDel;
$esquemav_zz = 'zz' . $esquemaDelv;

// comun
$configComunP = $oConfigDB->getEsquema('public');
$oConexion = new core\DBConnection($configComunP);
$oConComun = $oConexion->getPDO();
$oDBRol->setDbConexion($oConComun);
$oDBRol->setUser($esquema_zz);
$oDBRol->renombrarSchema($QEsquemaDel); // Cambia el nombre del esquema
// Quitar las herencias:
$oAlterSchema->quitarHerencias($oConComun, $esquema_zz);

// sv
$configSv = $oConfigDB->getEsquema('publicv');
$oConexion = new core\DBConnection($configSv);
$oConSv = $oConexion->getPDO();
$oDBRol->setDbConexion($oConSv);
$oDBRol->setUser($esquemav_zz);
$oDBRol->renombrarSchema($esquemaDelv); // Cambia el nombre del esquema
// Quitar las herencias:
$oAlterSchema->quitarHerencias($oConSv, $esquemav_zz);

// sv-e
$configSve = $oConfigDB->getEsquema('publicv-e');
$oConexion = new core\DBConnection($configSve);
$oConSve = $oConexion->getPDO();
$oDBRol->setDbConexion($oConSve);
$oDBRol->setUser($esquemav_zz);
$oDBRol->renombrarSchema($esquemaDelv); // Cambia el nombre del esquema
// Quitar las herencias:
$oAlterSchema->quitarHerencias($oConSve, $esquemav_zz);


echo "<br>";
echo sprintf(_("se ha pasado la %s a %s"), $dl_old, $dl_new);
echo "<br>";
// Borra o cambiar de nombre el esquema viejo.
echo "Hay que borrar el esquema viejo. Se le ha cambiado el nombre";

