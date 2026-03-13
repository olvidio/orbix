<?php

namespace src\ubiscamas\domain;

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\model\ViewNewPhtml;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use web\Hash;
use web\Lista;
use function core\is_true_txt;

/**
 * Gestiona el dossier 3102: Cargos de una actividad
 *
 * En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
 * abajo se añaden los botones para añadir una nueva persona-cargo.
 *
 * @package    orbix
 * @subpackage    actividadcargos
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select2006
{
    // --------- Variables internas de la clase.
    /**
     * array con los permisos (si o no) para añadir las personas (agd, n...)
     * según el tipo de actividad de que se trate y quién seamos nosotros.
     * @var array $a_ref_perm
     */
    private $a_ref_perm;
    /* @var $mwg_err string */
    private $msg_err;
    /* @var $a_valores array */
    private $a_valores;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private $bloque;

    // ---------- Variables requeridas
    private string $queSel;
    /* @var $id_dossier integer */
    private $id_dossier;
    /* @var $pau string */
    private $pau;
    /* @var $obj_pau string */
    private $obj_pau;
    /* @var $id_pau integer */
    private $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer permiso
     */
    private $permiso;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;
    /**
     * @var array|mixed
     */
    private mixed $aLinks_dl;


    private function getBotones()
    {
        $a_botones = array(
            array('txt' => _("modificar habitación"), 'click' => "fnjs_mod_habitacion(this.form)"),
            array('txt' => _("borrar habitación"), 'click' => "fnjs_borrar_habitacion(this.form)")
        );
        return $a_botones;
    }

    private function getCabeceras()
    {
        $a_cabeceras = [
            _("nombre"),
            _("planta"),
            _("adaptada"),
            _("sillon"),
            _("fumador"),
            _("despacho"),
            _("tipoLavabo"),
        ];
        return $a_cabeceras;
    }

    private function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla()
    {
        $HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);

        $c = 0;
        $a_valores = [];
        $cHabitaciones = $HabitacionRepository->getHabitaciones(['id_ubi' => $this->id_pau]);
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        foreach ($cHabitaciones as $oHabitacion) {
            $c++;
            $id_habitacion = $oHabitacion->getId_habitacion();
            $id_ubi = $oHabitacion->getId_ubi();
            $orden = $oHabitacion->getOrden();
            $nombre = $oHabitacion->getNombre();
            $numero_camas = $oHabitacion->getNumero_camas();
            $numero_camas_vip = $oHabitacion->getNumero_camas_vip();
            $planta = $oHabitacion->getPlanta();
            $sillon = $oHabitacion->isSillon();
            $adaptada = $oHabitacion->isAdaptada();
            $fumador = $oHabitacion->isFumador();
            $tipoLavabo = $oHabitacion->gettipoLavabo();
            $despacho = $oHabitacion->isDespacho();

            $sillon_txt = is_true_txt($sillon);
            $adaptada_txt = is_true_txt($adaptada);
            $fumador_txt = is_true_txt($fumador);
            $despacho_txt = is_true_txt($despacho);

            $tipoLavabo_txt = TipoLavabo::getArrayTipoLavabo()[$tipoLavabo];

            $a_valores[$c]['sel'] = "$id_habitacion#$id_ubi#$orden";
            $a_valores[$c][1] = $nombre;
            $a_valores[$c][2] = $planta;
            $a_valores[$c][3] = $adaptada_txt;
            $a_valores[$c][4] = $sillon_txt;
            $a_valores[$c][5] = $fumador_txt;
            $a_valores[$c][6] = $despacho_txt;
            $a_valores[$c][7] = $tipoLavabo_txt;
        }

        $this->a_valores = $a_valores;
        if (!empty($this->msg_err)) {
            echo $this->msg_err;
        }
    }

    public function getHtml()
    {
        $oHashSelect = new Hash();
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 3,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new Lista();
        $oTabla->setId_tabla('select3102');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $aQuery = ['nuevo' => 1, 'id_ubi' => $this->id_pau];
        $url_nuevo = Hash::link(ConfigGlobal::getWeb() . '/frontend/ubiscamas/controller/habitacion_form.php?' . http_build_query($aQuery));

        $a_campos = ['oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $this->aLinks_dl,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'url_nuevo' => $url_nuevo,
        ];

        $oView = new ViewNewPhtml('frontend\ubiscamas\view');
        $oView->renderizar('select2006.phtml', $a_campos);
    }

    private function setLinksInsert()
    {
        $this->aLinks_dl = [];
        $a_ref_perm = $this->a_ref_perm;
        if (empty($a_ref_perm) || ConfigGlobal::mi_ambito() === 'rstgr') { // si es nulo, no tengo permisos de ningún tipo
            return '';
        }
        reset($a_ref_perm);
        foreach ($a_ref_perm as $clave => $val) {
            $perm = $val["perm"];
            $obj_pau = $val["obj"];
            $nom = $val["nom"];
            if (!empty($perm)) {
                $aQuery = array('mod' => 'nuevo',
                    'pau' => $this->pau,
                    'obj_pau' => $obj_pau,
                    'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
                    'id_pau' => $this->id_pau);
                // el hppt_build_query no pasa los valores null
                if (is_array($aQuery)) {
                    array_walk($aQuery, 'core\poner_empty_on_null');
                }
                $pagina = Hash::link('frontend/ubiscamas/controller/habitacion_form.php?' . http_build_query($aQuery));
                $nom2 = sprintf(_("añadir %s"), $nom);
                $this->aLinks_dl[$nom2] = $pagina;
            }
        }
    }

    public function getId_dossier()
    {
        return $this->id_dossier;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getObj_pau()
    {
        return $this->obj_pau;
    }

    public function getId_pau()
    {
        return $this->id_pau;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }

    public function setId_dossier($Qid_dossier)
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau)
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau)
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau)
    {
        $this->id_pau = $Qid_pau;
    }

    public function setPermiso($Qpermiso)
    {
        $this->permiso = $Qpermiso;
    }

    public function setQid_sel($Qid_sel)
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id)
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel)
    {
        $this->queSel = $queSel;
    }


}
