<?php
/* En el caso de usarse desde la lienea de comandos (cli), se le pasan parametros ($argv).
*  No se le puede pasar id de la session, porque sólo puede haber un proceso con un session_id.
*  Debe crearse una nueva session. Hay que pasarle un usuario y un password.
*  Desde ext_a_cambios.class, se llama a esta página para que funcione en background:
*	exec('nohup /usr/bin/php /var/www/dl/sistema/avisos_generar_tabla.php $username $password > /tmp/avisos.out 2> /tmp/avisos.err < /dev/null &');
*
* Inicialmente se ejecutaba manualmente desde menú y no habia problema.
* Al dispararlo cada vez que se ejecuta un cambio, pasa que pueden ejecutarse varios procesos en paralelo.
* Como lo primero que hace es coger los cambios que no se han anotado, puede que cuando le toque escribirlo ya lo haya hecho otro proceso antes.
* Para evitarlo escribo en un archivo ($pid) que estoy trabajando, y hasta que no acabe no empieza el siguiente proceso. 
* Esto tampoco funciona, porque en el tiempo de espera para saber si ya ha acabado el primer proceso, se puede colar algun otro proceso, saltándose el orden.
* Realmente no debería importar, excepto en el caso de asistencias en las que no quiero que se avise de la primera y si cambia el orden, la primera puede ser la segunda...
*
* Finalmente lo que se hace es lanzar el proceso, al teminar vuelve iniciarse hasta que no haya ningun cambio que analizar. Al principio se anota el pid, y no se borra hasta el final. Si se dispara un proceso en paralelo, al ver que existe el pid, se para y no hace nada. En caso contrario se inicia.
*
*/
use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\Actividad;
use actividades\model\entity\GestorImportada;
use actividades\model\entity\GestorTipoDeActividad;
use asistentes\model\entity\GestorAsistenteDl;
use cambios\model\entity\CambioAnotado;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\GestorCambio;
use cambios\model\entity\GestorCambioAnotado;
use cambios\model\entity\GestorCambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioPropiedadPref;
use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use usuarios\model\entity\Usuario;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;

if(!empty($argv[1])) {
	$_POST['username'] = $argv[1];
	$_POST['password'] = $argv[2];
}

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************

if(!empty($argv[1])) {
	// Si he lanzado el proceso automáticamente, escribo el id del proceso.
	// si ya existe un proceso en marcha, salgo del proceso.
	$pid = ConfigGlobal::$directorio.'/log/avisos.pid';
	$file = file_get_contents($pid);
	if (!empty($file)) exit;
	$ahora=date("d/m/Y H:i:s");
	$mensaje = "$ahora -- ${_POST['username']} \n";
	file_put_contents($pid, $mensaje);
}

