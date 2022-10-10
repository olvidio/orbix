<?php

namespace actividadcargos\model;

/**
 * Clase que implementa la entidad d_asistentes_activ
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class CargoOAsistente
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de CargoOAsistente
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de CargoOAsistente
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_activ de CargoOAsistente
     *
     * @var integer
     */
    private $iid_activ;
    /**
     * Id_nom de CargoOAsistente
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Propio de CargoOAsistente
     *
     * @var boolean
     */
    private $bpropio;
    /**
     * Id_cargo de CargoOAsistente
     *
     * @var integer
     */
    private $iid_cargo;
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id;
            }
        } else {
            if ($a_id !== '') $this->iid_activ = (integer)$a_id;
        }
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Recupera el atributo iid_activ de CargoOAsistente
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de CargoOAsistente
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_nom de CargoOAsistente
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de CargoOAsistente
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo bpropio de CargoOAsistente
     *
     * @return boolean bpropio
     */
    function getPropio()
    {
        return $this->bpropio;
    }

    /**
     * Establece el valor del atributo bpropio de CargoOAsistente
     *
     * @param boolean bpropio='f' optional
     */
    function setPropio($bpropio = 'f')
    {
        $this->bpropio = $bpropio;
    }

    /**
     * Recupera el atributo iid_cargo de CargoOAsistente
     *
     * @return integer iid_cargo
     */
    function getId_cargo()
    {
        return $this->iid_cargo;
    }

    /**
     * Establece el valor del atributo iid_cargo de CargoOAsistente
     *
     * @param integer iid_nom
     */
    function setId_cargo($iid_cargo)
    {
        $this->iid_cargo = $iid_cargo;
    }

}