<?php

/* En el caso de usarse desde la lienea de comandos (cli), se le pasan parametros ($argv).
*  No se le puede pasar id de la session, porque sólo puede haber un proceso con un session_id.
*  Debe crearse una nueva session. Hay que pasarle un usuario y un password.
*  Desde ext_a_cambios.class, se llama a esta página para que funcione en background:
*	exec('nohup /usr/bin/php /var/www/dl/sistema/avisos_generar_tabla.php $username $password $dirweb $doc_root $ubicacion $esquema_web > /tmp/avisos.out 2> /tmp/avisos.err < /dev/null &');
*
* Inicialmente se ejecutaba manualmente desde menú y no habia problema.
* Al dispararlo cada vez que se ejecuta un cambio, pasa que pueden ejecutarse varios procesos en paralelo.
* Como lo primero que hace es coger los cambios que no se han anotado, puede que cuando le toque escribirlo ya lo haya hecho
* otro proceso antes.
* Para evitarlo escribo en un archivo ($pid) que estoy trabajando, y hasta que no acabe no empieza el siguiente proceso. 
* Esto tampoco funciona, porque en el tiempo de espera para saber si ya ha acabado el primer proceso, se puede colar algun
* otro proceso, saltándose el orden.
* Realmente no debería importar, excepto en el caso de asistencias en las que no quiero que se avise de la primera y 
* si cambia el orden, la primera puede ser la segunda...
*
* Finalmente lo que se hace es lanzar el proceso, al teminar vuelve iniciarse hasta que no haya ningun cambio que analizar.
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
$private = 'sf'; para el caso del servido exterior en dlb. puerto distinto.
*/

