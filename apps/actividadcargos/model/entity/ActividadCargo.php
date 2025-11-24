<?php

namespace actividadcargos\model\entity;
use src\actividadcargos\application\repositories\CargoRepository;

/**
 * Fitxer amb la Classe que accedeix a la taula d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */

/**
 * Clase que implementa la entidad d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class ActividadCargo extends ActividadCargoAbstract
{

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $aDades = $this->getAllAtributes();
        $id_cargo = $this->getId_cargo();
        $oCargo = (new CargoRepository())->findById($id_cargo);
        $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
        switch ($tipo_cargo) {
            case 'sacd':
                $oActividadCargoI = new ActividadCargoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAttributes($aDades);
                return $oActividadCargoI->DBGuardar($quiet);
                break;
            default:
                $oActividadCargoI = new ActividadCargoNoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAttributes($aDades);
                return $oActividadCargoI->DBGuardar($quiet);
                break;
        }
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $id_cargo = $this->getId_cargo();
        $oCargo = (new CargoRepository())->findById($id_cargo);
        $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
        switch ($tipo_cargo) {
            case 'sacd':
                $oActividadCargoI = new ActividadCargoSacd($this->aPrimary_key);
                $oActividadCargoI->DBEliminar();
                break;
            default:
                $oActividadCargoI = new ActividadCargoNoSacd($this->aPrimary_key);
                $oActividadCargoI->DBEliminar();
                break;
        }
    }
}