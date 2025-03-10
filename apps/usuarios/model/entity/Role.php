<?php

namespace usuarios\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula aux_roles
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/01/2014
 */

/**
 * Clase que implementa la entidad aux_roles
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/01/2014
 */
class Role extends ClasePropiedades
{

    // pau constants.
    const PAU_CDC = 'cdc'; // Casa.
    const PAU_CTR = 'ctr'; // Centro.
    const PAU_NOM = 'nom'; // Persona.
    const PAU_SACD = 'sacd'; // Sacd.

    const ARRAY_PAU_TXT = [
        self::PAU_CDC => 'cdc',
        self::PAU_CTR => 'ctr',
        self::PAU_NOM => 'nom',
        self::PAU_SACD => 'sacd',
    ];

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Role
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Role
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
     * Id_role de Role
     *
     * @var integer
     */
    private $iid_role;
    /**
     * Role de Role
     *
     * @var string
     */
    private $srole;
    /**
     * Sf de Role
     *
     * @var boolean
     */
    private $bsf;
    /**
     * Sv de Role
     *
     * @var boolean
     */
    private $bsv;
    /**
     * Pau de Role
     *
     * @var string
     */
    private $spau;
    /**
     * Dmz de Role
     *
     * @var boolean
     */
    private $bdmz;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_role
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_role') && $val_id !== '') $this->iid_role = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_role = (integer)$a_id; 
                $this->aPrimary_key = array('id_role' => $this->iid_role);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_roles');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = array();
        $aDades['role'] = $this->srole;
        $aDades['sf'] = $this->bsf;
        $aDades['sv'] = $this->bsv;
        $aDades['pau'] = $this->spau;
        $aDades['dmz'] = $this->bdmz;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sf'])) {
            $aDades['sf'] = 'true';
        } else {
            $aDades['sf'] = 'false';
        }
        if (is_true($aDades['sv'])) {
            $aDades['sv'] = 'true';
        } else {
            $aDades['sv'] = 'false';
        }
        if (is_true($aDades['dmz'])) {
            $aDades['dmz'] = 'true';
        } else {
            $aDades['dmz'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					role                     = :role,
					sf                       = :sf,
					sv                       = :sv,
					pau                      = :pau,
					dmz                      = :dmz";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_role='$this->iid_role'")) === false) {
                $sClauError = 'Role.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Role.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(role,sf,sv,pau,dmz)";
            $valores = "(:role,:sf,:sv,:pau,:dmz)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Role.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Role.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_role = $oDbl->lastInsertId('aux_roles_id_role_seq');
        }
        $this->setAllAtributes($aDades);
        return true;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_role)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_role='$this->iid_role'")) === false) {
                $sClauError = 'Role.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return false;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_role='$this->iid_role'")) === false) {
            $sClauError = 'Role.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_role', $aDades)) $this->setId_role($aDades['id_role']);
        if (array_key_exists('role', $aDades)) $this->setRole($aDades['role']);
        if (array_key_exists('sf', $aDades)) $this->setSf($aDades['sf']);
        if (array_key_exists('sv', $aDades)) $this->setSv($aDades['sv']);
        if (array_key_exists('pau', $aDades)) $this->setPau($aDades['pau']);
        if (array_key_exists('dmz', $aDades)) $this->setDmz($aDades['dmz']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_role('');
        $this->setRole('');
        $this->setSf('');
        $this->setSv('');
        $this->setPau('');
        $this->setDmz('');
        $this->setPrimary_key($aPK);
    }
    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Role en un array
     *
     * @return array aDades
     */
    function getTot()
    {
        if (!is_array($this->aDades)) {
            $this->DBCarregar('tot');
        }
        return $this->aDades;
    }

    /**
     * Recupera la clave primaria de Role en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_role' => $this->iid_role);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Role en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_role') && $val_id !== '') $this->iid_role = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_role de Role
     *
     * @return integer iid_role
     */
    function getId_role()
    {
        if (!isset($this->iid_role) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_role;
    }

    /**
     * Establece el valor del atributo iid_role de Role
     *
     * @param integer iid_role
     */
    function setId_role($iid_role)
    {
        $this->iid_role = $iid_role;
    }

    /**
     * Recupera el atributo srole de Role
     *
     * @return string srole
     */
    function getRole()
    {
        if (!isset($this->srole) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->srole;
    }

    /**
     * Establece el valor del atributo srole de Role
     *
     * @param string srole='' optional
     */
    function setRole($srole = '')
    {
        $this->srole = $srole;
    }

    /**
     * Recupera el atributo bsf de Role
     *
     * @return boolean bsf
     */
    function isSf()
    {
        if (!isset($this->bsf) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsf;
    }

    /**
     * Establece el valor del atributo bsf de Role
     *
     * @param boolean bsf='f' optional
     */
    function setSf($bsf = 'f')
    {
        $this->bsf = $bsf;
    }

    /**
     * Recupera el atributo bsv de Role
     *
     * @return boolean bsv
     */
    function isSv()
    {
        if (!isset($this->bsv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsv;
    }

    /**
     * Establece el valor del atributo bsv de Role
     *
     * @param boolean bsv='f' optional
     */
    function setSv($bsv = 'f')
    {
        $this->bsv = $bsv;
    }

    /**
     * Recupera el atributo spau de Role
     *
     * @return string spau
     */
    function getPau()
    {
        if (!isset($this->spau) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->spau;
    }

    /**
     * Establece el valor del atributo spau de Role
     *
     * @param string spau='' optional
     */
    function setPau($spau = '')
    {
        $this->spau = $spau;
    }

    /**
     * Recupera el atributo bdmz de Role
     *
     * @return string bdmz
     */
    function getDmz()
    {
        if (!isset($this->bdmz) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdmz;
    }

    /**
     * Establece el valor del atributo bdmz de Role
     *
     * @param string bdmz='' optional
     */
    function setDmz($bdmz = '')
    {
        $this->bdmz = $bdmz;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oRoleSet = new Set();

        $oRoleSet->add($this->getDatosRole());
        $oRoleSet->add($this->getDatosSf());
        $oRoleSet->add($this->getDatosSv());
        $oRoleSet->add($this->getDatosPau());
        $oRoleSet->add($this->getDatosDmz());
        return $oRoleSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut srole de Role
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosRole()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'role'));
        $oDatosCampo->setEtiqueta(_("role"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsf de Role
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSf()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sf'));
        $oDatosCampo->setEtiqueta(_("sf"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsv de Role
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sv'));
        $oDatosCampo->setEtiqueta(_("sv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut spau de Role
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPau()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pau'));
        $oDatosCampo->setEtiqueta(_("pau"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bdmz de Role
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDmz()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dmz'));
        $oDatosCampo->setEtiqueta(_("dmz"));
        return $oDatosCampo;
    }
}
