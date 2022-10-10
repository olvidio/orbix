<?php
namespace usuarios\model\entity;

use core;

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/10/2010
 */
class Grupo extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Grupo
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Grupo
     *
     * @var array
     */
    protected $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    protected $bLoaded = FALSE;

    /**
     * Id_usuario de Grupo
     *
     * @var integer
     */
    protected $iid_usuario;
    /**
     * Usuario de Grupo
     *
     * @var string
     */
    protected $susuario;
    /**
     * Id_role de Usuario
     *
     * @var integer
     */
    private $iid_role;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_usuario
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_usuario = (integer)$a_id;
                $this->aPrimary_key = array('id_usuario' => $this->iid_usuario);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('aux_grupos_y_usuarios');
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
        $aDades['usuario'] = $this->susuario;
        $aDades['id_role'] = $this->iid_role;

        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					usuario                  = :usuario,
					id_role                  = :id_role";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_usuario='$this->iid_usuario'")) === false) {
                $sClauError = 'Grupo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Grupo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(usuario,id_role)";
            $valores = "(:usuario,:id_role)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Grupo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Grupo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['id_usuario'] = $oDbl->lastInsertId($nom_tabla . '_id_usuario_seq');
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_usuario)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM ONLY $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
                $sClauError = 'Grupo.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_usuario='$this->iid_usuario'")) === false) {
            $sClauError = 'Grupo.eliminar';
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_usuario', $aDades)) $this->setId_usuario($aDades['id_usuario']);
        if (array_key_exists('usuario', $aDades)) $this->setUsuario($aDades['usuario']);
        if (array_key_exists('id_role', $aDades)) $this->setId_role($aDades['id_role']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_usuario('');
        $this->setUsuario('');
        $this->setId_role('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Grupo en un array
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
     * Recupera la clave primaria de Grupo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_usuario' => $this->iid_usuario);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Grupo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_usuario') && $val_id !== '') $this->iid_usuario = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_usuario de Grupo
     *
     * @return integer iid_usuario
     */
    function getId_usuario()
    {
        if (!isset($this->iid_usuario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_usuario;
    }

    /**
     * Establece el valor del atributo iid_usuario de Grupo
     *
     * @param integer iid_usuario
     */
    function setId_usuario($iid_usuario)
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo susuario de Grupo
     *
     * @return string susuario
     */
    function getUsuario()
    {
        if (!isset($this->susuario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->susuario;
    }

    /**
     * Establece el valor del atributo susuario de Grupo
     *
     * @param string susuario='' optional
     */
    function setUsuario($susuario = '')
    {
        $this->susuario = $susuario;
    }

    /**
     * Recupera el atributo iid_role de Usuario
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
     * Establece el valor del atributo iid_role de Usuario
     *
     * @param integer iid_role='' optional
     */
    function setId_role($iid_role = '')
    {
        $this->iid_role = $iid_role;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oGrupoSet = new core\Set();

        $oGrupoSet->add($this->getDatosUsuario());
        $oGrupoSet->add($this->getDatosId_role());
        return $oGrupoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut susuario de Grupo
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosUsuario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'usuario'));
        $oDatosCampo->setEtiqueta(_("usuario"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_role de Usuario
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_role()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_role'));
        $oDatosCampo->setEtiqueta(_("id_role"));
        return $oDatosCampo;
    }
}

?>