function fn_apuntar($id_schema_cmb,$id_item_cmb,$id_usuario,$aviso_tipo,$aviso_donde) {
	//echo "<br>$id_item_cmb,$id_usuario,$aviso_tipo;";
	$oCambioUsuario = new CambioUsuario();
	$oCambioUsuario->setId_schema_cambio($id_schema_cmb);
	$oCambioUsuario->setId_item_cambio($id_item_cmb);
	$oCambioUsuario->setId_usuario($id_usuario);
	$oCambioUsuario->setAviso_tipo($aviso_tipo);
	$oCambioUsuario->setAviso_donde($aviso_donde);
	//echo "id_item_cmb: $id_item_cmb, id_usuario: $id_usuario, aviso_tipo: $aviso_tipo, aviso_donde: $aviso_donde\n";
	if ($oCambioUsuario->DBGuardar() === false) {
		echo ConfigGlobal::$web_server.'-->'.date('Y/m/d') . " " . _("Hay un error, no se ha guardado");
		echo " $id_schema_cmb,$id_item_cmb,$id_usuario,$aviso_tipo\r";
	}
	//anotado($id_item_cmb); // En principio ya lo hace al final de todo.
}
function anotado($id_schema_cmb,$id_item_cmb) {
	// marcar como apuntado
	$aWhere = ['id_schema_cambio' => $id_schema_cmb, 'id_item_cambio' => $id_item_cmb];
    $gesCambiosAnotados = new GestorCambioAnotado();
	$cCambiosAnotados = $gesCambiosAnotados->getCambiosAnotados($aWhere);
	// debería ser único
	if (count($cCambiosAnotados) > 0) {
	    $oCambioAnotado = $cCambiosAnotados[0];
    	$oCambioAnotado->DBCarregar();
	} else {
	   $oCambioAnotado = new CambioAnotado();
	   $oCambioAnotado->setId_item_cambio($id_item_cmb);
	   $oCambioAnotado->setId_schema_cambio($id_schema_cmb);
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
	$miRole=$oMiUsuario->getId_role();

	if ($miRole == 9) { //casa
		$id_pau=$oMiUsuario->getId_pau(); // puede ser una lista separada por comas.
	}
	if (!empty($id_pau)) { //casa o un listado de ubis en la preferencia del aviso.
		$a_id_pau = explode(',',$id_pau);

		$oActividad = new Actividad($id_activ);
		$id_ubi = $oActividad->getId_ubi(); // id ubi actual.

		// si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
		if ($propiedad == 'id_ubi') {
			if (in_array($valor_old_cmb,$a_id_pau) || in_array($id_ubi,$a_id_pau)) {
				return true;
			} else {
				return false;
			}
		}
		// si cambia qualquier otra cosa en mi id_ubi.
		if (in_array($id_ubi,$a_id_pau)) {
			return true;
		} else {
			return false;
		}
	}
	// si soy un sacd.
	if ($miRole == 7) { //sacd
		$id_nom=$oMiUsuario->getId_pau();
		if (soy_encargado($id_nom,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$sObjeto)) {
			return true;
		} else {
			return false;
		}

	}
	return true;
}
function soy_encargado($id_nom,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$sObjeto) {
	// sacd encargados de esta actividad
	$GesCargos = new GestorActividadCargo();
	$a_Sacds = $GesCargos->getActividadIdSacds($id_activ);
	// si soy jefe de zona me afectan todos los sacd de la zona.
	$GesZonas = new GestorZona();
	$cZonas = $GesZonas->getZonas(array('id_nom'=>$id_nom));
	if (is_array($cZonas) && count($cZonas)>0) {
		// sacd asistentes de esta actividad
		$GesAsistentes = new GestorAsistenteDl();
		$a_Asistentes = $GesAsistentes->getListaAsistentesDeActividad($id_activ); // si estoy en el exterior sólo están los sacd.
		// sacd de mi zona
		$GesZonaSacd = new GestorZonaSacd();
		$rta = 0;
		foreach ($cZonas as $oZona) {
			$id_zona = $oZona->getId_zona();
			$cSacds = $GesZonaSacd->getSacdsZona($id_zona);
			switch ($sObjeto) {
				case 'Actividad':
				case 'ActividadDl':
				case 'ActividadEx':
					// compruebo si el sacd asiste. En caso de que no lo haya apuntado todavía.
					$a_sacd_asistente = array_intersect($cSacds, $a_Asistentes);
					if (count($a_sacd_asistente)>0) $rta += 1;
				break;
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
				case 'ActividadAsistente':
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


$GesCambios = new GestorCambio();
// Borrar los cambios y sus anotaciones de hace más de un año:
$GesCambios->borrarCambios('P1Y');

// seleccionar cambios no anotados:
$cNuevosCambios = $GesCambios->getCambiosNuevos();
$num_cambios = count($cNuevosCambios);
// Repito el proceso por si se han apuntado cambios mientras estaba realizando el proceso.
while ($num_cambios) {
	//print_r($cNuevosCambios);
	$id_tipo_activ_anterior = '';
	$dl_org_anterior = '';
	foreach ($cNuevosCambios as $oCambio) {
		$id_item_cmb = $oCambio->getId_item_cambio();
		$id_schema_cmb = $oCambio->getId_schema();
		$sObjeto = $oCambio->getObjeto();
		$dl_org = $oCambio->getDl_org();
		$id_tipo_activ = $oCambio->getId_tipo_activ();
		$id_fase_cmb = $oCambio->getId_fase();
		$propiedad_cmb = $oCambio->getPropiedad();
		$valor_old_cmb = $oCambio->getValor_old();
		$valor_new_cmb = $oCambio->getValor_new();
		$id_activ = $oCambio->getId_activ();
		
		$dl_propia = (ConfigGlobal::mi_dele() == $dl_org)? 't' : 'f';
		if (ConfigGlobal::is_app_installed('procesos')) {
            // para evitar repetir el proceso si el tipo de actividad es el mismo.
            if ($id_tipo_activ_anterior != $id_tipo_activ || $dl_org_anterior != $dl_org) {
                $id_tipo_activ_anterior = $id_tipo_activ;
                $dl_org_anterior = $dl_org;
                // buscar los procesos posibles para estos tipos de actividad
                $GesTiposActiv = new GestorTipoDeActividad();
                $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($id_tipo_activ,$dl_propia);
                $TipoDeProceso = $aTiposDeProcesos[0];
                // buscar las fases para estos procesos
                $oGesFases= new GestorActividadFase();
                $aFases = $oGesFases->getTodasActividadFases($TipoDeProceso);
            }
		} else {
		    $aFases = [1,2,3,4]; // id correspondientes al status de la actividad.
		}
        // Si es de otra dl, compruebo que sea una actividad importada, sino no tiene sentido avisar.
        if ($dl_propia == 'f') {
            $GesImportada = new GestorImportada();
            $cImportadas = $GesImportada->getImportadas(array('id_activ'=>$id_activ));
            if (empty($cImportadas)) { continue; }
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
			$id_fase_ini = $oCambioUsuarioObjetoPref->getId_fase_ini();
			$id_fase_fin = $oCambioUsuarioObjetoPref->getId_fase_fin();
			
			$fase_correcta = 0;
			/////////////////// COMPARAR STATUS //////////////////////////////////////////
			// Si el id_fase es 1,2,3,4 corresponde al status de la actividad,
			//   porque no tiene instalado el módulo de procesos.
			if ($id_fase_ini < 10 || $id_fase_fin < 10) {
			    if ($id_fase_ini <= $id_fase_cmb && $id_fase_fin >= $id_fase_cmb) {
                    $fase_correcta = 1;
			    }
			} else {
			/////////////////// COMPARAR FASES //////////////////////////////////////////
                // aFases es un array con todas las fases (sf o sv) de la actividad ordenado según el proceso.
                // compruebo que existan las fases inicial i final, sino doy un error 
                if (in_array($id_fase_ini, $aFases) && in_array($id_fase_fin, $aFases)) {
                    //mirar si la fase está dentro del intervalo.
                    $key_ini = array_search($id_fase_ini, $aFases);
                    $key_fin = array_search($id_fase_fin, $aFases);
                    // Si la actividad es de otra dl que no tiene instalados los procesos, la $id_fase_cmb 
                    // corresponde al status de la actividad (1,2,3,4), y por tanto $key_cmb va a dar FALSE.
                    $key_cmb = array_search($id_fase_cmb, $aFases);
                    //echo "<br>fases: $id_fase_ini ::$id_fase_cmb:: $id_fase_fin <br>";
                    //echo "key fases: $key_ini ::$key_cmb:: $key_fin <br>";
                    //print_r($aFases);
                    if ($key_ini <= $key_cmb && $key_fin >= $key_cmb) {
                        $fase_correcta = 1;
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
                        if (!me_afecta($id_usuario,$propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb,$id_pau,$sObjeto)) { $apuntar = false; continue; }
                        if (!empty($valor)) {
                            $operador = empty($operador)? '=' : $operador;
                            if ($valor_old == 't') {
                                $apuntar = comparar($valor_old_cmb,$operador,$valor); 
                            }
                            if ($apuntar === false && $valor_new == 't') {
                                $apuntar = comparar($valor_new_cmb,$operador,$valor); 
                            }
                        } else {
                            $apuntar = true;
                        }
                    }
                }
            } else {
                echo _("ERROR: la fase de la actividad no está en el proceso.");
                echo " id_activ: $id_activ<br>";
                echo " id_usuario: $id_usuario<br>";
                echo " id_fase_ini: $id_fase_ini<br>";
                echo " id_fase_fin: $id_fase_fin<br>";
                print_r($aFases);
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
	if ($num_cambios === $num_cambios_old) { exit (_("Algo falla")); }
}
// al finalizar borro el pid
if(!empty($argv[1])) {
	// Si he lanzado el proceso automáticamente.
	// Borro el pid, para que empieze el siguiente proceso.
	// Hay que asegurarse que se han acabado de escribir todos los anotados, para que no los vuelva a escribir. Po esto espero 7 segundos (con 3 no basta...)
	$pid = ConfigGlobal::$directorio.'/log/avisos.pid';
	$mensaje = "";
	file_put_contents($pid, $mensaje);
}
