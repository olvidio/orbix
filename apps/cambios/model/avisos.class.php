<?php
namespace cambios\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\Actividad;
use asistentes\model\entity\GestorAsistenteDl;
use cambios\model\entity\CambioAnotado;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\GestorCambioAnotado;
use cambios\model\entity\GestorCambioUsuario;
use function cambios\model\generarTablaAvisos\soy_encargado;
use core\ConfigGlobal;
use permisos\model\PermisosActividades;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\DateTimeLocal;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;


/**
 * Classe para anotar todos los avisos en una tabla
 * 
 * @package delegación
 * @subpackage cambios
 * @author
 * @version 1.0
 * @created 24/4/2020
 */

class Avisos {
    
    private $id_schema_cmb;
    private $id_item_cmb;
    private $id_usuario;
    private $sObjeto;
    private $aFases_cmb;
    
	/* METODES  ----------------------------------------------------------*/
    
    
    public function crear_pid($username,$esquema) {
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
                    $matches = [];
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

    public function borrar_pid($username,$esquema) {
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

    public function fn_apuntar($aviso_tipo,$aviso_donde) {
        $sfsv = ConfigGlobal::mi_sfsv();
        // Asegurar que no existe:
        $aWhere = [];
        $aWhere['id_schema_cambio'] = $this->id_schema_cmb;
        $aWhere['id_item_cambio'] = $this->id_item_cmb;
        $aWhere['sfsv'] = $sfsv;
        $aWhere['id_usuario'] = $this->id_usuario;
        $aWhere['aviso_tipo'] = $aviso_tipo;
        $oGesCambiosUsuario = new GestorCambioUsuario();
        $cCambioUsuario = $oGesCambiosUsuario->getCambiosUsuario($aWhere);
        // ya existe
        $err_tabla = '';
        if (count($cCambioUsuario) > 0) {
            if (empty($err_tabla)) {
                $err_tabla .= _("apuntar cambio usuario");
                $err_tabla .= " ".ConfigGlobal::$web_server.'-->'.date('Y/m/d') . " " . _("Ya existe").": ";
                $err_tabla .= '<table><tr>'; 
                $err_tabla .= '<th>' . _("schema") . '</th>'; 
                $err_tabla .= '<th>' . _("id_item_cmb") . '</th>'; 
                $err_tabla .= '<th>' . _("id_usuario") . '</th>'; 
                $err_tabla .= '<th>' . _("aviso tipo") . '</th>'; 
                $err_tabla .= '</tr>'; 
            }
            $err_tabla .= "<tr><td>". $this->id_schema_cmb . "</td>";
            $err_tabla .= "<td>". $this->id_item_cmb . "</td>";
            $err_tabla .= "<td>". $this->id_usuario . "</td>";
            $err_tabla .= "<td>". $aviso_tipo . "</td>";
            $err_tabla .= "</tr>";
        } else {
            $oCambioUsuario = new CambioUsuario();
            $oCambioUsuario->setId_schema_cambio($this->id_schema_cmb);
            $oCambioUsuario->setId_item_cambio($this->id_item_cmb);
            $oCambioUsuario->setId_usuario($this->id_usuario);
            $oCambioUsuario->setSfsv($sfsv);
            $oCambioUsuario->setAviso_tipo($aviso_tipo);
            $oCambioUsuario->setAviso_donde($aviso_donde);
            //echo "id_item_cmb: $id_item_cmb, id_usuario: $id_usuario, aviso_tipo: $aviso_tipo, aviso_donde: $aviso_donde\n";
            if ($oCambioUsuario->DBGuardar() === false) {
                echo ConfigGlobal::$web_server.'-->'.date('Y/m/d') . " " . _("Hay un error, no se ha guardado");
            }
        }
        //anotado($id_item_cmb); // En principio ya lo hace al final de todo.
        if (!empty($err_tabla)) {
            $err_tabla .="</table>";
            echo $err_tabla;
        }
    }
    
    public function anotado() {
        if ( $_SERVER['DB_SERVER'] == 1) {
            $server = 1;
        } else {
            $server = 2;
        }
        // marcar como apuntado
        $aWhere = ['id_schema_cambio' => $this->id_schema_cmb,
                   'id_item_cambio' => $this->id_item_cmb,
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
           $oCambioAnotado->setId_item_cambio($this->id_item_cmb);
           $oCambioAnotado->setId_schema_cambio($this->id_schema_cmb);
           $oCambioAnotado->setServer($server);
        }

        $oCambioAnotado->setAnotado('t');
        if ($oCambioAnotado->DBGuardar(true) === false) { //'true' para que no genere la tabla de avisos.
            echo _("Hay un error, no se ha guardado");
            echo _("anotado");
        }
    }

    public function comparar($valor_cmb,$operador,$valor) {
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

    public function me_afecta($propiedad,$id_activ,$valor_old_cmb,$valor_new_cmb) {
        //echo "usuario: $id_usuario, camp: $propiedad, id_activ: $id_activ <br>\n";
        // Si el usuario es una casa o un sacd, sólo ve los cambios que le afectan:
        $oMiUsuario = new Usuario($this->id_usuario);

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
                        switch ($this->sObjeto) {
                            case 'ActividadCargoNoSacd':
                            case 'ActividadCargoSacd':
                                // si lo que cambia es el campo observaciones, no hace falata informar.
                                if ($propiedad == 'observ') {
                                    return FALSE;
                                }
                        }
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            }
        }
        // si soy un sacd.
        if ($oMiUsuario->isRolePau(Role::PAU_SACD)) {
            $oMiUsuario = new Usuario($this->id_usuario);
            $id_nom_usuario = $oMiUsuario->getId_pau();
            // soy jefe zona?
            // si soy jefe de zona me afectan todos los sacd de la zona.
            $rta = 0;
            $GesZonas = new GestorZona();
            $cZonas = $GesZonas->getZonas(array('id_nom' => $id_nom_usuario));
            if (is_array($cZonas) && count($cZonas)>0) {
                // sacd de mi zona
                $GesZonaSacd = new GestorZonaSacd();
                foreach ($cZonas as $oZona) {
                    $id_zona = $oZona->getId_zona();
                    $cSacds = $GesZonaSacd->getSacdsZona($id_zona);
                    foreach ($cSacds as $id_nom_sacd) {
                        $rta = $this->tengoPermiso($propiedad,$id_activ,$id_nom_sacd,$valor_old_cmb,$valor_new_cmb); 
                        if ($rta === TRUE) {
                            return TRUE;
                            // no hace falta seguir mirando todos.
                        }
                    }
                }
            } else { // No soy jefe de zona
                $rta = $this->tengoPermiso($propiedad,$id_activ,$id_nom_usuario,$valor_old_cmb,$valor_new_cmb); 
            }
            if ($rta) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        // En el caso de no ser casa ni sacd retornar TRUE
        return TRUE;
    }
    
    private function tengoPermiso($propiedad,$id_activ,$id_nom,$valor_old_cmb,$valor_new_cmb) {
        switch ($this->sObjeto) {
            case 'Actividad':
                // busco los datos de las actividades
                $aWhereAct = [ 'id_activ' => $id_activ];
                $aOperadorAct = [];
                $aWhere = ['id_nom' => $id_nom];
                $aOperador = [];
                
                $permiso_ver = FALSE;
                $oGesActividadCargo = new GestorActividadCargo();
                $cAsistentes = $oGesActividadCargo ->getAsistenteCargoDeActividad($aWhere,$aOperador,$aWhereAct,$aOperadorAct);
                if (is_array($cAsistentes) && count($cAsistentes) > 0) {
                    $aAsistente = $cAsistentes[$id_activ];
                    $propio = $aAsistente['propio'];
                    //$plaza = $aAsistente['plaza'];
                    $id_cargo = empty($aAsistente['id_cargo'])? '' : $aAsistente['id_cargo'];
                    
                    $oPermActividades = new PermisosActividades($this->id_usuario);
                    $oPermActividades->setActividad($id_activ);
                    $permiso_ver = $oPermActividades->havePermisoSacd($id_cargo, $propio);
                }
                return $permiso_ver;
                break;
            case 'ActividadCargoNoSacd':
                if ($this->cargo($id_nom, $id_activ)) {
                    return TRUE;
                } else {
                    // si lo que cambia es el id_nom, compruebo que el valor old o new sean del sacd.
                    if ($propiedad == 'id_nom') {
                        if (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom)) {
                            return TRUE;
                        }
                    }
                }
                return FALSE;
                break;
            case 'ActividadCargoSacd':
                if ($this->cargoSacd($id_nom, $id_activ)) {
                    return TRUE;
                } else {
                    // si lo que cambia es el id_nom, compruebo que el valor old o new sean del sacd.
                    if ($propiedad == 'id_nom') {
                        if (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom)) {
                            return TRUE;
                        }
                    }
                }
                return FALSE;
                break;
            case 'Asistente':
                // si lo que cambia es el id_nom, compruebo que el valor old o new sean de algun sacd de la zona.
                if ($propiedad == 'id_nom') {
                    if ($this->asiste($id_nom, $id_activ)) {
                        return TRUE;
                    } else {
                        if (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom)) {
                            return TRUE;
                        }
                    }
                }
                return FALSE;
            break;
        }
    }

    private function cargo($id_nom,$id_activ) {
        // compruebo si el sacd tiene cargo.
        // y la fase okSacd está on:
        $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
        $GesActividadCargo = new GestorActividadCargo();
        $a_Asistentes = $GesActividadCargo->getActividadCargos($aWhere);
        if (count($a_Asistentes)>0) {
            $oPermActividades = new PermisosActividades($this->id_usuario);
            $oPermActividades->setActividad($id_activ);
            $oPermActividades->setFasesCompletadas($this->aFases_cmb);
            $oPermSacd = $oPermActividades->getPermisoOn('cargos');
            if ( $oPermSacd->have_perm_activ('ver') ) {
                return TRUE;
            }
        }
        return FALSE; 
    }

    private function cargoSacd($id_nom,$id_activ) {
        // compruebo si el sacd tiene cargo.
        // y la fase okSacd está on:
        $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
        $GesActividadCargo = new GestorActividadCargo();
        $a_Asistentes = $GesActividadCargo->getActividadCargos($aWhere);
        if (count($a_Asistentes)>0) {
            $oPermActividades = new PermisosActividades($this->id_usuario);
            $oPermActividades->setActividad($id_activ);
            $oPermActividades->setFasesCompletadas($this->aFases_cmb);
            $oPermSacd = $oPermActividades->getPermisoOn('sacd');
            if ( $oPermSacd->have_perm_activ('ver') ) {
                return TRUE;
            }
        }
        return FALSE; 
    }
    private function asiste($id_nom,$id_activ) {
        // compruebo si el sacd asiste.
        // y la fase asistente sacd es ok.
        $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
        $GesAsistentes = new GestorAsistenteDl();
        $a_Asistentes = $GesAsistentes->getAsistentes($aWhere);
        if (count($a_Asistentes)>0) {
            $oPermActividades = new PermisosActividades($this->id_usuario);
            $oPermActividades->setActividad($id_activ);
            $oPermActividades->setFasesCompletadas($this->aFases_cmb);
            $oPermAsisSacd = $oPermActividades->getPermisoOn('asistentesSacd');
            if ( $oPermAsisSacd->have_perm_activ('ver') ) {
                return TRUE;
            }
        }
        return FALSE; 
    }
    
    /*
    private function tengoPermiso($propiedad,$id_activ,$id_nom,$valor_old_cmb,$valor_new_cmb) {
        $rta = 0;
        switch ($this->sObjeto) {
            case 'Actividad':
                // compruebo si el sacd tienen cargo.
                // y la fase okSacd está on:
                $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
                $GesActividadCargo = new GestorActividadCargo();
                $a_Asistentes = $GesActividadCargo->getActividadCargos($aWhere);
                if (count($a_Asistentes)>0) {
                    $oPermActividades = new PermisosActividades($this->id_usuario);
                    $oPermActividades->setActividad($id_activ);
                    $oPermSacd = $oPermActividades->getPermisoOn('sacd');
                    if ( !$oPermSacd->have_perm_activ('ver') ) { continue; }
                    $rta += 1;
                }
                // sino, compruebo si el sacd asiste.
                // y la fase es ok.
                if (empty($rta)) {
                    $aWhere = ['id_nom' => $id_nom, 'id_activ' => $id_activ];
                    $GesAsistentes = new GestorAsistenteDl();
                    $a_Asistentes = $GesAsistentes->getAsistentes($aWhere);
                    if (count($a_Asistentes)>0) {
                        $oPermActividades = new PermisosActividades($this->id_usuario);
                        $oPermActividades->setActividad($id_activ);
                        $oPermAsisSacd = $oPermActividades->getPermisoOn('asistentesSacd');
                        if ( !$oPermAsisSacd->have_perm_activ('ver') ) { continue; }
                        $rta += 1;
                    }
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
    */
    
    public function setId_schema_cmb($id_schema_cmb) {
        $this->id_schema_cmb = $id_schema_cmb;
    }
    
    public function setId_item_cmb($id_item_cmb) {
        $this->id_item_cmb = $id_item_cmb;
    }
    
    public function setId_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
    
    public function setObjeto($sObjeto) {
        $this->sObjeto = $sObjeto;
    }
    
    public function setFasesCmb($aFases_cmb) {
        $this->aFases_cmb = $aFases_cmb;
    }
}