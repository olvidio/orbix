<?php
namespace ubis\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad d_teleco_ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
abstract class TelecoUbiGlobal extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TelecoUbi
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de TelecoUbi
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
     * Id_ubi de TelecoUbi
     *
     * @var integer
     */
    protected $iid_ubi;
    /**
     * Id_item de TelecoUbi
     *
     * @var integer
     */
    protected $iid_item;
    /**
     * Tipo_teleco de TelecoUbi
     *
     * @var string
     */
    protected $stipo_teleco;
    /**
     * Desc_teleco de TelecoUbi
     *
     * @var string
     */
    protected $sdesc_teleco;
    /**
     * Num_teleco de TelecoUbi
     *
     * @var string
     */
    protected $snum_teleco;
    /**
     * Observ de TelecoUbi
     *
     * @var string
     */
    protected $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe vuit.
     *
     */
    function __construct($a_id = '')
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de TelecoUbi en un array
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
     * Recupera la clave primaria de TelecoUbi en un array
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
     * Establece la clave primaria de TelecoUbi en un array
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
     * Recupera el atributo iid_ubi de TelecoUbi
     *
     * @return integer iid_ubi
     */
    function getId_ubi()
    {
        if (!isset($this->iid_ubi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi;
    }

    /**
     * Establece el valor del atributo iid_ubi de TelecoUbi
     *
     * @param integer iid_ubi
     */
    function setId_ubi($iid_ubi)
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     * Recupera el atributo iid_item de TelecoUbi
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
     * Establece el valor del atributo iid_item de TelecoUbi
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo stipo_teleco de TelecoUbi
     *
     * @return string stipo_teleco
     */
    function getTipo_teleco()
    {
        if (!isset($this->stipo_teleco) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_teleco;
    }

    /**
     * Establece el valor del atributo stipo_teleco de TelecoUbi
     *
     * @param string stipo_teleco='' optional
     */
    function setTipo_teleco($stipo_teleco = '')
    {
        $this->stipo_teleco = $stipo_teleco;
    }

    /**
     * Recupera el atributo sdesc_teleco de TelecoUbi
     *
     * @return string sdesc_teleco
     */
    function getDesc_teleco()
    {
        if (!isset($this->sdesc_teleco) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_teleco;
    }

    /**
     * Establece el valor del atributo sdesc_teleco de TelecoUbi
     *
     * @param string sdesc_teleco='' optional
     */
    function setDesc_teleco($sdesc_teleco = '')
    {
        $this->sdesc_teleco = $sdesc_teleco;
    }

    /**
     * Recupera el atributo snum_teleco de TelecoUbi
     *
     * @return string snum_teleco
     */
    function getNum_teleco()
    {
        if (!isset($this->snum_teleco) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snum_teleco;
    }

    /**
     * Establece el valor del atributo snum_teleco de TelecoUbi
     *
     * @param string snum_teleco='' optional
     */
    function setNum_teleco($snum_teleco = '')
    {
        $this->snum_teleco = $snum_teleco;
    }

    /**
     * Recupera el atributo sobserv de TelecoUbi
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
     * Establece el valor del atributo sobserv de TelecoUbi
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTelecoUbiSet = new Set();

        $oTelecoUbiSet->add($this->getDatosTipo_teleco());
        $oTelecoUbiSet->add($this->getDatosDesc_teleco());
        $oTelecoUbiSet->add($this->getDatosNum_teleco());
        $oTelecoUbiSet->add($this->getDatosObserv());
        return $oTelecoUbiSet->getTot();
    }

    function getDatosTipo_teleco()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_teleco'));
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('ubis\model\entity\TipoTeleco'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre_teleco'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposTelecoUbi'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        $oDatosCampo->setAccion('desc_teleco'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    function getDatosDesc_teleco()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_teleco'));
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument('ubis\model\entity\DescTeleco');
        $oDatosCampo->setArgument2('getDesc_teleco'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayDescTelecoUbis');
        $oDatosCampo->setDepende('tipo_teleco');
        return $oDatosCampo;
    }

    function getDatosNum_teleco()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_teleco'));
        $oDatosCampo->setEtiqueta(_("número o siglas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

}

?>
