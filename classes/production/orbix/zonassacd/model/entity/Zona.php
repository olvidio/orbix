<?php

namespace zonassacd\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula zonas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */

/**
 * Clase que implementa la entidad zonas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class Zona extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Zona
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Zona
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
     * Id_zona de Zona
     *
     * @var integer
     */
    private $iid_zona;
    /**
     * Nombre_zona de Zona
     *
     * @var string
     */
    private $snombre_zona;
    /**
     * Orden de Zona
     *
     * @var integer
     */
    private $iorden;
    /**
     * Id_grupo de Zona
     *
     * @var integer
     */
    private $iid_grupo;
    /**
     * Id_nom de Zona
     *
     * @var integer
     */
    private $iid_nom;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Zona
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Zona
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
     * @param integer|array iid_zona
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_zona') && $val_id !== '') $this->iid_zona = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_zona = (integer)$a_id;
                $this->aPrimary_key = array('id_zona' => $this->iid_zona);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('zonas');
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
        $aDades = [];
        $aDades['nombre_zona'] = $this->snombre_zona;
        $aDades['orden'] = $this->iorden;
        $aDades['id_grupo'] = $this->iid_grupo;
        $aDades['id_nom'] = $this->iid_nom;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					nombre_zona              = :nombre_zona,
					orden                    = :orden,
					id_grupo                 = :id_grupo,
					id_nom                   = :id_nom";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_zona='$this->iid_zona'")) === FALSE) {
                $sClauError = 'Zona.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Zona.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(nombre_zona,orden,id_grupo,id_nom)";
            $valores = "(:nombre_zona,:orden,:id_grupo,:id_nom)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'Zona.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Zona.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_zona = $oDbl->lastInsertId('zonas_id_zona_seq');
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_zona)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_zona='$this->iid_zona'")) === FALSE) {
                $sClauError = 'Zona.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_zona='$this->iid_zona'")) === FALSE) {
            $sClauError = 'Zona.eliminar';
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
        if (array_key_exists('id_zona', $aDades)) $this->setId_zona($aDades['id_zona']);
        if (array_key_exists('nombre_zona', $aDades)) $this->setNombre_zona($aDades['nombre_zona']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
        if (array_key_exists('id_grupo', $aDades)) $this->setId_grupo($aDades['id_grupo']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_zona('');
        $this->setNombre_zona('');
        $this->setOrden('');
        $this->setId_grupo('');
        $this->setId_nom('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Zona en un array
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
     * Recupera la clave primaria de Zona en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_zona' => $this->iid_zona);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Zona en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_zona') && $val_id !== '') $this->iid_zona = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_zona de Zona
     *
     * @return integer iid_zona
     */
    function getId_zona()
    {
        if (!isset($this->iid_zona) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_zona;
    }

    /**
     * Establece el valor del atributo iid_zona de Zona
     *
     * @param integer iid_zona
     */
    function setId_zona($iid_zona)
    {
        $this->iid_zona = $iid_zona;
    }

    /**
     * Recupera el atributo snombre_zona de Zona
     *
     * @return string snombre_zona
     */
    function getNombre_zona()
    {
        if (!isset($this->snombre_zona) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_zona;
    }

    /**
     * Establece el valor del atributo snombre_zona de Zona
     *
     * @param string snombre_zona='' optional
     */
    function setNombre_zona($snombre_zona = '')
    {
        $this->snombre_zona = $snombre_zona;
    }

    /**
     * Recupera el atributo iorden de Zona
     *
     * @return integer iorden
     */
    function getOrden()
    {
        if (!isset($this->iorden) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden;
    }

    /**
     * Establece el valor del atributo iorden de Zona
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }

    /**
     * Recupera el atributo iid_grupo de Zona
     *
     * @return integer iid_grupo
     */
    function getId_grupo()
    {
        if (!isset($this->iid_grupo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_grupo;
    }

    /**
     * Establece el valor del atributo iid_grupo de Zona
     *
     * @param integer iid_grupo='' optional
     */
    function setId_grupo($iid_grupo = '')
    {
        $this->iid_grupo = $iid_grupo;
    }

    /**
     * Recupera el atributo iid_nom de Zona
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        if (!isset($this->iid_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de Zona
     *
     * @param integer iid_nom='' optional
     */
    function setId_nom($iid_nom = '')
    {
        $this->iid_nom = $iid_nom;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oZonaSet = new Set();

        $oZonaSet->add($this->getDatosNombre_zona());
        $oZonaSet->add($this->getDatosOrden());
        $oZonaSet->add($this->getDatosId_grupo());
        $oZonaSet->add($this->getDatosId_nom());
        return $oZonaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snombre_zona de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_zona()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_zona'));
        $oDatosCampo->setEtiqueta(_("nombre zona"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('5');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_grupo de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_grupo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_grupo'));
        $oDatosCampo->setEtiqueta(_("grupo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('zonassacd\model\entity\ZonaGrupo'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre_grupo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaZonaGrupos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nom de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nom'));
        $oDatosCampo->setEtiqueta(_("jefe zona"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('personas\model\entity\PersonaDl'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getPrefApellidosNombre'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaSacd'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }
}
