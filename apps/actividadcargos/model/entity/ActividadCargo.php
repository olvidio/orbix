<?php

namespace actividadcargos\model\entity;
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
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $aDades = $this->getAllAtributes();
        $id_cargo = $this->getId_cargo();
        $oCargo = new Cargo($id_cargo);
        $tipo_cargo = $oCargo->getTipo_cargo();
        switch ($tipo_cargo) {
            case 'sacd':
                $oActividadCargoI = new ActividadCargoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAtributes($aDades);
                return $oActividadCargoI->DBGuardar($quiet);
                break;
            default:
                $oActividadCargoI = new ActividadCargoNoSacd($this->aPrimary_key);
                $oActividadCargoI->setAllAtributes($aDades);
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
        $oCargo = new Cargo($id_cargo);
        $tipo_cargo = $oCargo->getTipo_cargo();
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