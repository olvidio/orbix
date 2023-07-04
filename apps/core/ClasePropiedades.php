<?php
namespace core;

abstract class ClasePropiedades
{
    /**
     * oDbl de ClasePropiedades
     *
     * @var object
     */
    protected $oDbl;
    protected $oDbl_Select;
    /**
     * NomTabla de ClasePropiedades
     *
     * @var string
     */
    protected $sNomTabla;
    /**
     * ErrorTxt de ClasePropiedades
     *
     * @var string
     */
    protected $sErrorTxt;
    /* MÉTODOS GET y SET --------------------------------------------------------*/


    /**
     * Recupera el atributo iid_schema
     *
     * @return integer iid_schema
     */
    function getId_schema()
    {
        if (!isset($this->iid_schema) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_schema;
    }

    /**
     * Establece el valor del atributo iid_schema
     *
     * @param integer iid_schema='' optional
     */
    function setId_schema($iid_schema = '')
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     * Recupera el atributo oDbl de ClasePropiedades
     *
     * @return object oDbl
     */
    public function getoDbl()
    {
        return $this->oDbl;
    }

    /** idem sólo lectura */
    public function getoDbl_Select()
    {
        return $this->oDbl_Select;
    }

    /**
     * Establece el valor del atributo oDbl de ClasePropiedades
     * El faig public per quan s'ha de copiar dades d'un esquema a un altre.
     *
     * @param object oDbl
     */
    public function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }
    public function setoDbl_Select($oDbl_Select)
    {
        $this->oDbl_Select = $oDbl_Select;
    }

    /**
     * Recupera el atributo sNomTabla de ClasePropiedades
     *
     * @return string sNomTabla
     */
    public function getNomTabla()
    {
        return $this->sNomTabla;
    }

    /**
     * Establece el valor del atributo sNomTabla de ClasePropiedades
     *
     * @param string sNomTabla
     */
    protected function setNomTabla($sNomTabla)
    {
        $this->sNomTabla = $sNomTabla;
    }


    public function __get($nombre)
    {
        $metodo = 'get' . ucfirst($nombre);
        if (method_exists($this, $metodo)) return $this->$metodo();
    }

    public function __set($nombre, $valor)
    {
        $metodo = 'set' . ucfirst($nombre);
        if (method_exists($this, $metodo)) $this->$metodo($valor);
    }

    /**
     * sErrorTxt
     * @return string
     */
    public function getErrorTxt()
    {
        return $this->sErrorTxt;
    }

    /**
     * sErrorTxt
     * @param string $sErrorTxt
     * @return ClasePropiedades
     */
    public function setErrorTxt($sErrorTxt)
    {
        $this->sErrorTxt = $sErrorTxt;
        return $this;
    }

}

?>
