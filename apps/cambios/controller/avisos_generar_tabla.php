<?php

/* En el caso de usarse desde la linea de comandos (cli), se le pasan parámetros ($argv).
*  No se le puede pasar id de la session, porque sólo puede haber un proceso con un session_id.
*  Debe crearse una nueva session. Hay que pasarle un usuario y un password.
*  Desde la clase Cambio y CambioDl, se llama a esta página para que funcione en background:
*	exec('nohup /usr/bin/php /var/www/dl/sistema/avisos_generar_tabla.php $username $password $dirweb $doc_root $ubicacion $esquema_web > /tmp/avisos.out 2> /tmp/avisos.err < /dev/null &');
*
* Inicialmente se ejecutaba manualmente desde menú y no había problema.
* Al dispararlo cada vez que se ejecuta un cambio, pasa que pueden ejecutarse varios procesos en paralelo.
* Como lo primero que hace es coger los cambios que no se han anotado, puede que cuando le toque escribirlo ya lo haya hecho
* otro proceso antes.
* Para evitarlo escribo en un archivo ($pid) que estoy trabajando, y hasta que no acabe no empieza el siguiente proceso. 
* Esto tampoco funciona, porque en el tiempo de espera para saber si ya ha acabado el primer proceso, se puede colar algún
* otro proceso, saltándose el orden.
* Realmente no debería importar, excepto en el caso de asistencias en las que no quiero que se avise de la primera y 
* si cambia el orden, la primera puede ser la segunda...
*
* Finalmente lo que se hace es lanzar el proceso, al terminar vuelve iniciarse hasta que no haya ningún cambio que analizar.
* Al principio se anota el pid, y no se borra hasta el final. Si se dispara un proceso en paralelo, al ver que existe el pid,
* se para y no hace nada. En caso contrario se inicia.
*
* OJO: poner en  '/etc/php/7.2/cli/php.ini'
*       include_path = ".:/usr/share/php:/home/dani/orbix_local/orbix"
*/

/* Hay que pasarle los argumentos que no tienen si se le llama por command line:
$username;
$password;
$dir_web = orbix | pruebas;
document_root = /home/dani/orbix_local
$ubicacion = 'sv';
$esquema_web = 'H-dlbv';
$private = 'sf'; para el caso del servidor exterior en dlb. puerto distinto.
$DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)
*/

