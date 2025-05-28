<?php

namespace asistentes\model\entity;

use actividades\model\entity\ActividadAll;
use personas\model\entity\PersonaEx;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la vista av_asistentes_in
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad av_asistentes_in
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class AsistenteIn extends AsistentePub
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     * // Crec que no cal fer res
     * function __construct($a_id='') {
     * $oDbl = $GLOBALS['oDBEP'];
     * if (is_array($a_id)) {
     * $this->aPrimary_key = $a_id;
     * foreach($a_id as $nom_id=>$val_id) {
     * if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
     * if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
     * }
     * }
     * $this->setoDbl($oDbl);
     * $this->setNomTabla('d_asistentes_de_paso');
     * }
     */
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function DBGuardar($quiet = 0)
    {
        // Para los de paso si se puede guardar. Para el reso NO
        if (is_true($this->perm_modificar())) {
            parent::DBGuardar($quiet);
        } else {
            exit (_("los datos de asistencia los modifica la dl del asistente"));
            return FALSE;
        }
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        // Para los de paso si se puede guardar. Para el resto NO
        if (is_true($this->perm_modificar())) {
            parent::DBEliminar();
        } else {
            $id_nom = $this->getId_nom();
            $id_activ = $this->getId_activ();
            $oPersona = new PersonaEx($id_nom);
            $ape_nom = $oPersona->getApellidosNombre();
            $oActividad = new ActividadAll($id_activ);
            $nom_activ = $oActividad->getNom_activ();

            $error_txt = _("el asistente es de otra dl. Se debe modificar en la dl origen.");
            $error_txt .= "<br>";
            $error_txt .= "actividad: $nom_activ<br>";
            $error_txt .= "asistente: $ape_nom<br>";
            echo $error_txt;
            return FALSE;
        }
    }
    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
}