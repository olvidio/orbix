<?php

namespace notas\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Acta extends core\ClasePropiedades
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Acta
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Acta
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
     * Acta de Acta
     *
     * @var string
     */
    protected $sacta;
    /**
     * Id_asignatura de Acta
     *
     * @var integer
     */
    protected $iid_asignatura;
    /**
     * Id_activ de Acta
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * F_acta de Acta
     *
     * @var web\DateTimeLocal
     */
    protected $df_acta;
    /**
     * Libro de Acta
     *
     * @var integer
     */
    protected $ilibro;
    /**
     * Pagina de Acta
     *
     * @var integer
     */
    protected $ipagina;
    /**
     * Linea de Acta
     *
     * @var integer
     */
    protected $ilinea;
    /**
     * Lugar de Acta
     *
     * @var string
     */
    protected $slugar;
    /**
     * Observ de Acta
     *
     * @var string
     */
    protected $sobserv;
    /**
     * pdf de Acta
     *
     * @var string bytea
     */
    protected $pdf;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Acta
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Acta
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
     * @param integer|array sacta
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'acta') && $val_id !== '') $this->sacta = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->sacta = (string)$a_id;
                $this->aPrimary_key = array('acta' => $this->sacta);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas');
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
        $aDades['id_asignatura'] = $this->iid_asignatura;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['f_acta'] = $this->df_acta;
        $aDades['libro'] = $this->ilibro;
        $aDades['pagina'] = $this->ipagina;
        $aDades['linea'] = $this->ilinea;
        $aDades['lugar'] = $this->slugar;
        $aDades['observ'] = $this->sobserv;
        $aDades['pdf'] = $this->pdf;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					f_acta                   = :f_acta,
					libro                    = :libro,
					pagina                   = :pagina,
					linea                    = :linea,
					lugar                    = :lugar,
					observ                   = :observ,
					pdf                    	 = :pdf";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE acta='$this->sacta'")) === false) {
                $sClauError = 'Acta.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                $id_asignatura = $aDades['id_asignatura'];
                $id_activ = $aDades['id_activ'];
                $f_acta = $aDades['f_acta'];
                $libro = $aDades['libro'];
                $pagina = $aDades['pagina'];
                $linea = $aDades['linea'];
                $lugar = $aDades['lugar'];
                $observ = $aDades['observ'];
                $pdf = $aDades['pdf'];

                $oDblSt->bindParam(1, $id_asignatura, \PDO::PARAM_INT);
                $oDblSt->bindParam(2, $id_activ, \PDO::PARAM_INT);
                $oDblSt->bindParam(3, $f_acta, \PDO::PARAM_STR);
                $oDblSt->bindParam(4, $libro, \PDO::PARAM_INT);
                $oDblSt->bindParam(5, $pagina, \PDO::PARAM_INT);
                $oDblSt->bindParam(6, $linea, \PDO::PARAM_INT);
                $oDblSt->bindParam(7, $lugar, \PDO::PARAM_STR);
                $oDblSt->bindParam(8, $observ, \PDO::PARAM_STR);
                $oDblSt->bindParam(9, $pdf, \PDO::PARAM_STR);
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Acta.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->sacta);
            $aDades['acta'] = $this->sacta;
            $campos = "(acta,id_asignatura,id_activ,f_acta,libro,pagina,linea,lugar,observ,pdf)";
            $valores = "(:acta,:id_asignatura,:id_activ,:f_acta,:libro,:pagina,:linea,:lugar,:observ,:pdf)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Acta.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                $acta = $aDades['acta'];
                $id_asignatura = $aDades['id_asignatura'];
                $id_activ = $aDades['id_activ'];
                $f_acta = $aDades['f_acta'];
                $libro = $aDades['libro'];
                $pagina = $aDades['pagina'];
                $linea = $aDades['linea'];
                $lugar = $aDades['lugar'];
                $observ = $aDades['observ'];
                $pdf = $aDades['pdf'];

                $oDblSt->bindParam(1, $acta, \PDO::PARAM_STR);
                $oDblSt->bindParam(2, $id_asignatura, \PDO::PARAM_INT);
                $oDblSt->bindParam(3, $id_activ, \PDO::PARAM_INT);
                $oDblSt->bindParam(4, $f_acta, \PDO::PARAM_STR);
                $oDblSt->bindParam(5, $libro, \PDO::PARAM_INT);
                $oDblSt->bindParam(6, $pagina, \PDO::PARAM_INT);
                $oDblSt->bindParam(7, $linea, \PDO::PARAM_INT);
                $oDblSt->bindParam(8, $lugar, \PDO::PARAM_STR);
                $oDblSt->bindParam(9, $observ, \PDO::PARAM_STR);
                $oDblSt->bindParam(10, $pdf, \PDO::PARAM_STR);
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Acta.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
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
        $id_asignatura = '';
        $id_activ = '';
        $f_acta = '';
        $libro = '';
        $pagina = '';
        $linea = '';
        $lugar = '';
        $observ = '';
        $pdf = '';

        if (isset($this->sacta)) {
            if (($oDblSt = $oDbl->query("SELECT id_asignatura, id_activ, f_acta, libro, pagina, linea, lugar, observ, pdf
							 FROM $nom_tabla WHERE acta='$this->sacta'")) === false) {
                $sClauError = 'Acta.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $oDblSt->execute();
            $oDblSt->bindColumn(1, $id_asignatura, \PDO::PARAM_INT);
            $oDblSt->bindColumn(2, $id_activ, \PDO::PARAM_INT);
            $oDblSt->bindColumn(3, $f_acta, \PDO::PARAM_STR);
            $oDblSt->bindColumn(4, $libro, \PDO::PARAM_INT);
            $oDblSt->bindColumn(5, $pagina, \PDO::PARAM_INT);
            $oDblSt->bindColumn(6, $linea, \PDO::PARAM_INT);
            $oDblSt->bindColumn(7, $lugar, \PDO::PARAM_STR);
            $oDblSt->bindColumn(8, $observ, \PDO::PARAM_STR);
            $oDblSt->bindColumn(9, $pdf, \PDO::PARAM_STR);
            $oDblSt->fetch(\PDO::FETCH_BOUND);

            $aDades = [
                'id_asignatura' => $id_asignatura,
                'id_activ' => $id_activ,
                'f_acta' => $f_acta,
                'libro' => $libro,
                'pagina' => $pagina,
                'linea' => $linea,
                'lugar' => $lugar,
                'observ' => $observ,
                'pdf' => $pdf,
            ];

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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE acta='$this->sacta'")) === false) {
            $sClauError = 'Acta.eliminar';
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
    function setAllAtributes($aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        // la fecha debe estar antes del acta por si hay que usar la función inventarActa.
        if (array_key_exists('f_acta', $aDades)) $this->setF_acta($aDades['f_acta'], $convert);
        if (array_key_exists('acta', $aDades)) $this->setActa($aDades['acta']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('libro', $aDades)) $this->setLibro($aDades['libro']);
        if (array_key_exists('pagina', $aDades)) $this->setPagina($aDades['pagina']);
        if (array_key_exists('linea', $aDades)) $this->setLinea($aDades['linea']);
        if (array_key_exists('lugar', $aDades)) $this->setLugar($aDades['lugar']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('pdf', $aDades)) $this->setPdfEscaped($aDades['pdf']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        // la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
        $this->setF_acta('');
        $this->setActa('');
        $this->setId_asignatura('');
        $this->setId_activ('');
        $this->setLibro('');
        $this->setPagina('');
        $this->setLinea('');
        $this->setLugar('');
        $this->setObserv('');
        $this->setPdfEscaped('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Acta en un array
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
     * Recupera la clave primaria de Acta en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('acta' => $this->sacta);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Acta en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'acta') && $val_id !== '') $this->sacta = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo sacta de Acta
     *
     * @return string sacta
     */
    function getActa()
    {
        if (!isset($this->sacta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sacta;
    }

    /**
     * inventa el valor del acta, si no es correcto
     *
     */
    public static function inventarActa(string $valor, web\DateTimeLocal|string $fecha): string
    {
        $valor = trim($valor);
        // comprobar si hace falta, o ya está bien el acta como está
        $reg_exp = "/^(\?|\w+\??)\s+([0-9]{0,3})\/([0-9]{2})\??$/";
        if (!preg_match($reg_exp, $valor)) {
            // inventar acta.
            // Se puede usar la función desde personaNota, por eso se puede pasar la fecha.
            if (empty($fecha)) {
                $any = '?';
                $num_acta = 'x';
            } else {
                if (is_object($fecha)) {
                    $oData = $fecha;
                } else {
                    $oData = web\DateTimeLocal::createFromLocal($fecha);
                }
                $any = $oData->format('y');
                // inventar acta.
                $oGesActas = new GestorActa();
                $num_acta = 1 + $oGesActas->getUltimaActa($any, $valor);
            }
            // no sé nada
            if ($valor === '?') {
                // 'dl? xx/15?';
                $valor = "dl? $num_acta/$any?";
            } else {  // solo la región o dl
                // 'region xx/15?';
                $valor = "$valor $num_acta/$any?";
            }
        }
        return $valor;
    }

    /**
     * Establece el valor del atributo sacta de Acta
     *
     * @param string sacta
     */
    function setActa($sacta)
    {
        $this->sacta = $sacta;
    }

    /**
     * Recupera el atributo iid_asignatura de Acta
     *
     * @return integer iid_asignatura
     */
    function getId_asignatura()
    {
        if (!isset($this->iid_asignatura) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_asignatura;
    }

    /**
     * Establece el valor del atributo iid_asignatura de Acta
     *
     * @param integer iid_asignatura='' optional
     */
    function setId_asignatura($iid_asignatura = '')
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     * Recupera el atributo iid_activ de Acta
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de Acta
     *
     * @param integer iid_activ='' optional
     */
    function setId_activ($iid_activ = '')
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo df_acta de Acta
     *
     * @return web\DateTimeLocal df_acta
     */
    function getF_acta()
    {
        if (!isset($this->df_acta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_acta)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\ConverterDate('date', $this->df_acta);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_acta de Acta
     * Si df_acta es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_acta debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_acta='' optional.
     * @param boolean convert=true optional. Si es false, df_acta debe ser un string en formato ISO (Y-m-d).
     */
    function setF_acta($df_acta = '', $convert = true)
    {
        if ($convert === true && !empty($df_acta)) {
            $oConverter = new core\ConverterDate('date', $df_acta);
            $this->df_acta = $oConverter->toPg();
        } else {
            $this->df_acta = $df_acta;
        }
    }

    /**
     * Recupera el atributo ilibro de Acta
     *
     * @return integer ilibro
     */
    function getLibro()
    {
        if (!isset($this->ilibro) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ilibro;
    }

    /**
     * Establece el valor del atributo ilibro de Acta
     *
     * @param integer ilibro='' optional
     */
    function setLibro($ilibro = '')
    {
        $this->ilibro = $ilibro;
    }

    /**
     * Recupera el atributo ipagina de Acta
     *
     * @return integer ipagina
     */
    function getPagina()
    {
        if (!isset($this->ipagina) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ipagina;
    }

    /**
     * Establece el valor del atributo ipagina de Acta
     *
     * @param integer ipagina='' optional
     */
    function setPagina($ipagina = '')
    {
        $this->ipagina = $ipagina;
    }

    /**
     * Recupera el atributo ilinea de Acta
     *
     * @return integer ilinea
     */
    function getLinea()
    {
        if (!isset($this->ilinea) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ilinea;
    }

    /**
     * Establece el valor del atributo ilinea de Acta
     *
     * @param integer ilinea='' optional
     */
    function setLinea($ilinea = '')
    {
        $this->ilinea = $ilinea;
    }

    /**
     * Recupera el atributo slugar de Acta
     *
     * @return string slugar
     */
    function getLugar()
    {
        if (!isset($this->slugar) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->slugar;
    }

    /**
     * Establece el valor del atributo slugar de Acta
     *
     * @param string slugar='' optional
     */
    function setLugar($slugar = '')
    {
        $this->slugar = $slugar;
    }

    /**
     * Recupera el atributo sobserv de Acta
     *
     * @return string sobserv
     */
    function getObserv()
    {
        if (!isset($this->sobserv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv;
    }

    /**
     * Establece el valor del atributo sobserv de Acta
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Establece el valor del atributo pdf de Acta
     *
     * @param string pdf='' optional
     */
    function setPdf($pdf = '')
    {
        // Escape the binary data
        $escaped = bin2hex($pdf);
        $this->pdf = $escaped;
    }

    /**
     * Establece el valor del atributo pdf de Acta
     * per usar amb els valors directes de la DB.
     *
     * @param string pdf='' optional (ja convertit a hexadecimal)
     */
    private function setPdfEscaped($pdf = '')
    {
        $this->pdf = $pdf;
    }

    public function getpdf()
    {
        if (!isset($this->pdf) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return hex2bin($this->pdf ?? '');
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActaSet = new core\Set();

        $oActaSet->add($this->getDatosActa());
        $oActaSet->add($this->getDatosId_asignatura());
        $oActaSet->add($this->getDatosId_activ());
        $oActaSet->add($this->getDatosF_acta());
        $oActaSet->add($this->getDatosLibro());
        $oActaSet->add($this->getDatosPagina());
        $oActaSet->add($this->getDatosLinea());
        $oActaSet->add($this->getDatosLugar());
        $oActaSet->add($this->getDatosObserv());
        return $oActaSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sacta de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosActa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'acta'));
        $oDatosCampo->setEtiqueta(_("acta"));
        // Las actcas de otras r sólo tienen la sigla de la r
        $oDatosCampo->setRegExp("/^(\?|\w{1,8}\??)(\s+([0-9]{0,3})\/([0-9]{2})\??)?$/");
        $txt = "No tienen el formato: 'dlxx nn/aa'. Debe tener sólo un espacio.";
        $txt .= "\nSi sólo se sabe la region/dl poner la sigla.\nSi no se sabe nada poner ?.\n";
        $oDatosCampo->setRegExpText(_($txt));
        return $oDatosCampo;
    }


    /**
     * Recupera les propietats de l'atribut iid_asignatura de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_asignatura()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_asignatura'));
        $oDatosCampo->setEtiqueta(_("id_asignatura"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_activ de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_activ'));
        $oDatosCampo->setEtiqueta(_("id_activ"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_acta de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_acta()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_acta'));
        $oDatosCampo->setEtiqueta(_("fecha acta"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ilibro de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosLibro()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'libro'));
        $oDatosCampo->setEtiqueta(_("libro"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ipagina de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPagina()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'pagina'));
        $oDatosCampo->setEtiqueta(_("página"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ilinea de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosLinea()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'linea'));
        $oDatosCampo->setEtiqueta(_("línea"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut slugar de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosLugar()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'lugar'));
        $oDatosCampo->setEtiqueta(_("lugar"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Acta
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        return $oDatosCampo;
    }
}