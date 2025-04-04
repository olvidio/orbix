<?php
namespace core;

/**
 * DatosCampo
 *
 * Classe per a gestionar les dades referents a un camp de la Base de Dades
 *
 * @param array amb camp Nom del camp
 *                    tabla Nom de la Taula
 * @subpackage model
 * @author
 * @version 1.0
 * @created 22/9/2010
 *
 * @package delegación
 */
class DatosCampo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Nom_tabla de DatosCampo
     *
     * @var string
     */
    private $snom_tabla;
    /**
     * Nom_camp de DatosCampo
     *
     * @var string
     */
    private $snom_camp;
    private $metodoGet;
    private $metodoSet;
    /**
     * Etiqueta de DatosCampo
     *
     * @var string
     */
    private $setiqueta;
    /**
     * Aviso de DatosCampo:
     * para indicar si debe salir en las opciones de avisar si hay un cambio.
     *
     * @var boolean
     */
    private $baviso;
    /**
     * Tipo de DatosCampo
     *
     * @var string
     */
    private $stipo;
    /**
     * Argument de DatosCampo
     *
     * @var string
     */
    private $sargument;
    /**
     * Argument2 de DatosCampo
     *
     * @var string
     */
    private $sargument2;
    /**
     * Argument3 de DatosCampo
     *
     * @var string
     */
    private $sargument3;
    /**
     * Accion de DatosCampo
     *
     * @var string
     */
    private $saccion;
    /**
     * Depende de DatosCampo
     *
     * @var string
     */
    private $sdepende;
    /**
     * Lista de DatosCampo
     *
     * @var array
     */
    private $alista;
    /**
     * RegExp de DatosCampo
     *
     * @var string
     */
    private $sRegExp;
    /**
     * RegExpText de DatosCampo
     *
     * @var string
     */
    private $sRegExpText;


    /* CONSTRUCTOR -------------------------------------------------------------- */
    private array $aPrimary_key;

    /**
     * Constructor de la classe.
     *
     *
     */
    function __construct($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 's' . $nom_id;
                if ($val_id !== '') $this->$nom_id = $val_id;
            }
        }
    }
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/


    /**
     *
     * Esta función devuelve datos sobre el campo de una tabla
     *
     * $oDB es la conexión al Postgresql
     * $tabla es el nombre de la tabla
     * $camp es el nombre del campo
     * $que es el dato que queremos saber:
     *        "longitud"    longitud del campo
     *        "nulo"        si es permite nulo o no
     *        "tipo"        int, varchar, bool...
     *        "valor"        valor por defecto
     */
    function datos_campo($oDB, $que)
    {
        $tabla = $this->getNom_tabla();
        $camp = $this->getNom_camp();
        if ($tabla && $camp) {
            //tipo de campos
            $sql_get_fields = "
				SELECT 
					a.attrelid,
					a.attnum,
					a.attname AS field, 
					t.typname AS type, 
					a.attlen AS length,
					a.atttypmod AS lengthvar,
					a.attnotnull AS notnull,
					a.atthasdef
				FROM 
					pg_attribute a, 
					pg_type t
				WHERE 
					a.attnum > 0
					and a.attrelid = ('\"'|| current_schema() || '\"' || '.$tabla')::regclass
					and a.atttypid = t.oid
					and a.attname = '$camp'
				ORDER BY a.attnum
			";
            //echo "query: ($sql_get_fields)";
            $oDBSt_res_fields = $oDB->query($sql_get_fields);
            if ($oDBSt_res_fields === false) {
                $sClauError = 'DatosCampo.datos_campo';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDB, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $row = $oDBSt_res_fields->fetch(\PDO::FETCH_ASSOC);
            if ($row === FALSE) {
                $err_txt = sprintf(_("No está definido el campo %s en DatosCampo para la tabla %s"),$camp, $tabla);
                exit($err_txt);
            }
            if ($row['length'] > 0) {
                $llarg = $row['length'];
            } else if ($row['lengthvar'] > 0) {
                $llarg = $row['lengthvar'] - 4;
            } else {
                $llarg = "var";
            }
            if ($row['type'] == "date") {
                $llarg = 8;
            }
            $null = $row['notnull'];

            //respuesta a lo que se pide
            switch ($que) {
                case "longitud" :
                    return $llarg;
                    break;
                case "nulo":
                    return $null;
                    break;
                case "tipo":
                    return $row['type'];
                    break;
                case "valor":
                    $rowdefault = '';
                    if ($row['atthasdef'] == true) {
                        /*  valores por defecto
                        / creo  que las posibilidades son:
                            número
                            'txto'::character...
                            true, false
                            nextval(),idlocal()... -> funcion
                        */
                        $sql_get_default = "
							SELECT pg_get_expr(adbin, adrelid) as defvalor
							FROM pg_attrdef d
						   	WHERE d.adnum =" . $row['attnum'] . " AND 
							d.adrelid =" . $row['attrelid'];

                        $oDBSt_def_res = $oDB->query($sql_get_default);
                        if (!$oDBSt_def_res->rowCount()) {
                            $rowdefault = "";
                        } else {
                            $rowdefault = $oDBSt_def_res->fetchColumn();
                            $rta = preg_match_all("/^'([\w]+)'::(.*)/", $rowdefault, $matches, PREG_SET_ORDER);
                            /*
                            foreach ($matches as $val) {
                                echo "matched: " . $val[0] . "\n";
                                echo "part 1: " . $val[1] . "\n";
                                echo "part 2: " . $val[3] . "\n";
                                echo "part 3: " . $val[4] . "\n\n";
                            }
                            */
                            if (!empty($rta)) {
                                $rowdefault = $matches[0][1];
                            } elseif (strstr($rowdefault, '(')) {
                                $rowdefault = "function";
                            }
                        }
                        //echo "$camp ::: $rta; default: $rowdefault<br>";
                        return $rowdefault;
                    } else {
                        return '';
                    }
                    break;
            }
        }
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/


    /**
     * Recupera el atributo snom_tabla de DatosCampo
     *
     * @return string snom_tabla
     */
    function getNom_tabla()
    {
        if (!isset($this->snom_tabla)) {
            //$this->DBCarregar();
        }
        return $this->snom_tabla;
    }

    /**
     * Establece el valor del atributo snom_tabla de DatosCampo
     *
     * @param string snom_tabla
     */
    function setNom_tabla($snom_tabla)
    {
        $this->snom_tabla = $snom_tabla;
    }

    /**
     * Recupera el atributo snom_camp de DatosCampo
     *
     * @return string snom_camp
     */
    function getNom_camp()
    {
        if (!isset($this->snom_camp)) {
            //$this->DBCarregar();
        }
        return $this->snom_camp;
    }

    /**
     * Establece el valor del atributo snom_camp de DatosCampo
     *
     * @param string snom_camp
     */
    function setNom_camp($snom_camp)
    {
        $this->snom_camp = $snom_camp;
    }

    /**
     * Recupera el atributo setiqueta de DatosCampo
     *
     * @return string setiqueta
     */
    function getEtiqueta()
    {
        if (!isset($this->setiqueta)) {
            //$this->DBCarregar();
        }
        return $this->setiqueta;
    }

    /**
     * Establece el valor del atributo setiqueta de DatosCampo
     *
     * @param string setiqueta
     */
    function setEtiqueta($setiqueta)
    {
        $this->setiqueta = $setiqueta;
    }

    /**
     * Recupera el atributo baviso de DatosCampo
     *
     * @return boolean baviso
     */
    function getAviso()
    {
        if (!isset($this->baviso)) {
            $this->baviso = TRUE;
        }
        return $this->baviso;
    }

    /**
     * Establece el valor del atributo baviso de DatosCampo
     *
     * @param boolean baviso
     */
    function setAviso($baviso)
    {
        $this->baviso = $baviso;
    }

    /**
     * Recupera el atributo stipo de DatosCampo
     *
     * @return string stipo
     */
    function getTipo()
    {
        if (!isset($this->stipo)) {
            //$this->DBCarregar();
        }
        return $this->stipo;
    }

    /**
     * Establece el valor del atributo stipo de DatosCampo
     *
     * @param string stipo
     */
    function setTipo($stipo)
    {
        $this->stipo = $stipo;
    }

    /**
     * Recupera el atributo sargument de DatosCampo
     *
     * @return string sargument
     */
    function getArgument()
    {
        if (!isset($this->sargument)) {
            //$this->DBCarregar();
        }
        return $this->sargument;
    }

    /**
     * Establece el valor del atributo sargument de DatosCampo
     *
     * @param string sargument
     */
    function setArgument($sargument)
    {
        $this->sargument = $sargument;
    }

    /**
     * Recupera el atributo sargument2 de DatosCampo
     *
     * @return string sargument2
     */
    function getArgument2()
    {
        if (!isset($this->sargument2)) {
            //$this->DBCarregar();
        }
        return $this->sargument2;
    }

    /**
     * Establece el valor del atributo sargument2 de DatosCampo
     *
     * @param string sargument2
     */
    function setArgument2($sargument2)
    {
        $this->sargument2 = $sargument2;
    }

    /**
     * Recupera el atributo sargument3 de DatosCampo
     *
     * @return string sargument3
     */
    function getArgument3()
    {
        if (!isset($this->sargument3)) {
            //$this->DBCarregar();
        }
        return $this->sargument3;
    }

    /**
     * Establece el valor del atributo sargument3 de DatosCampo
     *
     * @param string sargument3
     */
    function setArgument3($sargument3)
    {
        $this->sargument3 = $sargument3;
    }

    /**
     * Recupera el atributo saccion de DatosCampo
     *
     * @return string saccion
     */
    function getAccion()
    {
        if (!isset($this->saccion)) {
            //$this->DBCarregar();
        }
        return $this->saccion;
    }

    /**
     * Establece el valor del atributo saccion de DatosCampo
     *
     * @param string saccion
     */
    function setAccion($saccion)
    {
        $this->saccion = $saccion;
    }

    /**
     * Recupera el atributo sdepende de DatosCampo
     *
     * @return string sdepende
     */
    function getDepende()
    {
        if (!isset($this->sdepende)) {
            //$this->DBCarregar();
        }
        return $this->sdepende;
    }

    /**
     * Establece el valor del atributo sdepende de DatosCampo
     *
     * @param string sdepende
     */
    function setDepende($sdepende)
    {
        $this->sdepende = $sdepende;
    }

    /**
     * Recupera el atributo alista de DatosCampo
     *
     * @return array alista
     */
    function getLista()
    {
        if (!isset($this->alista)) {
            //$this->DBCarregar();
        }
        return $this->alista;
    }

    /**
     * Establece el valor del atributo alista de DatosCampo
     *
     * @param array alista
     */
    function setLista($alista)
    {
        $this->alista = $alista;
    }

    /**
     * Recupera el atributo sRegExp de DatosCampo
     *
     * @return string sRegExp
     */
    function getRegExp()
    {
        if (!isset($this->sRegExp)) {
            //$this->DBCarregar();
        }
        return $this->sRegExp;
    }

    /**
     * Establece el valor del atributo sRegExp de DatosCampo
     *
     * @param string sRegExp
     */
    function setRegExp($sRegExp)
    {
        $this->sRegExp = $sRegExp;
    }

    /**
     * Recupera el atributo sRegExpText de DatosCampo
     *
     * @return string sRegExpText
     */
    function getRegExpText()
    {
        if (!isset($this->sRegExpText)) {
            //$this->DBCarregar();
        }
        return $this->sRegExpText;
    }

    /**
     * Establece el valor del atributo sRegExpText de DatosCampo
     *
     * @param string sRegExpText
     */
    function setRegExpText($sRegExpText)
    {
        $this->sRegExpText = $sRegExpText;
    }

    /**
     * @return mixed
     */
    public function getMetodoGet()
    {
        return $this->metodoGet;
    }

    /**
     * @param mixed $metodoGet
     */
    public function setMetodoGet($metodoGet): void
    {
        $this->metodoGet = $metodoGet;
    }

    /**
     * @return mixed
     */
    public function getMetodoSet()
    {
        return $this->metodoSet;
    }

    /**
     * @param mixed $metodoSet
     */
    public function setMetodoSet($metodoSet): void
    {
        $this->metodoSet = $metodoSet;
    }
}