if(!empty($argv[1])) {
	$_POST['username'] = $argv[1];
	$_POST['password'] = $argv[2];
	$_SERVER['DIRWEB'] = $argv[3];
	$_SERVER['DOCUMENT_ROOT'] = $argv[4];
	putenv("UBICACION=$argv[5]");
	putenv("ESQUEMA=$argv[6]");
	putenv("PRIVATE=$argv[7]");
	$_SERVER['PRIVATE'] = $argv[7];
	
	$username = $argv[1];
	$esquema = $argv[6];
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$dir_web = $_SERVER['DIRWEB'];
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\Actividad;
use actividades\model\entity\GestorImportada;
use actividades\model\entity\TipoDeActividad;
use asistentes\model\entity\GestorAsistenteDl;
use cambios\model\entity\CambioAnotado;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\GestorCambio;
use cambios\model\entity\GestorCambioAnotado;
use cambios\model\entity\GestorCambioUsuario;
use cambios\model\entity\GestorCambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioPropiedadPref;
use core\ConfigGlobal;
use function core\is_true;
use procesos\model\entity\GestorActividadProcesoTarea;
use procesos\model\entity\GestorTareaProceso;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\DateTimeLocal;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

if(empty($argv[1])) { // Si lo hago desde el menu ()
    $username = ConfigGlobal::mi_usuario();
    $esquema = ConfigGlobal::mi_region_dl();
}


function crear_pid($username,$esquema) {
    if(!empty($username)) {
        // Si he lanzado el proceso automáticamente, escribo el id del proceso.
        // si ya existe un proceso en marcha, salgo del proceso.
        $filename = ConfigGlobal::$directorio."/log/avisos.$esquema.pid";
        
        if (file_exists($filename)) {
            $fileContent = file_get_contents($filename);
            if (!empty($fileContent)) {
                // Comprobar que no sea por que el anterior ha dadao un error y 
                // no se ha borrado. Miaramos que sea de hace más de 15 min.
                $delta = 15;
                preg_match('@(\d+/\d+/\d+ \d+:\d+:\d+) -- .*@', $fileContent, $matches);
                $f_iso = $matches[1];

                $oDiaFichero = new DateTimeLocal($f_iso);
                $oAhora = new DateTimeLocal('now');

                $interval = $oDiaFichero->diff($oAhora);
                $a = $interval->format('%i');

                if ($a > $delta) {
                    $ahora=date("Y/m/d H:i:s");
                    echo "$ahora ";
                    echo sprintf(_("El fichero %s no està vacio."),$filename);
                    echo " ";
                    echo _("Posiblemente la anterior operación finalizó con error");
                } else {
                    exit;
                }
            }
        }
        $ahora=date("Y/m/d H:i:s");
        $mensaje = "$ahora -- $username \n";
        file_put_contents($filename, $mensaje, LOCK_EX);
    }
}

function borrar_pid($username,$esquema) {
    // al finalizar borro el pid
    if(!empty($username)) {
        // Si he lanzado el proceso automáticamente.
        // Borro el pid, para que empieze el siguiente proceso.
        // Hay que asegurarse que se han acabado de escribir todos los anotados, para que no los vuelva a escribir.
        // Por esto espero 7 segundos (con 3 no basta...)
        $filename = ConfigGlobal::$directorio."/log/avisos.$esquema.pid";
        
        if (file_exists($filename)) {
            $mensaje = "";
            file_put_contents($filename, $mensaje, LOCK_EX);
        }
    }
}

function fn_apuntar($id_schema_cmb,$id_item_cmb,$id_usuario,$aviso_tipo,$aviso_donde) {
    $sfsv = ConfigGlobal::mi_sfsv();
	// Asegurar que no existe:
	$aWhere = [];
	$aWhere['id_schema_cambio'] = $id_schema_cmb;
	$aWhere['id_item_cambio'] = $id_item_cmb;
	$aWhere['sfsv'] = $sfsv;
	$aWhere['id_usuario'] = $id_usuario;
	$aWhere['aviso_tipo'] = $aviso_tipo;
    $oGesCambiosUsuario = new GestorCambioUsuario();
    $cCambioUsuario = $oGesCambiosUsuario->getCambiosUsuario($aWhere);
    // ya existe
    if (count($cCambioUsuario) > 0) {
		echo "<br>";
		echo _("apuntar cambio usuario");
		echo " ".ConfigGlobal::$web_server.'-->'.date('Y/m/d') . " " . _("Ya existe").": ";
		echo " $id_schema_cmb,$id_item_cmb,$id_usuario,$aviso_tipo\r";
    } else {
        $oCambioUsuario = new CambioUsuario();
        $oCambioUsuario->setId_schema_cambio($id_schema_cmb);
        $oCambioUsuario->setId_item_cambio($id_item_cmb);
        $oCambioUsuario->setId_usuario($id_usuario);
        $oCambioUsuario->setSfsv($sfsv);
        $oCambioUsuario->setAviso_tipo($aviso_tipo);
        $oCambioUsuario->setAviso_donde($aviso_donde);
        //echo "id_item_cmb: $id_item_cmb, id_usuario: $id_usuario, aviso_tipo: $aviso_tipo, aviso_donde: $aviso_donde\n";
        if ($oCambioUsuario->DBGuardar() === false) {
            echo ConfigGlobal::$web_server.'-->'.date('Y/m/d') . " " . _("Hay un error, no se ha guardado");
        }
    }
	//anotado($id_item_cmb); // En principio ya lo hace al final de todo.
}
function anotado($id_schema_cmb,$id_item_cmb) {
	if (ConfigGlobal::mi_sfsv() == 1) {
        $server = 1;
    } else {
        $server = 2;
    }
	// marcar como apuntado
	$aWhere = ['id_schema_cambio' => $id_schema_cmb,
	           'id_item_cambio' => $id_item_cmb,
	           'server' => $server,
	];
    $gesCambiosAnotados = new GestorCambioAnotado();
    $gesCambiosAnotados->setTabla($server);
	$cCambiosAnotados = $gesCambiosAnotados->getCambiosAnotados($aWhere);
	// debería ser único
	if (count($cCambiosAnotados) > 0) {
	    $oCambioAnotado = $cCambiosAnotados[0];
    	$oCambioAnotado->DBCarregar();
	} else {
	   $oCambioAnotado = new CambioAnotado();
	   $oCambioAnotado->setTabla($server);
	   $oCambioAnotado->setId_item_cambio($id_item_cmb);
	   $oCambioAnotado->setId_schema_cambio($id_schema_cmb);
	   $oCambioAnotado->setServer($server);
	}

    $oCambioAnotado->setAnotado('t');
	if ($oCambioAnotado->DBGuardar(true) === false) { //'true' para que no genere la tabla de avisos.
		echo _("Hay un error, no se ha guardado");
		echo _("anotado");
	}
}

function comparar($valor_cmb,$operador,$valor) {
	switch ($operador) {
		case '=':
			if (strpos($valor,',')) { // es una lista de valores
				$a_val = explode(',',$valor);
				$rta = false;
				foreach($a_val as $val) {
					$rta = $rta || ($valor_cmb == $val);
				}
				return $rta;
			} else {
				// ojo con los boolean.
				if (($valor_cmb == 't') || ($valor == 't') || ($valor_cmb === true) || ($valor === true)) return ((bool)$valor_cmb === (bool)$valor);
				if (($valor_cmb == 'f') || ($valor == 'f') || ($valor_cmb === false) || ($valor === false)) return ((bool)$valor_cmb === (bool)$valor);
				return ($valor_cmb == $valor);
			}
			break;
		case '>':
			return ($valor_cmb >= $valor);
			break;
		case '<':
			return ($valor_cmb <= $valor);
			break;
		case 'regexp':
			break;

	}
	
}	

function me_afecta($id_usuario,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$id_pau,$sObjeto) {
	//echo "usuario: $id_usuario, camp: $propiedad, id_activ: $id_activ, id_pau: $id_pau<br>\n";
	// Si el usuario es una casa o un sacd, sólo ve los cambios que le afectan:
	$oMiUsuario = new Usuario($id_usuario);

	$id_pau = '';
    //casa
	if ($oMiUsuario->isRolePau(Role::PAU_CDC)) {
		$id_pau = $oMiUsuario->getId_pau(); // puede ser una lista separada por comas.
        if (!empty($id_pau)) { //casa o un listado de ubis en la preferencia del aviso.
            $a_id_pau = explode(',',$id_pau);

            $oActividad = new Actividad($id_activ);
            $id_ubi = $oActividad->getId_ubi(); // id ubi actual.

            // si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
            if ($propiedad == 'id_ubi') {
                if (in_array($valor_old_cmb,$a_id_pau) || in_array($id_ubi,$a_id_pau)) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                // si cambia qualquier otra cosa en mi id_ubi.
                if (in_array($id_ubi,$a_id_pau)) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }
	}
	// si soy un sacd.
	if ($oMiUsuario->isRolePau(Role::PAU_SACD)) {
		$id_nom=$oMiUsuario->getId_pau();
		if (soy_encargado($id_nom,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$sObjeto)) {
			return TRUE;
		} else {
			return FALSE;
		}

	}
	return TRUE;
}
function soy_encargado($id_nom,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$sObjeto) {
	// sacd encargados de esta actividad
	$GesCargos = new GestorActividadCargo();
	$a_Sacds = $GesCargos->getActividadIdSacds($id_activ);
	// si soy jefe de zona me afectan todos los sacd de la zona.
	$GesZonas = new GestorZona();
	$cZonas = $GesZonas->getZonas(array('id_nom'=>$id_nom));
	if (is_array($cZonas) && count($cZonas)>0) {
		// sacd de mi zona
		$GesZonaSacd = new GestorZonaSacd();
		$rta = 0;
		foreach ($cZonas as $oZona) {
			$id_zona = $oZona->getId_zona();
			$cSacds = $GesZonaSacd->getSacdsZona($id_zona);
			switch ($sObjeto) {
				case 'Actividad':
					// compruebo si el sacd asiste.
				    foreach ($cSacds as $id_nom) {
				        $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
                        $GesAsistentes = new GestorAsistenteDl();
                        $a_Asistentes = $GesAsistentes->getAsistentes($aWhere);
					    if (count($a_Asistentes)>0) $rta += 1;
				    }
				break;
				case 'ActividadCargoNoSacd':
				case 'ActividadCargoSacd':
					// compruebo si el sacd tiene cargo
					$a_sacd_con_cargo = array_intersect($cSacds, $a_Sacds);
					if (count($a_sacd_con_cargo)>0) $rta += 1;
					// si lo que cambia es el id_nom, compruebo que el valor old o new sean de algun sacd de la zona.
					if ($propiedad == 'id_nom') {
						if (in_array($valor_old_cmb,$cSacds) || in_array($valor_new_cmb,$cSacds)) {
							$rta += 1;
						}
					}
				break;
				case 'Asistente':
					// si lo que cambia es el id_nom, compruebo que el valor old o new sean de algun sacd de la zona.
					if ($propiedad == 'id_nom') {
						if (in_array($valor_old_cmb,$cSacds) || in_array($valor_new_cmb,$cSacds)) {
							$rta += 1;
						}
					}
				break;
			}
		}
		if ($rta) {
			return true;
		} else {
			return false;
		}
	} else { // no soy jefe zona.
		if (in_array($id_nom,$a_Sacds)) {
			return true;
		} else {
			return false;
		}
	}
}
// FIN de  Cabecera global de URL de controlador ********************************

// Para asegurar que no lo ejecuto desde una dl que no lo tenga instalado
if (!ConfigGlobal::is_app_installed('cambios')) {
   die(); 
}
// Mirar si hay otro proceso en marcha:
crear_pid($username,$esquema);

$GesCambios = new GestorCambio();
// Borrar los cambios y sus anotaciones de hace más de un año:
$GesCambios->borrarCambios('P1Y');

// seleccionar cambios no anotados:
$cNuevosCambios = $GesCambios->getCambiosNuevos();
$num_cambios = count($cNuevosCambios);
// Repito el proceso por si se han apuntado cambios mientras estaba realizando el proceso.
while ($num_cambios) {
	//print_r($cNuevosCambios);
	foreach ($cNuevosCambios as $oCambio) {
		$id_item_cmb = $oCambio->getId_item_cambio();
		$id_schema_cmb = $oCambio->getId_schema();
		$sObjeto = $oCambio->getObjeto();
		$dl_org = $oCambio->getDl_org();
		$id_tipo_activ = $oCambio->getId_tipo_activ();
		$id_fase_cmb_sv = $oCambio->getId_fase_sv();
		$id_fase_cmb_sf = $oCambio->getId_fase_sf();
		$id_status_cmb = $oCambio->getId_status();
		$propiedad_cmb = $oCambio->getPropiedad();
		$valor_old_cmb = $oCambio->getValor_old();
		$valor_new_cmb = $oCambio->getValor_new();
		$id_activ = $oCambio->getId_activ();
		
		// Para las actividades, en el cambio se anota: 'ActividadDl' 'ActividadEx'
		// pero en las preferencias, solo 'Actividad'.
		if(strpos($sObjeto, 'Actividad') !== false){
		    $sObjeto = 'Actividad';
		}
		
		// Para los asistentes, en el cambio se anota: 'Asistente' 'AsistenteDl' 'AsistenteEx' 'AsistenteIn' 'AsistenteOut'
		// pero en las preferencias, solo 'Asistente'.
		if(strpos($sObjeto, 'Asistente') !== false){
		    $sObjeto = 'Asistente';
		}
		
		if (ConfigGlobal::mi_sfsv() == 1) {
            $id_fase_cmb = $id_fase_cmb_sv;		    
		} else {
            $id_fase_cmb = $id_fase_cmb_sf;		    
		}
		// para dl y dlf:
		$dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
		$dl_propia = (ConfigGlobal::mi_dele() == $dl_org_no_f)? 't' : 'f';
        // Si es de otra dl, compruebo que sea una actividad importada, sino no tiene sentido avisar.
        if ($dl_propia == 'f') {
            $GesImportada = new GestorImportada();
            $cImportadas = $GesImportada->getImportadas(array('id_activ'=>$id_activ));
            if (empty($cImportadas)) { 
        		// marco el cambio como anotado.
		        anotado($id_schema_cmb,$id_item_cmb);
                continue;
            }
        }
		
		$aWhere = [];
		$aOperador = [];
		$aWhere['objeto'] = $sObjeto;
		$aWhere['dl_org'] = ($dl_propia == 'f')? 'x' : $dl_org;
		$aWhere['id_tipo_activ_txt'] = $id_tipo_activ;
		$aOperador['id_tipo_activ_txt'] = '~INV';
		$aWhere['_ordre'] = 'aviso_tipo,id_usuario,id_tipo_activ_txt DESC'; // intento que el primero sea el más definido.
		$GesCambioUsuarioObjeto = new GestorCambioUsuarioObjetoPref();
		$cCambiosUsuarioObjeto = $GesCambioUsuarioObjeto->getCambioUsuarioObjetosPrefs($aWhere,$aOperador);
		if (($cCambiosUsuarioObjeto === false) OR empty($cCambiosUsuarioObjeto)) { anotado($id_schema_cmb,$id_item_cmb); continue; }
		$id_usuario_anterior='';
		$aviso_tipo_anterior='';
		$apuntar = false;
		foreach ($cCambiosUsuarioObjeto as $oCambioUsuarioObjetoPref) {
			$id_item_usuario_objeto = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
			$id_usuario = $oCambioUsuarioObjetoPref->getId_usuario();
			$aviso_tipo = $oCambioUsuarioObjetoPref->getAviso_tipo();
			// con que cumpla una condicion para un mismo usuario basta, salto al siguiente cambio. 
			if ($apuntar && ($aviso_tipo == $aviso_tipo_anterior) && ($id_usuario == $id_usuario_anterior)) {
				$apuntar = false;
				continue;
			} else {
				$aviso_tipo_anterior = $aviso_tipo;
				$id_usuario_anterior = $id_usuario;
			}
			$aviso_donde = $oCambioUsuarioObjetoPref->getAviso_donde();
			$id_pau = $oCambioUsuarioObjetoPref->getId_pau();
			$json_fases = $oCambioUsuarioObjetoPref->getJson_fases();
			$oFases = json_decode($json_fases);
			if (empty($oFases)) {
			    $oFases = new stdClass;
			}
			
			$fase_correcta = 0;
			/////////////////// COMPARAR STATUS //////////////////////////////////////////
			// Si el id_fase es NULL, hay que mirar el id_status
			// Si el id_status es 1,2,3,4 corresponde al status de la actividad,
			//   porque no tiene instalado el módulo de procesos.
			if (empty($id_fase_cmb)) {
			    // Si yo SI tengo procesos:
			    if(ConfigGlobal::is_app_installed('procesos')) {
			        // Busco el status de la actividad qe corresponde a la dase actual
			        // miro la fase actual de la actividad
			        $gesActivProcesoTarea = new GestorActividadProcesoTarea();
			        $id_faseActual = $gesActivProcesoTarea->getFaseActual($id_activ);
                    // busco el tipo de proceso			        
			        $oTipo = new TipoDeActividad(array('id_tipo_activ'=>$id_tipo_activ));
			        $id_tipo_proceso=$oTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
			        // status correspondiente a la fase actual de la actividad
			        $aWhereTP = ['id_tipo_proceso' => $id_tipo_proceso, 'id_fase' => $id_faseActual];
			        $gesTareaProceso = new GestorTareaProceso();
			        $cTareaProceso = $gesTareaProceso->getTareasProceso($aWhereTP);
			        $staus_de_fase = $cTareaProceso[0]->getStatus();
			        
                    if ($id_status_cmb == $staus_de_fase) {
                            $fase_correcta = 1;
                    }
			    } else{
    			    // Si yo no tengo procesos:
			        foreach ($oFases as $id_fase => $ok) {
			            if ($id_status_cmb == $id_fase) {
                            $fase_correcta = 1;
			            }
			        }
			        /*
                    if ($id_fase_ini <= $id_status_cmb && $id_fase_fin >= $id_status_cmb) {
                        $fase_correcta = 1;
                    }
                    */
			    }
			} else {
			    /////////////////// COMPARAR FASES //////////////////////////////////////////
			
			    // Si tengo instalado el modulo de procesos:
			    if(ConfigGlobal::is_app_installed('procesos')) {
			        foreach ($oFases as $id_fase => $ok) {
			            if ($id_fase_cmb == $id_fase) {
                            $fase_correcta = 1;
			            }
			        }
			    } else {
			        //Yo no tengo instalado el modulo procesos, pero la dl que ha hecho el cambio si.
			        // miro que esté en el status.
            		$oActividad = new Actividad($id_activ);
            		$status = $oActividad->getStatus();
			        foreach ($oFases as $id_fase => $ok) {
			            if ($status == $id_fase) {
                            $fase_correcta = 1;
			            }
			        }
			    }
			}
			
			if ($fase_correcta === 1) {
                //mirar el valor de la propiedad
                $GesCambiosUsuarioPropiedadPref = new GestorCambioUsuarioPropiedadPref();
                $cListaPropiedades = $GesCambiosUsuarioPropiedadPref->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto'=>$id_item_usuario_objeto));
                foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
                    $propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
                    $operador = $oCambioUsuarioPropiedadPref->getOperador();
                    $valor = $oCambioUsuarioPropiedadPref->getValor();
                    $valor_old = $oCambioUsuarioPropiedadPref->getValor_old();
                    $valor_new = $oCambioUsuarioPropiedadPref->getValor_new();
                    
                    if ($propiedad_cmb == $propiedad) {
                        // En el caso de casas o sacd, comprobar que me afecta.
                        if (!me_afecta($id_usuario,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$id_pau,$sObjeto)) {
                            $apuntar = false;
                            continue; 
                        }
                        if (!empty($valor)) {
                            $operador = empty($operador)? '=' : $operador;
                            if ( is_true($valor_old) ) {
                                $apuntar = comparar($valor_old_cmb,$operador,$valor); 
                            }
                            if ($apuntar === false && is_true($valor_new) ) {
                                $apuntar = comparar($valor_new_cmb,$operador,$valor); 
                            }
                        } else {
                            $apuntar = true;
                        }
                    }
                }
            } else {
                // Por lo menos en el caso de id_status (empty id_fase_cmb) no es un error, simplemente 
                // significa que el estatus en que se ha cambiado la actividad no pertenece al rango
                // para el que hay que generar el aviso.
                if (!empty($id_fase_cmb)) {
                    /*
                    echo "<br>";
                    echo _("ERROR: la fase en que se cambió la actividad no está en el intervalo 
                            de fases del proceso donde hay que avisar.");
                    echo "<br>";
                    echo " fase correcta: $fase_correcta<br>";
                    echo " id_activ: $id_activ<br>";
                    echo " id_usuario: $id_usuario<br>";
                    echo " id_fase_ini: $id_fase_ini<br>";
                    echo " id_fase_fin: $id_fase_fin<br>";
                    echo " id_fase_cmb: $id_fase_cmb<br>";
                    echo " fases:";
                    print_r($aFases);
                    */
                }
			}
			if ($apuntar) {
				fn_apuntar($id_schema_cmb,$id_item_cmb,$id_usuario,$aviso_tipo,$aviso_donde);
			}
			$apuntar = false;
		}
		// Si he mirado todas las pref de usuarios, marco el cambio como anotado, aunque no coincida con ninguno.
		anotado($id_schema_cmb,$id_item_cmb);
	}
	// Si algo falla, el $num_cambios es el mismo y se genera un bucle infinito.
	$num_cambios_old = $num_cambios;
	$cNuevosCambios = $GesCambios->getCambiosNuevos();
	$num_cambios = count($cNuevosCambios);
	if ($num_cambios === $num_cambios_old) {
	   // igualmente borro el pid
        borrar_pid($username,$esquema);
	    exit (_("Algo falla")); 
	}
}

// acabar el proceso:
borrar_pid($username,$esquema);