<?php

namespace cambios\model;

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\Actividad;
use cambios\model\entity\CambioAnotado;
use cambios\model\entity\CambioUsuario;
use cambios\model\entity\GestorCambioAnotado;
use cambios\model\entity\GestorCambioUsuario;
use core\ConfigGlobal;
use permisos\model\PermisosActividades;
use web\DateTimeLocal;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use function core\is_true;


/**
 * Classe para anotar todos los avisos en una tabla
 *
 * @package delegación
 * @subpackage cambios
 * @author
 * @version 1.0
 * @created 24/4/2020
 */
class Avisos
{

    private $id_schema_cmb;
    private $id_item_cmb;
    private $id_usuario;
    private $sObjeto;
    private $aFases_cmb;

    /* METODES  ----------------------------------------------------------*/


    public function crear_pid($username, $esquema)
    {
        if (!empty($username)) {
            // Si he lanzado el proceso automáticamente, escribo el id del proceso.
            // si ya existe un proceso en marcha, salgo del proceso.
            $filename = ConfigGlobal::$directorio . "/log/avisos.$esquema.pid";

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
                        $ahora = date("Y/m/d H:i:s");
                        echo "$ahora ";
                        echo sprintf(_("El fichero %s no està vacio."), $filename);
                        echo " ";
                        echo _("Posiblemente la anterior operación finalizó con error");
                    } else {
                        exit;
                    }
                }
            }
            $ahora = date("Y/m/d H:i:s");
            $mensaje = "$ahora -- $username \n";
            file_put_contents($filename, $mensaje, LOCK_EX);
        }
    }

    public function borrar_pid($username, $esquema)
    {
        // al finalizar borro el pid
        if (!empty($username)) {
            // Si he lanzado el proceso automáticamente.
            // Borro el pid, para que empieze el siguiente proceso.
            // Hay que asegurarse que se han acabado de escribir todos los anotados, para que no los vuelva a escribir.
            // Por esto espero 7 segundos (con 3 no basta...)
            $filename = ConfigGlobal::$directorio . "/log/avisos.$esquema.pid";

            if (file_exists($filename)) {
                $mensaje = "";
                file_put_contents($filename, $mensaje, LOCK_EX);
            }
        }
    }

    public function fn_apuntar($aviso_tipo)
    {
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
        $err_fila = '';
        if (count($cCambioUsuario) > 0) {
            $err_fila .= "<tr>";
            $err_fila .= "<td>" . $this->id_schema_cmb . "</td>";
            $err_fila .= "<td>" . $this->id_item_cmb . "</td>";
            $err_fila .= "<td>" . $this->id_usuario . "</td>";
            $err_fila .= "<td>" . $aviso_tipo . "</td>";
            $err_fila .= "</tr>";
        } else {
            $oCambioUsuario = new CambioUsuario();
            $oCambioUsuario->setId_schema_cambio($this->id_schema_cmb);
            $oCambioUsuario->setId_item_cambio($this->id_item_cmb);
            $oCambioUsuario->setId_usuario($this->id_usuario);
            $oCambioUsuario->setSfsv($sfsv);
            $oCambioUsuario->setAviso_tipo($aviso_tipo);
            //echo "id_item_cmb: $id_item_cmb, id_usuario: $id_usuario, aviso_tipo: $aviso_tipo \n";
            if ($oCambioUsuario->DBGuardar() === false) {
                echo ConfigGlobal::$web_server . '-->' . date('Y/m/d') . " " . _("Hay un error, no se ha guardado");
            }
        }
        //anotado($id_item_cmb); // En principio ya lo hace al final de todo.
        if (!empty($err_fila)) {
            return $err_fila;
        }
    }

    public function anotado()
    {
        $ubicacion = getenv('UBICACION');
        $server = getenv('DB_SERVER');

        // marcar como apuntado
        $aWhere = ['id_schema_cambio' => $this->id_schema_cmb,
            'id_item_cambio' => $this->id_item_cmb,
            'server' => $server,
        ];
        $gesCambiosAnotados = new GestorCambioAnotado();
        $gesCambiosAnotados->setTabla($ubicacion);
        $cCambiosAnotados = $gesCambiosAnotados->getCambiosAnotados($aWhere);
        // debería ser único
        if (count($cCambiosAnotados) > 0) {
            $oCambioAnotado = $cCambiosAnotados[0];
            $oCambioAnotado->DBCarregar();
        } else {
            $oCambioAnotado = new CambioAnotado();
            $oCambioAnotado->setTabla($ubicacion);
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

    public function comparar($valor_cmb, $operador, $valor)
    {
        switch ($operador) {
            case '=':
                if (strpos($valor, ',')) { // es una lista de valores
                    $a_val = explode(',', $valor);
                    $rta = false;
                    foreach ($a_val as $val) {
                        $rta = $rta || ($valor_cmb == $val);
                    }
                    return $rta;
                } else {
                    // ojo con los boolean.
                    if ((is_true($valor_cmb)) || (is_true($valor))) {
                        return ((bool)$valor_cmb === (bool)$valor);
                    }
                    if ((!is_true($valor_cmb)) || (!is_true($valor))) {
                        return ((bool)$valor_cmb === (bool)$valor);
                    }
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

    public function me_afecta($propiedad, $id_activ, $valor_old_cmb, $valor_new_cmb, $id_pau, $sObjeto)
    {
        //echo "usuario: $id_usuario, camp: $propiedad, id_activ: $id_activ <br>\n";
        // Si el usuario es una casa o un sacd, sólo ve los cambios que le afectan:
        $oMiUsuario = new Usuario($this->id_usuario);

        //casa
        if ($oMiUsuario->isRolePau(Role::PAU_CDC)) {
            $mis_id_ubis = $oMiUsuario->getId_pau(); // puede ser una lista separada por comas.
            if (!empty($mis_id_ubis)) { //casa o un listado de ubis en la preferencia del aviso.
                $a_mis_id_ubis = explode(',', $mis_id_ubis);

                $oActividad = new Actividad($id_activ);
                $id_ubi = $oActividad->getId_ubi(); // id ubi actual.

                // si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
                if ($propiedad == 'id_ubi') {
                    return (in_array($valor_old_cmb, $a_mis_id_ubis) || in_array($id_ubi, $a_mis_id_ubis));
                } else {
                    // si cambia qualquier otra cosa en mi id_ubi.
                    if (in_array($id_ubi, $a_mis_id_ubis)) {
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
            $id_nom_usuario = $oMiUsuario->getId_pau();
            // soy jefe zona?
            // si soy jefe de zona me afectan todos los sacd de la zona.
            $rta = FALSE;
            $GesZonas = new GestorZona();
            $cZonas = $GesZonas->getZonas(array('id_nom' => $id_nom_usuario));
            if (is_array($cZonas) && count($cZonas) > 0) {
                // sacd de mi zona
                $GesZonaSacd = new GestorZonaSacd();
                foreach ($cZonas as $oZona) {
                    $id_zona = $oZona->getId_zona();
                    $cSacds = $GesZonaSacd->getSacdsZona($id_zona);
                    foreach ($cSacds as $id_nom_sacd) {
                        $rta = $this->tengoPermiso($propiedad, $id_activ, $id_nom_sacd, $valor_old_cmb, $valor_new_cmb);
                        if ($rta === TRUE) {
                            return TRUE;
                            // no hace falta seguir mirando todos, con uno basta para avisar.
                        }
                    }
                }
            } else { // No soy jefe de zona
                $rta = $this->tengoPermiso($propiedad, $id_activ, $id_nom_usuario, $valor_old_cmb, $valor_new_cmb);
            }
            return $rta;
        }
        // En el caso de no ser casa ni sacd 
        // comparar si el aviso corresponde a la casa (id_pau)
        if (!empty($id_pau)) { //casa o un listado de ubis en la preferencia del aviso.
            $a_id_pau = explode(',', $id_pau);

            $oActividad = new Actividad($id_activ);
            $id_ubi = $oActividad->getId_ubi(); // id ubi actual.
            // si lo que cambia es el id_ubi, compruebo que el valor old o new sean de la casa.
            if ($propiedad == 'id_ubi') {
                return (in_array($valor_old_cmb, $a_id_pau) || in_array($id_ubi, $a_id_pau));
            } else {
                // si cambia qualquier otra cosa en mi id_ubi.
                if (in_array($id_ubi, $a_id_pau)) {
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
        } else {
            // En el caso de no tener ninguna casa: retornar TRUE
            return TRUE;
        }
    }

    /**
     * Mira si el cambio, afecta a uno de los sacd de la zona y si tengo permiso para ver.
     * El id_nom puede ser cualquiera de la zona, no el que origina el cambio.
     *
     * @param string $propiedad
     * @param integer $id_activ
     * @param integer $id_nom
     * @param mixed $valor_old_cmb
     * @param mixed $valor_new_cmb
     * @return boolean
     */
    private function tengoPermiso(string $propiedad, int $id_activ, int $id_nom, mixed $valor_old_cmb, mixed $valor_new_cmb)
    {
        switch ($this->sObjeto) {
            case 'Actividad':
                // busco los datos de las actividades
                $aWhereAct = ['id_activ' => $id_activ];
                $aOperadorAct = [];
                $aWhere = ['id_nom' => $id_nom];
                $aOperador = [];

                $permiso_ver = FALSE;
                $oGesActividadCargo = new GestorActividadCargo();
                $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
                if (is_array($cAsistentes) && !empty($cAsistentes)) {
                    $aAsistente = $cAsistentes[$id_activ];
                    $propio = $aAsistente['propio'];
                    $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

                    $oPermActividades = new PermisosActividades($this->id_usuario);
                    $oPermActividades->setActividad($id_activ);
                    $permiso_ver = $oPermActividades->havePermisoSacd($id_cargo, $propio);
                }
                return $permiso_ver;
                break;
            case 'ActividadCargoNoSacd':
                // si lo que cambia es el id_nom, compruebo que el valor old o new sean de algun sacd de la zona.
                // y tenga permiso.
                if ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permCargo($id_activ)
                ) {
                    return TRUE;
                }
                return FALSE;
                break;
            case 'ActividadCargoSacd':
                // si lo que cambia es el id_nom, compruebo que el valor old o new sean de algun sacd de la zona.
                // y tenga permiso.
                if ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permCargoSacd($id_activ)
                ) {
                    return TRUE;
                }
                return FALSE;
                break;
            case 'Asistente':
                // si lo que cambia es el id_nom, compruebo que el valor old o new sean de algún sacd de la zona.
                // y tenga permiso.
                if ($propiedad === 'id_nom'
                    && (($valor_old_cmb == $id_nom) || ($valor_new_cmb == $id_nom))
                    && $this->permAsiste($id_activ)
                ) {
                    return TRUE;
                }
                return FALSE;
                break;
        }
    }

    private function permCargo($id_activ)
    {
        $oPermActividades = new PermisosActividades($this->id_usuario);
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->aFases_cmb);
        $oPermSacd = $oPermActividades->getPermisoOn('cargos');
        return $oPermSacd->have_perm_activ('ver');
    }

    private function permCargoSacd($id_activ)
    {
        // compruebo si tengo permiso para sacd:
        $oPermActividades = new PermisosActividades($this->id_usuario);
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->aFases_cmb);
        $oPermSacd = $oPermActividades->getPermisoOn('sacd');
        return $oPermSacd->have_perm_activ('ver');
    }

    private function permAsiste($id_activ)
    {
        $oPermActividades = new PermisosActividades($this->id_usuario);
        $oPermActividades->setActividad($id_activ);
        $oPermActividades->setFasesCompletadas($this->aFases_cmb);
        $oPermAsisSacd = $oPermActividades->getPermisoOn('asistentesSacd');
        return $oPermAsisSacd->have_perm_activ('ver');
    }

    public function setId_schema_cmb($id_schema_cmb)
    {
        $this->id_schema_cmb = $id_schema_cmb;
    }

    public function setId_item_cmb($id_item_cmb)
    {
        $this->id_item_cmb = $id_item_cmb;
    }

    public function setId_usuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function setObjeto($sObjeto)
    {
        $this->sObjeto = $sObjeto;
    }

    public function setFasesCmb($aFases_cmb)
    {
        $this->aFases_cmb = $aFases_cmb;
    }
}