if (!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $_SERVER['DIRWEB'] = $argv[3];
    $_SERVER['DOCUMENT_ROOT'] = $argv[4];
    putenv("UBICACION=$argv[5]");
    putenv("ESQUEMA=$argv[6]");
    putenv("PRIVATE=$argv[7]");
    putenv("DB_SERVER=$argv[8]");

    $username = $argv[1];
    $esquema = $argv[6];
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$dir_web = $_SERVER['DIRWEB'];
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use cambios\model\Avisos;
use core\ConfigGlobal;
use permisos\model\PermisosActividades;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

if (empty($argv[1])) { // Si lo hago desde el menu ()
    $username = ConfigGlobal::mi_usuario();
    $esquema = ConfigGlobal::mi_region_dl();
}

// FIN de  Cabecera global de URL de controlador ********************************

// Para asegurar que no lo ejecuto desde una dl que no lo tenga instalado
if (!ConfigGlobal::is_app_installed('cambios')) {
    die();
}

$oAvisos = new Avisos();

// Mirar si hay otro proceso en marcha:
$oAvisos->crear_pid($username, $esquema);

$CambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
// Borrar los cambios y sus anotaciones de hace más de un año:
$CambioRepository->borrarCambios('P1Y');

// para mirar los permisos
$aObjPerm = [
    'Actividad' => 'datos',
    'ActividadProcesoTarea' => 'datos',
    'ActividadCargoSacd' => 'sacd',
    'ActividadCargoNoSacd' => 'cargos',
    'Asistente' => 'asistentes',
    'CentroEncargado' => 'ctr',
];

// seleccionar cambios no anotados:
$cNuevosCambios = $CambioRepository->getCambiosNuevos();
$num_cambios = count($cNuevosCambios);
$err_fila = '';
// Repito el proceso por si se han apuntado cambios mientras estaba realizando el proceso.
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
$ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
$TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
$PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
$TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
$CambioUsuarioObjetoPrefRepository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
$CambiosUsuarioPropiedadPrefRepository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
while ($num_cambios) {
    $num_cambios_inicial = $num_cambios;
    foreach ($cNuevosCambios as $oCambio) {
        $afecta = '';
        $id_item_cmb = $oCambio->getId_item_cambio();
        $id_schema_cmb = $oCambio->getId_schema();
        $sObjeto = $oCambio->getObjeto();
        $dl_org = $oCambio->getDl_org();
        $id_tipo_activ = $oCambio->getId_tipo_activ();
        $aFases_cmb_sv = $oCambio->getJson_fases_sv(TRUE);
        $aFases_cmb_sf = $oCambio->getJson_fases_sf(TRUE);
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
        if (strpos($sObjeto, 'Asistente') !== false) {
            $sObjeto = 'Asistente';
            // Para el caso de los sacd, el permiso es 'asistentessacd'
            if ($propiedad_cmb === 'id_nom') {
                $id_nom = empty($valor_new_cmb) ? $valor_old_cmb : $valor_new_cmb;
                $oPersonaSacd = $PersonaSacdRepository->findById($id_nom);
                if ($oPersonaSacd->isSacd()) {
                    $afecta = 'asistentesSacd';
                }
            }
        }

        $afecta = empty($afecta) ? $aObjPerm[$sObjeto] : $afecta;

        if (ConfigGlobal::mi_sfsv() === 1) {
            $aFases_cmb = $aFases_cmb_sv;
        } else {
            $aFases_cmb = $aFases_cmb_sf;
        }

        $oAvisos->setId_schema_cmb($id_schema_cmb);
        $oAvisos->setId_item_cmb($id_item_cmb);
        $oAvisos->setObjeto($sObjeto);
        $oAvisos->setFasesCmb($aFases_cmb);

        // para dl y dlf:
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
        $dl_propia = (ConfigGlobal::mi_dele() === $dl_org_no_f);
        // Si es de otra dl, compruebo que sea una actividad importada, sino no tiene sentido avisar.
        if (!is_true($dl_propia)) {
            $cImportadas = $ImportadaRepository->findById($id_activ);
            if (empty($cImportadas)) {
                // marco el cambio como anotado.
                $oAvisos->anotado();
                continue;
            }
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['objeto'] = $sObjeto;
        $aWhere['dl_org'] = (!is_true($dl_propia)) ? 'x' : $dl_org;
        $aWhere['id_tipo_activ_txt'] = $id_tipo_activ;
        $aOperador['id_tipo_activ_txt'] = '~INV';
        $aWhere['_ordre'] = 'aviso_tipo,id_usuario,id_tipo_activ_txt DESC'; // intento que el primero sea el más definido.
        $cCambiosUsuarioObjeto = $CambioUsuarioObjetoPrefRepository->getCambioUsuarioObjetosPrefs($aWhere, $aOperador);
        if (($cCambiosUsuarioObjeto === false) || empty($cCambiosUsuarioObjeto)) {
            $oAvisos->anotado();
            continue;
        }
        $id_usuario_anterior = '';
        $aviso_tipo_anterior = '';
        $apuntar = false;
        foreach ($cCambiosUsuarioObjeto as $oCambioUsuarioObjetoPref) {
            $id_item_usuario_objeto = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
            $id_usuario = $oCambioUsuarioObjetoPref->getId_usuario();
            $aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
            $oAvisos->setId_usuario($id_usuario);
            // con que cumpla una condición para un mismo usuario basta, salto al siguiente cambio.
            if ($apuntar && ($aviso_tipo === $aviso_tipo_anterior) && ($id_usuario === $id_usuario_anterior)) {
                $apuntar = false;
                continue;
            } else {
                $aviso_tipo_anterior = $aviso_tipo;
                $id_usuario_anterior = $id_usuario;
            }
            $id_pau = $oCambioUsuarioObjetoPref->getCsv_id_pau();
            $id_fase_ref = $oCambioUsuarioObjetoPref->getId_fase_ref();
            $aviso_off = $oCambioUsuarioObjetoPref->getAviso_off();
            $aviso_on = $oCambioUsuarioObjetoPref->getAviso_on();
            $aviso_outdate = $oCambioUsuarioObjetoPref->getAviso_outdate();

            $fase_correcta = 0;
            /////////////////// COMPARAR DATE //////////////////////////////////////////
            if (!is_true($aviso_outdate)) {
                $oActividad = $ActividadAllRepository->findById($id_activ);
                $oF_fin = $oActividad->getF_fin();
                if ($oF_cmb > $oF_fin) {
                    continue;
                }
            }

            /////////////////// COMPARAR STATUS //////////////////////////////////////////
            // Si el id_fase es NULL, hay que mirar el id_status
            // Si el id_status es 1,2,3 corresponde al status de la actividad,
            //   porque no tiene instalado el módulo de procesos.
            if (empty($aFases_cmb)) {
                // Si yo SI tengo procesos:
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
                    if ($id_status_cmb === $status_de_fase && is_true($aviso_on)) {
                        $fase_correcta = 1;
                    }
                } else {
                    // Si yo no tengo procesos:
                    foreach ($aFases_cmb as $id_fase) {
                        if ($id_status_cmb === $id_fase) {
                            $fase_correcta = 1;
                        }
                    }
                }
            } else {
                /////////////////// COMPARAR FASES //////////////////////////////////////////
                // fase on
                if (in_array($id_fase_ref, $aFases_cmb)) {
                    // aviso_on
                    if (is_true($aviso_on)) {
                        // Tengo permiso de ver esta fase?
                        $oPermActividades = new PermisosActividades($id_usuario);
                        $oPermActividades->setActividad($id_activ);
                        $oPermActividades->setFasesCompletadas($aFases_cmb);
                        $oPermActiv = $oPermActividades->getPermisoActual($afecta);
                        if (!$oPermActiv->have_perm_activ('ocupado')) {
                            continue;
                        }

                        // Si tengo instalado el modulo de procesos:
                        if (ConfigGlobal::is_app_installed('procesos')) {
                            $fase_correcta = 1;
                        } else {
                            //Yo no tengo instalado el modulo procesos, pero la dl que ha hecho el cambio si.
                            // miro que esté en el status.
                            $oActividad = $ActividadAllRepository->findById($id_activ);
                            $status = $oActividad->getStatus();
                            foreach ($aFases_cmb as $id_fase) {
                                if ($status === $id_fase) {
                                    $fase_correcta = 1;
                                }
                            }
                        }
                    }
                } else {
                    // fase off
                    // aviso_off
                    if (is_true($aviso_off)) {
                        // Tengo permiso de ver esta fase?
                        $oPermActividades = new PermisosActividades($id_usuario);
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
                //mirar el valor de la propiedad
                $cListaPropiedades = $CambiosUsuarioPropiedadPrefRepository->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto' => $id_item_usuario_objeto));
                foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
                    $propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
                    $operador = $oCambioUsuarioPropiedadPref->getOperador();
                    $valor = $oCambioUsuarioPropiedadPref->getValor();
                    $valor_old = $oCambioUsuarioPropiedadPref->getValor_old();
                    $valor_new = $oCambioUsuarioPropiedadPref->getValor_new();

                    if ($propiedad_cmb === $propiedad) {
                        // En el caso de casas o sacd, comprobar que me afecta.
                        if (!$oAvisos->me_afecta($propiedad, $id_activ, $valor_old_cmb, $valor_new_cmb, $id_pau, $sObjeto)) {
                            $apuntar = false;
                            continue;
                        } elseif (!empty($valor)) {
                            $operador = empty($operador) ? '=' : $operador;
                            if (is_true($valor_old)) {
                                $apuntar = $oAvisos->comparar($valor_old_cmb, $operador, $valor);
                            }
                            if ($apuntar === false && is_true($valor_new)) {
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
            }
            $apuntar = false;
        }
        // Si he mirado todas las pref de usuarios, marco el cambio como anotado, aunque no coincida con ninguno.
        $oAvisos->anotado();
    }
    // Si algo falla, el $num_cambios_inicial es igual al actual y se genera un bucle infinito.
    // Si se han producido nuevos cambios durante el proceso, $numcambios no será 0 y se repite el proceso.
    $cNuevosCambios = $CambioRepository->getCambiosNuevos();
    $num_cambios = count($cNuevosCambios);
    if ($num_cambios === $num_cambios_inicial) {
        // igualmente borro el pid
        $oAvisos->borrar_pid($username, $esquema);
        exit (_("Algo falla"));
    }
}

if (!empty($err_fila)) {
    $err_tabla = _("error al apuntar cambio usuario en");
    $err_tabla .= " " . ConfigGlobal::$web_server . '-->' . date('c') . ": " . _("Ya existe") . "<br>";
    $err_tabla .= '<table><tr>';
    $err_tabla .= '<th>' . _("schema") . '</th>';
    $err_tabla .= '<th>' . _("id_item_cmb") . '</th>';
    $err_tabla .= '<th>' . _("id_usuario") . '</th>';
    $err_tabla .= '<th>' . _("aviso tipo") . '</th>';
    $err_tabla .= '</tr>';
    $err_tabla .= $err_fila;
    $err_tabla .= "</table>";

    echo $err_tabla;
}

// acabar el proceso:
$oAvisos->borrar_pid($username, $esquema);