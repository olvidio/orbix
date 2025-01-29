<?php

namespace cambios\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */

/**
 * Clase que implementa la entidad av_cambios_usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioUsuario extends ClasePropiedades
{

    // aviso tipo constants.
    const TIPO_LISTA = 1; // Anotar en lista.
    const TIPO_MAIL = 2; // por mail.

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de CambioUsuario
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de CambioUsuario
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
     * Id_item de CambioUsuario
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_schema_cambio de CambioUsuario
     *
     * @var integer
     */
    private $iid_schema_cambio;
    /**
     * Id_item_cambio de CambioUsuario
     *
     * @var integer
     */
    private $iid_item_cambio;
    /**
     * Id_usuario de CambioUsuario
     *
     * @var integer
     */
    private $iid_usuario;
    /**
     * Sfsv de CambioUsuario
     *
     * @var integer
     */
    private $isfsv;
    /**
     * Aviso_tipo de CambioUsuario
     *
     * @var integer
     */
    private $iaviso_tipo;
    /**
     * Avisado de CambioUsuario
     *
     * @var boolean
     */
    private $bavisado;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de CambioUsuario
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de CambioUsuario
     *
     * @var string
     */
    protected $sNomTabla;
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_item
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_usuario');
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
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = array();
        $aDades['id_schema_cambio'] = $this->iid_schema_cambio;
        $aDades['id_item_cambio'] = $this->iid_item_cambio;
        $aDades['id_usuario'] = $this->iid_usuario;
        $aDades['sfsv'] = $this->isfsv;
        $aDades['aviso_tipo'] = $this->iaviso_tipo;
        $aDades['avisado'] = $this->bavisado;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['avisado'])) {
            $aDades['avisado'] = 'true';
        } else {
            $aDades['avisado'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					id_usuario               = :id_usuario,
					sfsv                     = :sfsv,
					aviso_tipo               = :aviso_tipo,
					avisado                  = :avisado";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'CambioUsuario.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CambioUsuario.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_schema_cambio,id_item_cambio,id_usuario,sfsv,aviso_tipo,avisado)";
            $valores = "(:id_schema_cambio,:id_item_cambio,:id_usuario,:sfsv,:aviso_tipo,:avisado)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'CambioUsuario.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CambioUsuario.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_item_cambio = $oDbl->lastInsertId('av_cambios_usuario_id_item_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'CambioUsuario.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return FALSE;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
            }
            return TRUE;
        } else {
            return FALSE;
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
            $sClauError = 'CambioUsuario.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_schema_cambio', $aDades)) $this->setId_schema_cambio($aDades['id_schema_cambio']);
        if (array_key_exists('id_item_cambio', $aDades)) $this->setId_item_cambio($aDades['id_item_cambio']);
        if (array_key_exists('id_usuario', $aDades)) $this->setId_usuario($aDades['id_usuario']);
        if (array_key_exists('sfsv', $aDades)) $this->setSfsv($aDades['sfsv']);
        if (array_key_exists('aviso_tipo', $aDades)) $this->setAviso_tipo($aDades['aviso_tipo']);
        if (array_key_exists('avisado', $aDades)) $this->setAvisado($aDades['avisado']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_schema_cambio('');
        $this->setId_item_cambio('');
        $this->setId_usuario('');
        $this->setSfsv('');
        $this->setAviso_tipo('');
        $this->setAvisado('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de CambioUsuario en un array
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
     * Recupera la clave primaria de CambioUsuario en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de CambioUsuario en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de CambioUsuario
     *
     * @return integer iid_item
     */
    function getId_item()
    {
        if (!isset($this->iid_item) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item;
    }

    /**
     * Establece el valor del atributo iid_item de CambioUsuario
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_schema_cambio de CambioUsuario
     *
     * @return integer iid_schema_cambio
     */
    function getId_schema_cambio()
    {
        if (!isset($this->iid_schema_cambio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_schema_cambio;
    }

    /**
     * Establece el valor del atributo iid_schema_cambio de CambioUsuario
     *
     * @param integer iid_schema_cambio='' optional
     */
    function setId_schema_cambio($iid_schema_cambio = '')
    {
        $this->iid_schema_cambio = $iid_schema_cambio;
    }

    /**
     * Recupera el atributo iid_item_cambio de CambioUsuario
     *
     * @return integer iid_item_cambio
     */
    function getId_item_cambio()
    {
        if (!isset($this->iid_item_cambio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item_cambio;
    }

    /**
     * Establece el valor del atributo iid_item_cambio de CambioUsuario
     *
     * @param integer iid_item_cambio='' optional
     */
    function setId_item_cambio($iid_item_cambio = '')
    {
        $this->iid_item_cambio = $iid_item_cambio;
    }

    /**
     * Recupera el atributo iid_usuario de CambioUsuario
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
     * Establece el valor del atributo iid_usuario de CambioUsuario
     *
     * @param integer iid_usuario='' optional
     */
    function setId_usuario($iid_usuario = '')
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo isfsv de CambioUsuario
     *
     * @return integer isfsv
     */
    function getSfsv()
    {
        if (!isset($this->isfsv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->isfsv;
    }

    /**
     * Establece el valor del atributo isfsv de CambioUsuario
     *
     * @param integer isfsv='' optional
     */
    function setSfsv($isfsv = '')
    {
        $this->isfsv = $isfsv;
    }

    /**
     * Recupera el atributo iaviso_tipo de CambioUsuario
     *
     * @return integer iaviso_tipo
     */
    function getAviso_tipo()
    {
        if (!isset($this->iaviso_tipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iaviso_tipo;
    }

    /**
     * Establece el valor del atributo iaviso_tipo de CambioUsuario
     *
     * @param integer iaviso_tipo='' optional
     */
    function setAviso_tipo($iaviso_tipo = '')
    {
        $this->iaviso_tipo = $iaviso_tipo;
    }

    /**
     * Recupera el atributo bavisado de CambioUsuario
     *
     * @return boolean bavisado
     */
    function getAvisado()
    {
        if (!isset($this->bavisado) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bavisado;
    }

    /**
     * Establece el valor del atributo bavisado de CambioUsuario
     *
     * @param boolean bavisado='f' optional
     */
    function setAvisado($bavisado = 'f')
    {
        $this->bavisado = $bavisado;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oCambioUsuarioSet = new Set();

        $oCambioUsuarioSet->add($this->getDatosId_item_cambio());
        $oCambioUsuarioSet->add($this->getDatosId_schema_cambio());
        $oCambioUsuarioSet->add($this->getDatosId_usuario());
        $oCambioUsuarioSet->add($this->getDatosSfsv());
        $oCambioUsuarioSet->add($this->getDatosAviso_tipo());
        $oCambioUsuarioSet->add($this->getDatosAvisado());
        return $oCambioUsuarioSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema_cambio de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_schema_cambio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema_cambio'));
        $oDatosCampo->setEtiqueta(_("id_schema_cambio"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_item_cambio de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_item_cambio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_item_cambio'));
        $oDatosCampo->setEtiqueta(_("id_item_cambio"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_usuario de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_usuario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_usuario'));
        $oDatosCampo->setEtiqueta(_("id_usuario"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut isfsv de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSfsv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sfsv'));
        $oDatosCampo->setEtiqueta(_("sfsv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iaviso_tipo de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosAviso_tipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'aviso_tipo'));
        $oDatosCampo->setEtiqueta(_("aviso_tipo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bavisado de CambioUsuario
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosAvisado()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'avisado'));
        $oDatosCampo->setEtiqueta(_("avisado"));
        return $oDatosCampo;
    }
}
